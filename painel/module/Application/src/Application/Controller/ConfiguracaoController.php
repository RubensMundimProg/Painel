<?php

namespace Application\Controller;

use Application\Form\UltimaMilha;
use Dashboard\Form\AvaliacaoPedagogica;
use Estrutura\Controller\AbstractEstruturaController;
use Modulo\Service\ApiSession;
use Modulo\Service\RiskManager;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class ConfiguracaoController extends AbstractEstruturaController {
    
    public function cadastroAction(){

        $urlArquivo = \Estrutura\Service\Config::getConfig('local_arquivo_configuracao');

        try {

                $form = new \Application\Form\Configuracao();
                $container = new \Zend\Session\Container('UsuarioApi');
                $post = [];
                $request = $this->getRequest();
                $post = $request->getPost();

                if($request->isPost()) {

                    $form->setData($post);

                    if (!$form->isValid()) {
                        $this->addValidateMessages($form);
                        return $this->setRedirect('/configuracao/cadastro', $post);
                    }

                    $dados = $form->getData();

                    // Criamos o arquivo do usuário com w+
                    $cria = fopen($urlArquivo. "\\configuracao.txt", "w+");

                    $dados = json_encode($dados);

                    // Agora escrevemos estes dados no arquivo
                    $escreve = fwrite($cria,$dados);

                    // Fechando o arquivo
                    fclose($cria);

                    $this->addSuccessMessage('Periodo configurado com Sucesso');
                    return $this->setRedirect('/');

                }

            } catch (\Exception $e) {
                $this->addErrorMessage($e->getMessage());
                return $this->setRedirect('/configuracao/cadastro', $post);
        }




        $file_lines = file_get_contents($urlArquivo."\\configuracao.txt");
        $dados = json_decode($file_lines, true);

        if(!empty($dados)){
            $form->setData($dados);
        };


        return new ViewModel(['form' => $form]);

    }

    public function indexAction()
    {
        $service = new \Application\Service\UltimaMilha();
        $service->setId(1);
        //$service->load();
        $form = new UltimaMilha();
        $form->setData($service->toArray());

        $avaliacao = new Container('SistemaSelecionado');
        $exame = $avaliacao->offsetGet('sistema');

        $lockSsi = file_get_contents('./data/settings/lock-ssi.txt');
        $downloadAnexo = file_get_contents('./data/settings/download-anexo.txt');
        $avaliacaoPedagogicaPadrao = file_get_contents('./data/settings/avaliacao-pedagogica-padrao.txt');
        $diaAplicacaoPadrao = file_get_contents('./data/settings/dia-aplicacao-padrao.txt');
        return new ViewModel(['form'=>$form,'exame'=>$exame,'lockSsi'=>$lockSsi,'downloadAnexo'=>$downloadAnexo,'avaliacaoPedagogicaPadrao'=>$avaliacaoPedagogicaPadrao,'diaAplicacaoPadrao'=>$diaAplicacaoPadrao]);
    }

    public function lockSsiAction()
    {
        $post = $this->getRequest()->getPost();
        $lock = 1;
        if(!isset($post['lockSsi'])) $lock = 0;
        if($post['lockSsi'] == '') $lock = 0;
        if($post['lockSsi'] == 0) $lock = 0;

        file_put_contents('./data/settings/lock-ssi.txt',$lock);

        $this->addSuccessMessage('Configuração salva com sucesso');
        return $this->redirect()->toUrl('/configuracao');
    }

    public function downloadAnexoAction()
    {
        $post = $this->getRequest()->getPost();
        $lock = 1;
        if(!isset($post['download_anexo'])) $lock = 0;
        if($post['download_anexo'] == '') $lock = 0;
        if($post['download_anexo'] == 0) $lock = 0;

        file_put_contents('./data/settings/download-anexo.txt',$lock);

        $this->addSuccessMessage('Configuração salva com sucesso');
        return $this->redirect()->toUrl('/configuracao');
    }

    public function saveDataAction(){
        try{
            $post = $this->getRequest()->getPost();
            $ultima = new \Application\Service\UltimaMilha();
            $ultima->setId(1);
            $method = 'set'.$post['uf'];
            $ultima->{$method}($post['dados']);
            $ultima->salvar();
            $return = ['error'=>false];
        }catch(\Exception $e){
            $return = ['error'=>true,'message'=>$e->getMessage()];
        }

        return new JsonModel($return);
    }

    public function dataMilhaAction(){
        $ultima = new \Application\Service\UltimaMilha();
        $ultima->setId(1);
        $ultima->load();
        $data = $ultima->toArray();
        $return = [];
        foreach($data as $key => $value){
            if(in_array($key, ['Id'])) continue;
            $dados = explode('|', $value);
            $return[] = ['ultima_milha'=>$dados[0],'abstecao'=>$dados[1],'uf'=>$key];
        }

        return new JsonModel($return);
    }

    public function correiosAction(){
        $service = new \Application\Service\UltimaMilha();
        $service->setId(1);
        //$service->load();
        $form = new UltimaMilha();
        $form->setData($service->toArray());
        return new ViewModel(['form'=>$form]);
    }


    public function uploadPlanoAction(){
        $files = $this->getRequest()->getFiles();
        $content = file_get_contents($files['plano']['tmp_name']);
        file_put_contents('./public/plano/plano.pdf', $content);

        $this->addSuccessMessage('Plano atualizado com sucesso');
        return $this->redirect()->toUrl('/configuracao');
    }

    public function uploadManualAction(){
        $files = $this->getRequest()->getFiles();
        $content = file_get_contents($files['manual']['tmp_name']);

        unlink('./public/manuais/Manual Perfil_cime.pdf');

        file_put_contents('./public/manuais/Manual Perfil_cime.pdf', $content);

        $this->addSuccessMessage('Manual atualizado com sucesso');
        return $this->redirect()->toUrl('/configuracao');
    }

    public function uploadCoordenacaoAction(){
        $files = $this->getRequest()->getFiles();
        $content = file_get_contents($files['coordenacoes']['tmp_name']);

        $avaliacao = new Container('SistemaSelecionado');
        $exame = $avaliacao->offsetGet('sistema');

        file_put_contents('./data/coordenacoes/coordenacoes_'.$exame.'.csv', $content);

        $this->addSuccessMessage('Arquivo de Coordenações Atualizado');
        return $this->redirect()->toUrl('/configuracao');
    }

    public function setAvaliacaoPedagogicaPadraoAction()
    {
        $post = $this->getRequest()->getPost();
        $padrao = '';
        if(!isset($post['AvaliacaoPedagogica'])) {
            $padrao = '';
        } else {
            $padrao = $post['AvaliacaoPedagogica'];
        }

        file_put_contents('./data/settings/avaliacao-pedagogica-padrao.txt',$padrao);

        $this->addSuccessMessage('Configuração salva com sucesso');
        return $this->redirect()->toUrl('/configuracao');
    }

    public function setDiaAplicacaoPadraoAction()
    {
        $post = $this->getRequest()->getPost();
        $padrao = '';
        if(isset($post['DiaAplicacao'])) {
            $padrao = $post['DiaAplicacao'];
        }

        $sistema = new \Zend\Session\Container('SistemaSelecionado');
        $sistema->offsetSet('diaAplicacao',$padrao);

        file_put_contents('./data/settings/dia-aplicacao-padrao.txt',$padrao);

        $this->addSuccessMessage('Configuração salva com sucesso');
        return $this->redirect()->toUrl('/configuracao');
    }

}
