<?php
// module/Mobile/src/Mobile/Controller/IndexController.php

namespace Mobile\Controller;

// A herança foi trocada para a classe base correta
use Estrutura\Controller\AbstractEstruturaController;
use Zend\View\Model\ViewModel;
use Application\Form\Triagem as TriagemForm;
use Zend\Session\Container;
use Zend\View\Model\JsonModel; // Adicionado para a resposta AJAX

class IndexController extends AbstractEstruturaController // <-- MUDANÇA CRUCIAL AQUI
{
    public function cadastroOcorrenciaAction()
    {
        $this->layout('layout/mobile');

        $sistemaSession = new Container('SistemaSelecionado');
        $sistema = $sistemaSession->offsetGet('sistema');
        $diaAplicacao = $sistemaSession->offsetGet('diaAplicacao');
        $container = new Container('UsuarioApi');

        $form = new TriagemForm(null, $this->getServiceLocator());
        
        if ($diaAplicacao) {
            $form->get('DiaAplicacao')->setValue($diaAplicacao);
        }
        
        $request = $this->getRequest();

        if ($request->isPost()) {
            $postData = $request->getPost()->toArray();
            $filesData = $request->getFiles()->toArray();
            
            // Lógica de upload corrigida, idêntica à do TriagemController
            if (isset($filesData['anexo']) && !empty($filesData['anexo'][0]['tmp_name'])) {
                // Este método agora existe graças à nova herança
                $uplod = $this->uploadFile($filesData); 
                $postData = array_merge($postData, $uplod);
            }

            $form->setData($postData);

            if ($form->isValid()) {
                $dados = $form->getData();

                $padrao = [
                    'status' => 1,
                    'dataHora' => date('d/m/Y H:i:s'),
                    'usuario' => $container->offsetGet('nome') . ' - ' . $container->offsetGet('email'),
                    'OrigemInformacao' => 'RM-Mobile',
                    'Ano' => date('Y')
                ];

                if (isset($dados['ocorrencia']) && $dados['categoria'] != 'Segurança Pública') {
                    $dados['ocorrencia'] = '';
                }

                $service = new \Classes\Service\Alertas();
                $service->exchangeArray($dados);
                $service->exchangeArray($padrao);
                $service->setSistema($sistema);

                if ($service->salvar()) {
                     return $this->redirect()->toRoute('mobile', ['action' => 'confirmacao']);
                }
            }
        }

        return new ViewModel([
            'form' => $form,
            'sistema' => $sistema 
        ]);
    }

    public function confirmacaoAction()
    {
        $this->layout('layout/mobile');
        return new ViewModel();
    }

    /**
     * NOVA ACTION
     * Lógica para buscar as coordenações via AJAX, replicando o TriagemController.
     */
    public function getCoordenacaoAction()
    {
        try {
            $request = $this->getRequest();
            $post = $request->getPost();
            $tratados = [];

            if ($request->isPost() && $post['uf'] && $post['municipio']) {
                $service = new \Classes\Service\Alertas();
                $tratados = $service->getCoordenacoes($post['uf'], $post['municipio']);
            }

            return new JsonModel(['error' => false, 'dados' => $tratados]);
        } catch (\Exception $e) {
            return new JsonModel(['error' => true, 'message' => 'Houve um erro ao executar, tente novamente mais tarde.', 'dados' => $e->getMessage()]);
        }
    }
}