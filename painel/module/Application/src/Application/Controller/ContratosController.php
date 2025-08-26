<?php

namespace Application\Controller;

use Application\CustomAttributes\ContratoCustom;
use Application\CustomAttributes\OrdemServicoCustom;
use Application\Form\OrdemServico;
use Application\Service\Categoria;
use Classes\Service\Acesso;
use Classes\Service\Atividade;
use Classes\Service\Aviso;
use Estrutura\Controller\AbstractEstruturaController;
use Estrutura\Service\Conexao;
use Estrutura\Service\Config;
use Modulo\Service\UsuarioApi;
use RiskManager\OData\Filter;
use RiskManager\Organization\Service\Asset;
use RiskManager\Organization\Service\Perimeter;
use RiskManager\Workflow\Service\Event;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ContratosController extends AbstractEstruturaController {

    public function indexAction()
    {
        $form = new OrdemServico();
        $dados = $this->loadAction(true);
        return new ViewModel(['dados'=>$dados,'OrdemServicoForm'=>$form]);
    }

    public function addAction(){
        try{
            $request = $this->getRequest();
            if(!$request->isPost()) throw new \Exception('Favor Envie um formulário');

            $post = $request->getPost();

            $asset = new Asset();
            $asset->setAnonimo();
            $asset->setName($post['name']);
            $asset->setAssetType('Ordem de Serviço');
            $asset->setRelevance(3);

            $asset->setPath($post['IdContrato']);

            $form = new OrdemServico();
            $form->setData($post);
            $form->isValid();

            $totalUst = 0;
            foreach($post as $key => $item){
                if(preg_match('/servico_s/',$key)){
                    $totalUst += $item;
                }
            }

            $custom = new \Application\CustomAttributes\OrdemServicoCustom();
            $custom->exchangeArray($form->getData());
            $custom->set('total_ust',$totalUst);

            $contrato = new Perimeter();
            $contrato->setId($post['IdPerimetro']);
            $contrato->setAnonimo();
            $contrato->load();

            $dadosContrato = $contrato->getCustomAttributes(true);
            if(!isset($dadosContrato['total_ust'])) throw new \Exception('Os dados de UST não foram preenchidos no contrato');
            $utilizadas = (isset($dadosContrato['ust_utilizadas'])) ? $dadosContrato['ust_utilizadas'] : 0;
            $saldo = $dadosContrato['total_ust'] - $utilizadas;

            if($totalUst > $saldo) throw  new \Exception('Saldo de UST do contrato é insuficiente');

            $totalUtilizado = $totalUst + $utilizadas;
            $customContrato = new ContratoCustom();
            $customContrato->set('ust_utilizadas',$totalUtilizado);

            $contrato = new Perimeter();
            $contrato->setId($post['IdPerimetro']);
            $contrato->setAnonimo();
            $contrato->setCustomAttributes($customContrato);
            $contrato->save();

            $asset->setCustomAttributes($custom);
            $asset->save();

            $this->addSuccessMessage('OS Salva com sucesso');
        }catch (\Exception $e){
            //debug($asset->hydrate(),0);
            //debug($e->getMessage());
            $this->addErrorMessage($e->getMessage());
        }

        return $this->redirect()->toUrl('/contratos');
    }

    public function loadAction($return=false){
        if($this->params('id') || !file_exists('./data/cache/contratos.json') || $return=true){
            $ativo = new Asset();
            $ativo->setAnonimo();

            $perimeter = new Perimeter();
            $perimeter->setAnonimo();

            $filter = new Filter();
            $filter->setPageSize(1000);
            $filter->where(Filter::F_STARTSWITH, Filter::A_STRING,'Path','Gestão de Contratos >');
            //$filter->where(Filter::F_EQ, Filter::A_STRING, 'CustomAttributes/ano_vigencia',date('Y'));

            $perimeter->setFilter($filter);

            $event = new Event();

            $trs = $perimeter->fetchAll();
            $trTratado = [];
            foreach($trs as $tr){
                if($tr->getCustomAttribute('ano_vigencia') != date('Y')) continue;
                $saldo = $tr->getCustomAttribute('total_ust') - $tr->getCustomAttribute('ust_utilizadas');

                $dadosTr = ['nome'=>$tr->getName(),'id'=>$tr->getId(),'path'=>$tr->getPath(),'saldo'=>$saldo];

                $filter = new Filter();
                $filter->where(Filter::F_STARTSWITH,Filter::A_STRING,'Path',$tr->getPath());
                $ativo->setFilter($filter);
                $listaOs = $ativo->fetchAll();
                $osTratado = [];
                foreach($listaOs as $os){
                    $custom = $os->getCustomAttributes(true);
                    foreach($custom as $key => $item){
                        if($key == 'data_de_inicio' || $key == 'data_de_termino'){
                            $custom[$key] = $event->epocToDate($item);
                        }
                    }
                    if(!isset($custom['data_de_inicio'])) $custom['data_de_inicio'] = '-';
                    if(!isset($custom['data_de_termino'])) $custom['data_de_termino'] = '-';

                    $osTratado[] = ['nome'=>$os->getName(),'custom'=>$custom,'id'=>$os->getId()];
                }
                $dadosTr['os'] = $osTratado;
                $trTratado[] = $dadosTr;
            }


            if($return) return $trTratado;

            $json = json_encode(['contratos'=>$trTratado]);
            file_put_contents('./data/cache/calendar.json', $json);
            echo $json;
            die;
        }else{
            echo file_get_contents('./data/cache/calendar.json');
            die;
        }
    }

    public function resumoAction(){
        $idContrato = $this->params('id');
        $contrato = new Perimeter();
        $contrato->setAnonimo();
        $contrato->setId($idContrato);
        $contrato->load();

        $os = new Asset();
        $os->setAnonimo();
        $filter = new Filter();
        $filter->where(Filter::F_STARTSWITH,Filter::A_STRING,'Path',$contrato->getPath());
        $filter->setPageSize(1000);
        $os->setFilter($filter);
        $listaOs = $os->fetchAll();

        $total = [];
        $totalUsado = 0;
        foreach($listaOs as $os){
            foreach($os->getCustomAttributes(true) as $key => $value){
                if(preg_match('/servico_s/',$key)){
                    $tipo = strtoupper(str_replace('servico_','',$key));
                    if(!isset($total[$tipo])) $total[$tipo] = 0;
                    $total[$tipo] += $value;
                    $totalUsado += $value;
                }
            }
        }

        $customContrato = $contrato->getCustomAttributes(true);
        if(!isset($customContrato['total_ust'])) $customContrato['total_ust'] = 0;
        $grafico = [];
        foreach($total as $tipo => $tTipo){
            $grafico[strtoupper($tipo)] = round(($tTipo * 100 ) / $customContrato['total_ust']);
        }

        $dados = [];
        $dados['utilizado'] = $totalUsado;
        $dados['restante'] = $customContrato['total_ust'] - $totalUsado;


        $view = new ViewModel(['dados'=>$grafico,'totais'=>$total,'contrato'=>$contrato,'resumo'=>$dados]);
        $view->setTerminal(true);
        return $view;

        //return new JsonModel(['dados'=>$grafico,'totais'=>$total]);
    }

    public function osAction(){
        $id = $this->params('id');

        $asset = new Asset();
        $asset->setAnonimo();
        $asset->setId($id);
        $asset->load();

        $event = new Event();

        $dados = $asset->toArray();
        $dados['custom'] = $asset->getCustomAttributes(true);

        foreach($dados['custom'] as $key => $value){
            if(preg_match('/data_/',$key)){
                $dados['custom'][$key] = $event->epocToDate($value);
            }
        }

        return new JsonModel(['dados'=>$dados]);
    }

    public function updateAction(){
        $post = $this->getRequest()->getPost();
        $asset = new Asset();
        $asset->setAnonimo();
        $asset->setId($post['IdAsset']);

        $custom = new OrdemServicoCustom();
        $custom->set('situacao',$post['situacao']);

        $asset->setCustomAttributes($custom);
        $asset->save();

        $this->addSuccessMessage('Ordem de Serviço Atualizado');
        return $this->redirect()->toUrl('/contratos');
    }

}