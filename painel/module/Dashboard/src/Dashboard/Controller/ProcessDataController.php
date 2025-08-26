<?php

namespace Dashboard\Controller;

use Application\Service\WriteFile;
use Classes\Service\UltimaMilha;
use Estrutura\Controller\AbstractEstruturaController;
use RiskManager\OData\Filter;
use RiskManager\Workflow\Service\Event;
use RiskManager\Workflow\Service\Queries;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ProcessDataController extends AbstractEstruturaController{

    public function detalheMilhaAction(){
        $mapa = ['E'=>'ENTREGUE','NE'=>'NAO ENTREGUE'];
        $ultimaMilha = new UltimaMilha();
        $ultimaMilha->setStatus($mapa[$this->params('id')]);
        $lista = $ultimaMilha->filtrarObjeto();

        $lista = $lista->toArray();

        $this->layout('layout/dashboard/clean');


        return new ViewModel(['lista'=>$lista,'form'=>new \Classes\Form\UltimaMilha()]);
    }

    public function listaEventosAction(){
        $color = ['#2b908f'=>'Aberto','#90ee7e'=>'Fechado',"#55B152"=>'1 - Baixo', "#548DD4"=>'2 - Atenção', "#FEF84A"=>'3 - Elevado', "#EC632F"=>'4 - Alto', "#D44534"=>'5 - Severo', "#cccccc" => 'Não Qualificado'];

        $post = $this->getRequest()->getPost();
        $params = explode('-', $post['get']);

        $write = new WriteFile();
        $dados = $write->get($params[0]);

        if($params[1] == 'categoria'){
            if(isset($dados[$color[$post['color']]][$post['category']])){
                $retorno = $dados[$color[$post['color']]][$post['category']];
            }
        }

        if($params[1] == 'etapa'){
            if(isset($dados[$post['category']][$color[$post['color']]])){
                $retorno = $dados[$post['category']][$color[$post['color']]];
            }
        }

        $form = new \Dashboard\Form\Event();
        $this->layout('layout/dashboard/clean');

        return new ViewModel(['retorno'=>$retorno,'form'=>$form,'evento'=>new Event()]);
    }

    public function listaEventosOldAction(){
        $color = ['#2b908f'=>'Aberto','#90ee7e'=>'Fechado',"#55B152"=>'1 - Baixo', "#548DD4"=>'2 - Atenção', "#FEF84A"=>'3 - Elevado', "#EC632F"=>'4 - Alto', "#D44534"=>'5 - Severo', "#cccccc" => 'Não Qualificado'];

        $params = $this->params('id');
        $params = explode('-', $params);

        $write = new WriteFile();
        $dados = $write->get($params[0]);

        $post = $this->getRequest()->getPost();

        if($params[1] == 'categoria'){
            if(isset($dados[$color[$post['color']]][$post['category']])){
                return new JsonModel($dados[$color[$post['color']]][$post['category']]);
            }
        }

        if($params[1] == 'status'){
            if(isset($dados[$post['status']])){
                return new JsonModel($dados[$post['status']]);
            }
        }

        if($params[1] == 'etapa'){
            if(isset($dados[$post['category']][$color[$post['color']]])){
                return new JsonModel($dados[$post['category']][$color[$post['color']]]);
            }
        }

        return new JsonModel();
    }


    protected $file = './data/arquivos/json_dashboard.json';
    public function saveDataAction(){
        echo "Iniciando execução para gerar a massa de dados para painel Dashboard.".PHP_EOL;
        $json = $this->indexAction(true);
        file_put_contents($this->file,$json);
        file_put_contents(BASE_PATCH.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'sync.html',date('d/m/Y H:i:s'));

        echo 'Feito';
        die;
    }

    public function loadJsonAction(){
        $json = file_get_contents($this->file);
        echo $json;
        die;
    }

    public function indexAction($return=false)
    {
        $write = new WriteFile();

        $dados = ['abertos'=>0,'fechados'=>0];


        echo "Carregando dados dos Estados...".PHP_EOL;
        $dados['estados'] = $this->dadosEstados();
        echo "Carregando dados das Etapas...".PHP_EOL;
        $dados['porEtapa'] = $this->alertasEtapas();

        $dados['categorias'] = [];

        echo "Carregando consulta do Workflow...".PHP_EOL;
        $querie = new Queries();
        $querie->setAnonimo();

//        $querie->setId('7eff48f3-40f3-4392-b34b-4b147d483cfa'); //Consulta encontra-se com problemas
        $querie->setId('58c1665e-fd66-49e5-a4da-454341e72575');
        $filter = new Filter();
        $filter->setPageSize(1000);
        $querie->setFilter($filter);
        $eventos = $querie->fetchAll();

        echo "Modelando dados para o painel Dahboard.".PHP_EOL;
        $paraSessao = [];
        foreach($eventos as $item){

            if($item->Status == 'Fechado') $dados['fechados']++;
            if($item->Status == 'Aberto') $dados['abertos']++;

            $categoria = ($item->categoria_evento) ? $item->categoria_evento : 'Sem categoria';
            if(!isset($dados['categorias'][$categoria])){
                $dados['categorias'][$categoria] = ['abertos'=>0,'fechados'=>0];
            }

            if($item->Status == 'Fechado') $dados['categorias'][$categoria]['fechados']++;
            if($item->Status == 'Aberto') $dados['categorias'][$categoria]['abertos']++;

            $paraSessao[$item->Status][$categoria][] = $item;
        }

        echo "Escrevendo dados na sessão.".PHP_EOL;
        $write->write('aplicacao',$paraSessao);

        $dados['nomeCategorias'] = array_keys($dados['categorias']);

        $dataCategoriaAbertos = [];
        $dataCategoriaFechados = [];
        foreach($dados['categorias'] as $categoria){
            array_push($dataCategoriaAbertos, $categoria['abertos']);
            array_push($dataCategoriaFechados, $categoria['fechados']);
        }

        $porCategoria = [];
        $porCategoria[] = ['name'=>'Abertos','data'=>$dataCategoriaAbertos];
        $porCategoria[] = ['name'=>'Fechados','data'=>$dataCategoriaFechados];

        $dados['porCategoria'] = $porCategoria;

        $eventoTratado = [];
        foreach($eventos as $evento){
            $eventoTratado[$evento->EventID] = $evento;
        }

        ksort($eventoTratado);
        $eventoTratado = array_reverse($eventoTratado);

        $i = 0;
        $final = [];
        foreach($eventoTratado as $evento){
            if($evento->Status != 'Aberto') continue;

            $i++;
            if($i == 10) continue;

            $final[] = $evento;
        }

        $dados['alertas'] = $final;

        echo "Carregando dados da última milha.".PHP_EOL;
        $dados['ultima_milha'] = $this->ultimaMilha();
        //$dados['abertos'] = 10;

        if($return){
            return json_encode($dados);
            die;
        }

        return new JsonModel($dados);
    }

    public function ultimoAlertaAction(){

        $event = new Event();
        $event->setAnonimo();

        $filter = new Filter();
        $filter->where(Filter::F_EQ, Filter::A_STRING, 'EventType','Evento SGIR');
        $filter->addOrder('Created','DESC');
        //$filter->andWhere(Filter::F_GE, Filter::A_DATETIME,'Created', date('Y').'-01-01');
        //$filter->andWhere(Filter::F_GE, Filter::A_DATETIME,'Created', date('Y').'-01-01');
        //$filter->andWhere(Filter::F_EQ, Filter::A_STRING,'CustomAttributes/sistema', 'Enem');
        $filter->andWhere(Filter::F_EQ, Filter::A_STRING,'CustomAttributes/etapas_do_cronograma', 'Exame');
        //$filter->andWhere(Filter::F_EQ, Filter::A_STRING,'CustomAttributes/origem_informacao', 'CMI');
        //$filter->orWhere(Filter::F_EQ, Filter::A_STRING,'CustomAttributes/origem_informacao', 'RNC');
        //$filter->orWhere(Filter::F_EQ, Filter::A_STRING,'CustomAttributes/origem_informacao', 'RM - Operação');
        $filter->setPageSize(1);

        $event->setFilter($filter);

        $dados = $event->fetchAll();

        $return = ['error'=>true];
        if(isset($dados[0])){
            $return = ['error'=>false,'titulo'=>$dados[0]->getTitle(),'code'=>$dados[0]->getCode()];
        }

        return new JsonModel($return);

    }

    private function alertasEtapas(){
        $querie = new Queries();
        $querie->setAnonimo();

        $filter = new Filter();
        $filter->setPageSize(1000);
        $querie->setFilter($filter);
      //  $querie->setId('994dff68-1b87-4f4d-9f12-9f506d3dba38');
        $querie->setId('40d09c4b-74f7-49b7-9176-0a41bafbfc06'); //filtro Ano alterado para 2016

        $eventos = $querie->fetchAll();

        $tratado = [];
        $tratado['abertos'] = 0;
        $tratado['fechados'] = 0;
        $tratado['data'] = [];
        $tratado['categorias'] = [];
        $tratado['porData'] = [];

        $event = new Event();

        $mes = date('m');

        $porStatus = [];
        $porStatus['Aberto']['1 - Baixo'] = 0;
        $porStatus['Aberto']['2 - Atenção'] = 0;
        $porStatus['Aberto']['3 - Elevado'] = 0;
        $porStatus['Aberto']['4 - Alto'] = 0;
        $porStatus['Aberto']['5 - Severo'] = 0;
      //  $porStatus['Aberto']['Não Qualificado'] = 0;

        $porStatus['Fechado']['1 - Baixo'] = 0;
        $porStatus['Fechado']['2 - Atenção'] = 0;
        $porStatus['Fechado']['3 - Elevado'] = 0;
        $porStatus['Fechado']['4 - Alto'] = 0;
        $porStatus['Fechado']['5 - Severo'] = 0;
     //   $porStatus['Fechado']['Não Qualificado'] = 0;

        $paraSessao = [];
        foreach($eventos as $evento){

         /*   $dataCriacao  = $event->epocToDate($evento->Created);
            if(preg_match('@/'.$mes.'/@', $dataCriacao)){
                if(!isset($tratado['porData'][$dataCriacao])){
                    $tratado['porData'][$dataCriacao] = ['Aberto'=>0,'Fechado'=>0];
                }

                $tratado['porData'][$dataCriacao][$evento->Status] += 1;
            }*/

            if($evento->Status == 'Fechado'){
                $tratado['fechados']++;
            }else{
                $tratado['abertos']++;
            }

            if(!$evento->nivel_do_alerta_sgir){
                //debug($evento);
            }

            $tratado['data'][] = ['nivel'=>$evento->nivel_do_alerta_sgir,'titulo'=>$evento->Title,'etapa'=>$evento->caminho_critico,'evento'=>$evento->EventID];

            $nivel = ($evento->nivel_do_alerta_sgir) ? $evento->nivel_do_alerta_sgir : 'Não Qualificado';

            if($evento->Status == 'Aberto'){
                if(!(isset($tratado['categorias'][$evento->caminho_critico][$evento->nivel_do_alerta_sgir]))){
                    $tratado['categorias'][$evento->caminho_critico]['1 - Baixo'] = 0;
                    $tratado['categorias'][$evento->caminho_critico]['2 - Atenção'] = 0;
                    $tratado['categorias'][$evento->caminho_critico]['3 - Elevado'] = 0;
                    $tratado['categorias'][$evento->caminho_critico]['4 - Alto'] = 0;
                    $tratado['categorias'][$evento->caminho_critico]['5 - Severo'] = 0;
                    $tratado['categorias'][$evento->caminho_critico]['Não Qualificado'] = 0;
                }
                $tratado['categorias'][$evento->caminho_critico][$nivel]++;
            }

            if(!isset($porStatus[$evento->Status][$nivel])){
                $porStatus[$evento->Status][$nivel] = 0;
            }

            $porStatus[$evento->Status][$nivel]++;

            $paraSessao[$evento->caminho_critico][$nivel][] = $evento;
        }


        $name = [];
        foreach($porStatus['Aberto'] as $nivel => $valor){
            $name[] = ['name'=>$nivel,'y'=>$valor];
        }

        $porStatus['Aberto'] = $name;

        $name = [];
        foreach($porStatus['Fechado'] as $nivel => $valor){
            $name[] = ['name'=>$nivel,'y'=>$valor];
        }

        $porStatus['Fechado'] = $name;

        $tratado['pizza'] = $porStatus;

        ksort($tratado['categorias']);

        $tratado['nomeCategorias'] = array_keys($tratado['categorias']);

        $a1 = [];
        $a2 = [];
        $a3 = [];
        $a4 = [];
        $a5 = [];
        $a6 = [];
        foreach($tratado['categorias'] as $categoria){
            array_push($a1, $categoria['1 - Baixo']);
            array_push($a2, $categoria['2 - Atenção']);
            array_push($a3, $categoria['3 - Elevado']);
            array_push($a4, $categoria['4 - Alto']);
            array_push($a5, $categoria['5 - Severo']);
  //          array_push($a6, $categoria['Não Qualificado']);
        }

        $porCategoria = [];
        $porCategoria[] = ['name'=>'1 - Baixo','data'=>$a1];
        $porCategoria[] = ['name'=>'2 - Atenção','data'=>$a2];
        $porCategoria[] = ['name'=>'3 - Elevado','data'=>$a3];
        $porCategoria[] = ['name'=>'4 - Alto','data'=>$a4];
        $porCategoria[] = ['name'=>'5 - Severo','data'=>$a5];
   //     $porCategoria[] = ['name'=>'Não Qualificado','data'=>$a6];

        $tratado['porCategoria'] = $porCategoria;

        $dataAbertos = [];
        $dataFechados = [];
        foreach($tratado['porData'] as $data => $valores){
            array_push($dataAbertos, $valores['Aberto']);
            array_push($dataFechados, $valores['Fechado']);
        }

        ksort($tratado['porData']);

        $tratado['nomeDatas'] = array_keys($tratado['porData']);

        $porData = [];
        $porData[] = ['name'=>'Abertos','data'=>$dataAbertos];
        $porData[] = ['name'=>'Fechados','data'=>$dataFechados];

        $tratado['porData'] = $porData;

      //  debug($tratado);

        $write = new WriteFile();
        $write->write('alerta',$paraSessao);

        return $tratado;
    }

    private function ultimaMilha(){
        $ultima = new \Classes\Service\UltimaMilha();
        $lista = $ultima->contador();

        return $lista;
    }

    private function dadosEstados(){
        $querie = new Queries();
        $querie->setAnonimo();

//        $querie->setId('f51f3505-0d2d-475f-a977-fcd88c002ba1');
        $querie->setId('77daa15a-f8e2-4cac-ba40-dcd75ab68796'); //2016

        $filter = new Filter();
        $filter->setPageSize(1000);
        $querie->setFilter($filter);
        $dados = $querie->fetchAll();

        $tratado = [];

        $uf = [];
        foreach($dados as $item){
            $explode = explode(' - ', $item->Asset_Name);

            if(count($explode) == 1) continue;

            if(!isset($uf[$explode[0]][$item->Status])) {
                $uf[$explode[0]] = ['Aberto'=>0,'Fechado'=>0];
            }

            $tratado[$explode[0]][] = (array) $item;

            $uf[$explode[0]][$item->Status] += 1;
        }

        $write = new WriteFile();
        $write->write('estado', $tratado);

        $return['nomeEstados'] = array_keys($uf);

        $estadosAbertos = [];
        $estadosFechados = [];

        $valores = [];
        foreach($uf as $ufDados){
            array_push($estadosAbertos, $ufDados['Aberto']);
            array_push($estadosFechados, $ufDados['Fechado']);
        }

        $porSituacao = [];
        $porSituacao[] = ['name'=>'Abertos','data'=>$estadosAbertos];
        $porSituacao[] = ['name'=>'Fechados','data'=>$estadosFechados];

        $return['valores'] = $porSituacao;

        return $return;
    }

    public function alertasUfAction(){
        $write = new WriteFile();
        $dados = $write->get('estado');
        if(!$dados) return new JsonModel([]);

        $uf = [];
        foreach($dados[$this->params('id')] as $item){
            $uf[] = ['municipio'=>$item['Asset_Name'],'titulo'=>$item['Title'],'codigo'=>$item['EventID'],'categoria'=>($item['categoria_evento']) ? $item['categoria_evento'] : ''];
        }

        return new JsonModel($uf);
    }

    public function liveAction(){
        $querie = new Queries();
        $querie->setId('6235a974-b370-4dc2-9c60-a8829fa8a3fa');

        echo $querie->count();
        die;
    }

    public function pizzaAction(){
        $dados = ['abertos'=>0,'fechados'=>0];

        /// Abertos
        $querie = new Queries();
        $querie->setId('6235a974-b370-4dc2-9c60-a8829fa8a3fa');
        $dados['abertos'] = $querie->count();

        /// Fechados
        $querie = new Queries();
        $querie->setId('74554f8c-8397-4f5c-a064-7d86318756ef');
        $dados['fechados'] = $querie->count();

        return new JsonModel($dados);
    }

    public function detalhesEventoAction(){
        try{
            $code = $this->params('id');
            $evento = new Event();
            $evento->setAnonimo();
            $evento->setCode($code);
            $evento->load();

            $form = new \Dashboard\Form\Event();

            $titulos = [];
            foreach($evento->toArray() as $key => $valor){
                try{
                    if($valor){
                        if($valor === true) $valor = 'Sim';
                        if($valor === false) $valor = 'Não';

                        if(preg_match('/Date/',$valor)){
                            $valor = $evento->epocToDate($valor,'d/m/Y H:i:s');
                        }

                        if(!in_array($key,['title','description'])){
                            throw new \Exception('');
                        }

                        $titulo = $form->get($key)->getLabel();
                        $titulos[] = ['nome'=>$titulo,'valor'=>$valor];
                    }
                }catch(\Exception $e){

                }
            }

            /*foreach($evento->getCustomAttributes() as $key => $valor){
                try{
                    if($valor || $valor !== false){
                        if($valor === true) $valor = 'Sim';
                        if($valor === false) $valor = 'Não';

                        $titulo = $form->get($key)->getLabel();
                        $titulos[] = ['nome'=>$titulo,'valor'=>$valor];
                    }
                }catch(\Exception $e){

                }
            }*/

            $updates = [];
            $up = $evento->getUpdates();
            foreach($up as $update){

                $valor = (isset($update->NewValue))?$update->NewValue:$update->Comment;

                if(!isset($update->Property)){
                    if($valor != ''){
                        $titulo = 'Comentário';
                        $updates[] = ['atualizacao'=>$evento->epocToDate($update->Date,'d/m/Y H:i:s'),'valor'=>$valor,'campo'=>$titulo];
                    }
                }else{
                    try{
                        $titulo = $form->get($update->Property)->getLabel();
                    }catch(\Exception $e){
                        $titulo=$update->Property;
                    }
                }

                //$updates[] = ['responsavel'=>$update->UpdatedBy,'atualizacao'=>$evento->epocToDate($update->Date,'d/m/Y H:i:s'),'valor'=>$valor,'campo'=>$titulo];

            }

            $json = ['titulos'=>$titulos,'updates'=>$updates,'error'=>false];

        }catch (\Exception $e){
            $json = ['error'=>true,'message'=>'Houve um erro ao processar a informação'];
        }

        return new JsonModel($json);
    }

}