<?php

namespace Application\Controller;

use Application\Service\Categoria;
use Classes\Service\Acesso;
use Classes\Service\Atividade;
use Classes\Service\Aviso;
use Estrutura\Controller\AbstractEstruturaController;
use Estrutura\Service\Conexao;
use Estrutura\Service\Config;
use Modulo\Service\UsuarioApi;
use RiskManager\OData\Filter;
use RiskManager\Workflow\Service\Event;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractEstruturaController
{

    public function testeDataAction() {
        debug(date('d/m/Y H:i:s'));
    }

    public function resetAction()
    {

        $acessoContainer = new Container('ChaveAcesso');
        $acessoContainer->offsetUnset('chave');


        die;

    }

    public function indexAction()
    {
        $event = new Event();
        $event->setAnonimo();

        return new ViewModel();
    }

    public function sairAction()
    {
        $container = new Container('UsuarioApi');
        $modulo = ($container->offsetGet('ssi')) ? true : false;
        $container->offsetUnset('id');

        $acessoContainer = new Container('ChaveAcesso');

        $acesso = new Acesso();
        $acesso->setId($acessoContainer->offsetGet('chave'));
        $acesso->excluir();

        $this->addSuccessMessage('Usuário deslogado com sucesso');

        if ($modulo) return $this->redirect()->toUrl('/autenticacao');

        return new ViewModel();
    }

    public function escolasAction()
    {
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $uf = $post['uf'];
            $dados = explode(PHP_EOL, $post['escolas']);
            $ocupadas = [];
            $naoLocalizadas = [];
            foreach ($dados as $item) {
                $detalhes = explode(' - ', $item);
                if (count($detalhes) != 2) {
                    $detalhes = ['', $item];
                }
                $nomeEscola = str_replace(['C.E. ' . 'IFRJ ', 'IFMG ', 'E.E ', 'IFRS ', 'IFGO ', 'IFRN ', 'IFMT ', 'IFPE ', 'IFAL ', 'IFES ', 'Univasf - '], '', $detalhes[1]);
                $select = 'select * from escolas_atual where NO_LOCAL_PROVA like "%' . $nomeEscola . '%" AND NO_MUNICIPIO_PROVA = "' . $detalhes[0] . '"';
                //echo $select.'<br>';
                $lista = Conexao::listarSql($select);
                if (!count($lista)) {
                    $naoLocalizadas[] = $detalhes;
                } else {
                    foreach ($lista as $valores) {
                        $ocupadas[] = $valores;
                    }
                }
            }

            $base = '{SG_UF_PROVA};{NO_MUNICIPIO_PROVA};' . date('d/m/Y') . ';{NO_LOCAL_PROVA};{CO_LOCAL};{QTD_INSCRICAO_DIVULGADO}';
            $table = utf8_decode('UF;MUNICIPIO;Data;Aplicação do Enem 2016;CO_LOCAL;QTD_INSCRICAO_DIVULGADO') . PHP_EOL;
            foreach ($ocupadas as $ocupada) {
                $linha = $base;
                foreach ($ocupada as $key => $value) {
                    $linha = str_replace('{' . $key . '}', $value, $linha);
                }
                $table .= utf8_decode($linha) . PHP_EOL;
            }
            file_put_contents('./data/arquivos/ocupadas_' . $uf . '.csv', $table);

            $table = 'UF;ESCOLA;MUNICIPIO' . PHP_EOL;
            foreach ($naoLocalizadas as $nao) {
                $table .= utf8_decode($uf . ';' . $nao[1] . ';' . $nao[0]) . PHP_EOL;
            }
            file_put_contents('./data/arquivos/nao_localizacas_' . $uf . '.csv', $table);
            return $this->redirect()->toUrl('/index/escolas');
            echo 'Feito';
            die;
        }

        return new ViewModel();
    }

    public function meuEspacoAction()
    {
        $aviso = new Aviso();
        $listaAvisos = $aviso->filtrarObjeto();
        $lista = [];
        foreach ($listaAvisos as $aviso) {
            $inicio = \DateTime::createFromFormat('Y-m-d', $aviso->getDataInicio());
            $fim = \DateTime::createFromFormat('Y-m-d', $aviso->getDataFim());
            $now = new \DateTime();
            if ($now >= $inicio && $now <= $fim) {
                $lista[] = $aviso;
            }
        }

        $usuarioApi = new UsuarioApi();
        $perfis = $usuarioApi->get('perfis');

        return new ViewModel(['nome' => $usuarioApi->get('nome'), 'perfis' => $perfis, 'email' => $usuarioApi->get('email'), 'lista' => $lista]);
    }

    public function excluirAvisoAction()
    {
        $service = new Aviso();
        $service->setId($this->params('id'));
        $service->excluir();

        $this->addSuccessMessage('Registro excluido com sucesso');
        return $this->redirect()->toUrl('/index/gerir-aviso');
    }

    public function gerirAvisoAction()
    {

        $container = new Container('SistemaSelecionado');
        $sistema = $container->offsetGet('sistema');

        $form = new \Classes\Form\Aviso();
        $service = new Aviso();

        if ($this->getRequest()->isPost()) {

            $post = $this->getRequest()->getPost();
            $file = $this->getRequest()->getFiles();

            $upload = $this->uploadFile($file);

            $form->setData($post);
            $form->isValid();

            $dados = $form->getData();
            $service->exchangeArray($dados);
            $service->setAnexo($upload['Anexo']);
            $service->salvar();

            $this->addSuccessMessage('Registro Salvo com Sucesso!');
            return $this->redirect()->toUrl('/index/gerir-aviso');
        } else {
            if ($this->params('id')) {
                $dado = $service->buscar($this->params('id'));
                $dado->setDataInicio(\DateTime::createFromFormat('Y-m-d', $dado->getDataInicio())->format('d/m/Y'));
                $dado->setDataFim(\DateTime::createFromFormat('Y-m-d', $dado->getDataFim())->format('d/m/Y'));
                $form->setData($dado->toArray());
            }
        }

        $serviceLista = new Aviso();
        $listaServico = $serviceLista->filtrarObjeto();
        $tratados = [];
        foreach ($listaServico as $itemS) {
            if ($sistema != $itemS->getAvaliacaoPedagogica()) continue;
            $tratados[] = $itemS;
        }

        return new ViewModel(['form' => $form, 'service' => $service, 'lista' => $tratados, 'avaliacaoPedagogica' => $sistema]);
    }

    public function excluirAtividadeAction()
    {
        $service = new Atividade();
        $service->setId($this->params('id'));
        $service->excluir();

        $this->addSuccessMessage('Registro excluido com sucesso');
        return $this->redirect()->toUrl('/index/gerir-atividade');
    }

    public function gerirAtividadeAction()
    {
        $container = new Container('SistemaSelecionado');
        $sistema = $container->offsetGet('sistema');

        if (!$sistema) {
            $this->addErrorMessage('Favor selecione a avaliação pedagógica');
            return $this->redirect()->toUrl('/');
        }

        if ($this->getRequest()->isPost()) {
            $form = new \Classes\Form\Atividade();
            $service = new Atividade();

            $post = $this->getRequest()->getPost();

            $form->setData($post);
            $form->isValid();

            $dados = $form->getData();
            $service->exchangeArray($dados);
            $service->setSistema($sistema);
            $service->salvar();

            $this->addSuccessMessage('Registro Salvo com Sucesso!');
            return $this->redirect()->toUrl('/index/gerir-atividade');
        }

        $form = new \Classes\Form\Atividade();
        $form->get('Sistema')->setValue($sistema)->setAttribute('disabled', 'disabled');
        $service = new Atividade();

        $service->setSistema($sistema);

        $listaServico = $service->filtrarObjeto();

        $data = [];
        if ($this->params('id')) {
            $atividadeService = new Atividade();
            $atividadeService->setId($this->params('id'));
            $data = $atividadeService->filtrarObjeto()->current()->toArray();
            $dateTime = \DateTime::createFromFormat('Y-m-d', $data['Data']);
            $data['Data'] = $dateTime->format('d/m/Y');
        }

        $form->setData($data);

        return new ViewModel(['form' => $form, 'service' => $service, 'lista' => $listaServico, 'sistema' => $sistema]);
    }

    public function limparAcessoAction()
    {
        $acesso = new Acesso();
        $lista = $acesso->filtrarObjeto();
        foreach ($lista as $item) {
            $dataAcesso = \DateTime::createFromFormat('Y-m-d H:i:s', $item->getDataAcesso());
            $now = new \DateTime();
            $diff = $dataAcesso->diff($now);
            if ($diff->days > 0 || $diff->i > 45) { // 45 MINUTOS DE INATIVIDADE
                $item->excluir();
            }
        }

        echo 'Feito';
        die;
    }

    public function categoriaAction()
    {
        $categoria = new Categoria();
        $categoria->getTable();
    }

    public function alterarSistemaAction()
    {
        $sistema = $this->params('id');
        $container = new Container('SistemaSelecionado');
        $container->offsetSet('sistema', $sistema);

        $this->addSuccessMessage('Avaliação Pedagógica Alterada');

        return new JsonModel(['error' => false]);
    }

    public function consolidadoAction()
    {
        /*$event = new Event();
        $event->setAnonimo();
        $filter = new Filter();
        $filter->setPageSize(1000);
        $filter->where(Filter::F_EQ,Filter::A_STRING,'EventType','Evento SGIR');
        $event->setFilter($filter);

        $listaEvento = $event->fetchAll();
        $totalEvento = $this->tratarLista($listaEvento);
        $i = 1;
        echo 'Total '.count($totalEvento).' Página 1'.PHP_EOL;
        while (count($listaEvento) == 1000){
            $i++;
            echo 'Total '.count($totalEvento).' Página '.$i.PHP_EOL;
            $filter->setPage($i);
            $event->setFilter($filter);
            $listaEvento = $event->fetchAll();
            $totalEvento = array_merge($totalEvento,$this->tratarLista($listaEvento));
        }

        file_put_contents('./data/eventos.json',json_encode($totalEvento));
        echo 'Feito';
        die;*/

        $dados = json_decode(file_get_contents('./data/eventos.json'), true);
        $csv = [];
        foreach ($dados as $item) {
            $csv[] = [
                'Titulo' => $this->tratarValor($item, 'title'),
                'Ano' => $this->tratarValor($item['custom'], 'ano_vigente'),
                'Avaliação Pedagógica' => $this->tratarValor($item['custom'], 'sistema'),
                'Caminho crítico' => $this->tratarValor($item['custom'], 'caminho_critico'),
                'Categoria' => $this->tratarValor($item['custom'], 'categoria_evento'),
                'UF' => $this->tratarValor($item['custom'], 'unidade_federativa'),
                'Municipio' => $this->tratarValor($item['custom'], 'municipio_de_aplicacao'),
                'Origem' => $this->tratarValor($item['custom'], 'origem_informacao'),
                'Tipos ocorrências' => $this->tratarValor($item['custom'], 'tipos_ocorrencias')
            ];
        }
        $string = utf8_decode(implode(';', array_keys($csv[0]))) . PHP_EOL;
        foreach ($csv as $item) {
            $string .= implode(';', array_values($item)) . PHP_EOL;
        }
        file_put_contents('./data/eventos.csv', $string);
        echo 'Die';
        die;
    }

    public function tratarValor($array, $valor)
    {
        return (isset($array[$valor])) ? utf8_decode($array[$valor]) : '';
    }

    public function tratarLista($lista)
    {
        $tratado = [];
        foreach ($lista as $item) {
            $array = $item->toArray();
            $array['custom'] = $item->getCustomAttributes(true);
            $tratado[] = $array;
        }
        return $tratado;
    }

}