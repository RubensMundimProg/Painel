<?php

namespace Application\Controller;

use Application\Form\Triagem;
use Classes\Service\Alertas;
use Classes\Service\Municipios;
use Estrutura\Controller\AbstractEstruturaController;
use Modulo\Service\UsuarioApi;
use RiskManager\OData\CustomAttributes;
use RiskManager\OData\People;
use RiskManager\Organization\Service\Asset;
use RiskManager\Workflow\Service\Attributes;
use RiskManager\Workflow\Service\Event;
use Zend\View\Helper\Placeholder\Container;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use \Modulo\Service\RiskManager;

class TriagemController extends AbstractEstruturaController{

    public function indexAction(){
        $form = new \Classes\Form\Triagem();
        $form->get('Categoria')->setAttribute('required',false);
        $service = new \Classes\Service\Alertas();

        $filtro = [];
        if(count($this->getRequest()->getQuery()) > 0){
            $filtro = $this->getRequest()->getQuery()->toArray();
            $filtro['Categoria'] = str_replace('Selecione','',$filtro['Categoria']);
            foreach($filtro as $key => $item){
                if($key == 'DataRegistro') continue;
                if($item){
                    $method = 'set'.$key;
                    $service->{$method}($item);
                }
            }
            $form->setData($filtro);
        }

        $sistema = new \Zend\Session\Container('SistemaSelecionado');
        $nomeSistema = $sistema->offsetGet('sistema');

        if(!$nomeSistema){
            $this->addErrorMessage('Selecione a avaliação pedagógica');
            return $this->redirect()->toUrl('/');
        }else{
            $service->setSistema($nomeSistema);
        }

        $service->setStatus(1);
        $lista = $service->filtrarObjeto();

        if(isset($filtro['DataRegistro']) && $filtro['DataRegistro'] != ''){
            $tratado = [];
            foreach($lista as $item){
                if(preg_match('@'.$filtro['DataRegistro'].'@', $item->getDataHora())){
                    $tratado[] = $item;
                }
            }
            $lista = $tratado;
        }

        $usuarioApi = new UsuarioApi();
        $grupos = $usuarioApi->get('perfis');
        //$grupos = ['ciccn sp'];

        if(!in_array('Administrador INEP',$grupos)){
            $ciccn = [];
            foreach($grupos as $grupo){
                $grupo = strtolower($grupo);
                if(preg_match('/ciccn/', $grupo)){
                    if($grupo == 'ciccn'){
                        $ciccn[] = 'ciccn';
                    }else{
                        $ciccn[] = str_replace('ciccn ','',$grupo);
                    }
                }
            }

            if(count($ciccn) && !in_array('ciccn', $ciccn)){
                $tratado = [];
                foreach ($lista as $item){
                    $location = explode(' - ',$item->getUf());
                    if(in_array(strtolower($location[0]), $ciccn)){
                        $tratado[] = $item;
                    }
                }
                $lista = $tratado;
            }
        }

        return new ViewModel(['lista' => $lista, 'service' => $service, 'form'=>$form,'sistema'=>$nomeSistema]);
    }

    public function recusadosAction(){

        $form = new \Classes\Form\Triagem();
        $form->get('Categoria')->setAttribute('required',false);
        $service = new \Classes\Service\Alertas();

        $filtro = [];
        if(count($this->getRequest()->getQuery()) > 0){
            $filtro = $this->getRequest()->getQuery()->toArray();
            $filtro['Categoria'] = str_replace('Selecione','',$filtro['Categoria']);
            foreach($filtro as $key => $item){
                if($key == 'DataRegistro') continue;
                if($item){
                    $method = 'set'.$key;
                    $service->{$method}($item);
                }
            }
            $form->setData($filtro);
        }

        $sistema = new \Zend\Session\Container('SistemaSelecionado');
        $nomeSistema = $sistema->offsetGet('sistema');

        if($nomeSistema){
            $service->setSistema($nomeSistema);
        }

        $service->setStatus(0);
        $lista = $service->filtrarObjeto();
        if(isset($filtro['DataRegistro']) && $filtro['DataRegistro'] != ''){
            $tratado = [];
            foreach($lista as $item){
                if(preg_match('@'.$filtro['DataRegistro'].'@', $item->getDataHora())){
                    $tratado[] = $item;
                }
            }
            $lista = $tratado;
        }


        return new ViewModel(['lista' => $lista, 'service' => $service, 'form'=>$form,'sistema'=>$nomeSistema]);
    }

    public function excluirTriagemAction(){

        $service = new \Classes\Service\Alertas();
        $container = new \Zend\Session\Container('UsuarioApi');

        if($this->getRequest()->getPost()->code){
            $service->setId($this->getRequest()->getPost()->code);
            $service->setStatus(0);
            $service->setUsuarioTriagem($container->offsetGet('nome') . ' - '. $container->offsetGet('email'));
            $service->salvar();
        }

        $this->addSuccessMessage('Ocorrência arquivada com sucesso!');
        return $this->setRedirect('/triagem');
    }

    public function confirmarTriagemAction(){
        try{

            $container = new \Zend\Session\Container('SistemaSelecionado');
            $sistema = $container->offsetGet('sistema');

            $alerta = new \Classes\Service\Alertas();
            $container = new \Zend\Session\Container('UsuarioApi');

            if($this->params('id')){

                $id = $this->params('id');
                $statusAlerta = 1;//Aberto
                if(preg_match('/|/',$id)){
                    $aux = explode('|',$id);
                    if(count($aux) > 1){
                        $id = $aux[0];
                        $statusAlerta = $aux[1];//Fechar
                    }
                }

                $alerta->setId($id);
                if(!$alerta->load()){
                    throw new \Exception('Alerta não encontrado');
                }

                $oldSituacao = $alerta->getStatus();

                if($alerta->getStatus() == 3){
       //             throw new \Exception('A Ocorrências já esta sendo processada, aguarde alguns instantes e verifique se a mesma foi aceita');
                }

                $concorrencia = new Alertas();
                $concorrencia->setId($alerta->getId());
                $concorrencia->setStatus(3);
                $concorrencia->salvar();

                $occ = (preg_match('/Relevantes/', $alerta->getOcorrencia())) ?  'Outras Ocorrências Relevantes ': $alerta->getOcorrencia();

                $event = new Event();

                $categoriaTratada = ($alerta->getSubCategoria())?$alerta->getCategoria().' - '.$alerta->getSubCategoria():$alerta->getCategoria();
                if(preg_match('/Abastecimento de/',$categoriaTratada)) $categoriaTratada = 'Abastecimento de Água';
                if(preg_match('/Acidentes no local de/',$categoriaTratada)) $categoriaTratada = 'Emergências Médicas - Acidentes no local de prova';
                if(preg_match('/no local de prova/',$categoriaTratada)) $categoriaTratada = 'Emergências Médicas - Acidentes no local de prova';
//                if(preg_match('/Atendimento especializado/',$categoriaTratada)) $categoriaTratada = utf8_encode('Aplicação - Atendimento especializado');

                //REGRA: SE CAMPO PROVIDÊNCIAS ADOTADAS ESTIVER PREENCHIDO, O STATUS DE TRATAMENTO SERÁ "Em Tratamento" SENÃO SERÁ "Não Iniciado"
                $statusTratamento = $alerta->getProvidenciasAdotadas()?'Em Tratamento':'Não Iniciado'; //Não Iniciado - Em Tratamento - Finalizado

                //INFORMAÇÕES ADICIONAIS
                $informacoesAdicionais = ($alerta->getInformacoesAdicionais())?' INFORMAÇÕES ADICIONAIS: '.$alerta->getInformacoesAdicionais():'';

                $evento = [
                    'title' => (trim($alerta->getTitulo())),
                    'description' => (trim($alerta->getDescricao())).$informacoesAdicionais,
                    'eventType' => 'Evento SGIR',
                    'urgency' => 3,
                    'customAttributes' => [
                        'descricao_do_risco_no_alerta_sgir'=>$alerta->getDescricaoRisco(),
                        'nivel_do_alerta_sgir'=>$alerta->getNivelAlerta(),
                        'sistema'=>$alerta->getSistema(),
                        'ano_vigente'=>date('Y'),
                        'caminho_critico'=>'2. Implementação - Aplicação do exame',
                        '__origem_do_alerta'=>'RM - Operação',
                        'categoria_evento'=>$categoriaTratada,
                        'nro_processo'=>$alerta->getNroProcesso(),
                        'impacto_aplicacao'=>($alerta->getImpactoAplicacao() == 'Sim') ? 'Sim' : 'Não',
                        'coordenacao'=>$alerta->getCoordenacao(),
                        'municipio_de_aplicacao'=>$alerta->getMunicipio(),
                        'unidade_federativa'=>$alerta->getUf(),
                        'status_tratamento'=>$statusTratamento,
                        'origem_informacao'=>$alerta->getOrigemInformacao(),
                        'usuario_triagem'=>$container->offsetGet('nome') . ' - '. $container->offsetGet('email'),
                        'usuario_cadastro'=>$alerta->getUsuario(),
                        'tipos_ocorrencias'=>$occ,
                        'dia_da_aplicacao'=>$alerta->getDiaAplicacao(),
                        'providencias_adotadas'=>$alerta->getProvidenciasAdotadas(),
//                        'informacoes_adicionais'=>$alerta->getInformacoesAdicionais(),
                        'evento'=>'Interface'
                    ]
                ];

                $event->exchangeArray($evento);

                $customAttr = new CustomAttributes();
                $customAttr->exchangeArray($evento['customAttributes']);
                $event->setCustomAttributes($customAttr);


                //REMOVER ABAIXO APOS TESTE
               // $event->save();
                //REMOVER ACIMA APOS TESTE




                //COMENTADO ABAIXO PARA TESTES


                $people = new People();

                $dadosLocal = explode(' - ', $alerta->getMunicipio());

                $envolvidos = ['operacao','ciccn','ciccr'];
                foreach($envolvidos as $envolvido){
                    $people->setPerson(strtolower($envolvido.'.'.$dadosLocal[0]));
                }

               // $event->setInvolved($people);
                $event->save();

                $municipioService = new \Classes\Service\Municipios();
                $municipioService->setNome($alerta->getMunicipio());
                $dadosMunicipio = $municipioService->filtrarObjeto()->current();

                $patch = str_replace('\\', ' > ', $dadosMunicipio->getPatch());
                $ddd = explode(' > ', $patch);

                $estado = end($ddd);
                $municipio = ($dadosMunicipio->getNome());


                $pat = "Monitoramento > ".$sistema." > Municípios de Aplicação > ".$estado." > ".$municipio;
                if(preg_match('/Santa/', $pat)){
                    $pat = "Monitoramento > ".$sistema." > Municípios de Aplicação > Santa Catarina > ".$municipio;
                }
                if(preg_match('/PB -/', $pat)){
                    $pat = "Monitoramento > ".$sistema." > Municípios de Aplicação > Paraíba > ".$municipio;
                }

                if(preg_match('/RR -/', $pat)){
                    $pat = "Monitoramento > ".$sistema." > Municípios de Aplicação > Roraima > ".$municipio;
                }

                if(preg_match('/RO -/', $pat)){
                    $pat = "Monitoramento > ".$sistema." > Municípios de Aplicação > Rondônia > ".$municipio;
                }

                if(preg_match('/ES -/', $pat)){
                    $pat = "Monitoramento > ".$sistema." > Municípios de Aplicação > Espírito Santo > ".$municipio;
                }

                if(preg_match('/GO -/', $pat)){
                    $pat = "Monitoramento > ".$sistema." > Municípios de Aplicação > Goías > ".$municipio;
                }

                if(preg_match('/- SAO FRANCISCO DO SUL/', $pat)){
                    $pat = "Monitoramento > ".$sistema." > Municípios de Aplicação > Santa Catarina > SC - SAO FRANCISCO DO SUL";
                }


                $event->assetAssociate($pat);

                //CHECA ANEXOS
                if($alerta->getAnexo()){
                    if(preg_match('/|/',$alerta->getAnexo())){
                        $files = explode('|',$alerta->getAnexo());
                        foreach ($files as $file) {
                            $name = explode('/',$file);
                            $anexo = (preg_match('/public/', $file)) ? $file : './public/'.$file;
                            $event->addAttachment(end($name),'anexar_arquivos',file_get_contents($anexo));
                        }

                    }else{
                        $name = explode('/',$alerta->getAnexo());
                        $anexo = (preg_match('/public/', $alerta->getAnexo())) ? $alerta->getAnexo() : './public/'.$alerta->getAnexo();
                        $event->addAttachment(end($name),'anexar_arquivos',file_get_contents($anexo));
                    }
                }

                //Solicitação do Guerra: fechar se usuário clicar no ícone Aceitar Ocorrência e Fechar Alerta
                if($statusAlerta == 2){
                    $event->setStatus(2);
                    $event->save();
                }

                $service = new \Classes\Service\Alertas();
                $service->setId($id);
                $service->setStatus(2);
                $service->setUsuarioTriagem($container->offsetGet('nome') . ' - '. $container->offsetGet('email'));
                $service->setCodigoRm($event->getCode());
                $service->salvar();

            }

            $this->addSuccessMessage('Alerta foi Processado com Sucesso!');
            return new JsonModel(['error'=>false]);
        }catch (\Exception $e){
            $concorrencia = new Alertas();
            $concorrencia->setId($id);
            $concorrencia->setStatus($oldSituacao);
            $concorrencia->salvar();

            $error = 'Houve um erro ao executar, tente novamente mais tarde.';
            if(preg_match('/sendo processada/', $e->getMessage())){
                $error = $e->getMessage();
            }

            return new JsonModel(['error'=>true,
                'message'=>$error,
                'dados'=>$e->getMessage()]);
        }

       // return $this->setRedirect('/triagem');
    }

    public function editarTriagemAction(){
        try{
            $service = new \Classes\Service\Alertas();
            $container = new \Zend\Session\Container('UsuarioApi');

            if($this->params('id')){ //GET

                $service->setId($this->params('id'));

                if(!$service->load()){
                    throw new \Exception('Alerta não encontrado');
                }

                $form = new \Classes\Form\Triagem([],$service);
                $form->setData($service->toArray());

                if (!$form->isValid()) {
                    $this->addValidateMessages($form);
                }

                $view = new ViewModel(['form'=>$form,'service'=>$service]);
                $view->setTerminal(true);

                return $view;
            }else{ //POST
                $request = $this->getRequest();
                $post = $request->getPost()->toArray();

                if($request->isPost()) {

                    $files = $request->getFiles();
                    if($files['Anexo'][0]['tmp_name'] != '' || $files['anexo'][0]['tmp_name'] != ''){
                        $uplod = $this->uploadFile($files);

                        $checkAnexosAnteriores = new Alertas();
                        $checkAnexosAnteriores->setId($post['Id']);
                        $checkAnexosAnteriores->load();

                        //INCREMENTAR ANEXO NO CAMPO DO BD
                        if($checkAnexosAnteriores->getAnexo()){
                            $aux = explode('|',$checkAnexosAnteriores->getAnexo());
                            $chaveAux = key($uplod);
                            foreach ($aux as $item) {
                                $uplod[$chaveAux] = $uplod[$chaveAux].'|'.$item;
                            }
                        }

                        $post = array_merge($post, $uplod);
                    }

                    $form = new \Classes\Form\Triagem();
                    $form->setData($post);

                    if (!$form->isValid()) {
                        $this->addValidateMessages($form);
                    }

                    $service->exchangeArray($form->getData());

                    if(preg_match('/Emergências Médicas/',$service->getCategoria()) || preg_match('/Eliminação de Participantes/',$service->getCategoria())){
                        $service->setImpactoAplicacao('NÃO');
                        $service->setNroProcesso('');
                    }else{
                        if(preg_match('/Demanda Judicial/',$service->getSubCategoria())){
                            $service->setImpactoAplicacao('NÃO');
                        }else{
                            $service->setNroProcesso('');
                        }
                    }

                    $service->salvar();

                    $this->addSuccessMessage('Registro alterado com sucesso!');

                    $status = new Alertas();
                    $status->setId($service->getId());
                    $status->load();
                    
                    if($status->getStatus() == 0){
                        //return $this->setRedirect('/triagem/recusados');
                    }

                    return $this->setRedirect($_SERVER['HTTP_REFERER']);

//                    return new JsonModel(['error'=>false,'message'=>'Registro alterado com sucesso!','dados'=>[]]);

                }
            }

        }catch (\Exception $e){
            return new JsonModel(['error'=>true,'message'=>'Houve um erro ao executar, tente novamente mais tarde.','dados'=>$e->getMessage()]);
        }

        // return $this->setRedirect('/triagem');
    }

    public function cadastroOcorrenciaAction(){
        try {

            $form = new \Application\Form\Triagem();
            $container = new \Zend\Session\Container('UsuarioApi');
            $post = [];
            $request = $this->getRequest();
            $post = $request->getPost()->toArray();

            $sistema = new \Zend\Session\Container('SistemaSelecionado');
            $nomeSistema = $sistema->offsetGet('sistema');

            if(!$nomeSistema){
                $this->addErrorMessage('Selecione a avaliação pedagógica');
                return $this->redirect()->toUrl('/');
            }

            if($request->isPost()) {

                $files = $request->getFiles();
                if($files['Anexo'][0]['tmp_name'] != '' || $files['anexo'][0]['tmp_name'] != ''){
                    $uplod = $this->uploadFile($files);
                    $post = array_merge($post, $uplod);
                }

                $form->setData($post);

                if (!$form->isValid()) {
                    $this->addValidateMessages($form);
                    return $this->setRedirect('/triagem/cadastro-ocorrencia', $post);
                }

                $dados = $form->getData();

                // Aberto
                $padrao = [
                    'status' => 1,
                    'dataHora' => date('d/m/Y H:i:s'),
                    'usuario' => $container->offsetGet('nome') . ' - '. $container->offsetGet('email'),
                    'OrigemInformacao' => 'RM',
                    'Ano' => date('Y')
                ];

                if(isset($dados['ocorrencia']) && $dados['categoria'] != 'Segurança Pública'){
                    $dados['ocorrencia'] = '';
                }
//                else {
//                    $dados['titulo'] = $dados['ocorrencia'];
//                }

                $sistema = new \Zend\Session\Container('SistemaSelecionado');
                $nomeSistema = $sistema->offsetGet('sistema');

                $service = new \Classes\Service\Alertas();
                $service->exchangeArray($dados);
                $service->exchangeArray($padrao);
                $service->setSistema($nomeSistema);

                debug($service->hydrate());

                $service->salvar();

                $this->addSuccessMessage('Ocorrência registrada com sucesso. Aguardando triagem.');
                return $this->setRedirect('/');

            }

        } catch (\Exception $e) {
            $this->addErrorMessage($e->getMessage());
            return $this->setRedirect('/triagem/cadastro-ocorrencia', $post);
        }

        return new ViewModel(['form' => $form,'sistema'=>$nomeSistema]);
    }

    public function filtrarMunicipioAction () {

        $nome = strtoupper($this->params()->fromQuery('term'));

//        $service = new Municipios();
//        $service->setNome($nome);
//
//        $municipios = $service->filtrarObjeto();

//        $cache = new \Zend\Cache\Storage\Adapter\Filesystem();
//        $cache->getOptions()->setTtl(600);
//        $returnCache = $cache->getItem(md5($nome.'estados'), $cached);
//
//        if(!$cached){
//            $options = $municipios->toArray();
//        } else {
//            $options = json_decode($returnCache, true);
//        }

//        $tratado = [];
//        foreach($options as $municipio){
//            $tratado[] = ['item' => $municipio['NOME'], 'value' => $municipio['NOME']];
//        }


        $sistema = new \Zend\Session\Container('SistemaSelecionado');
        $nomeSistema = $sistema->offsetGet('sistema');

        $municipios = json_decode(file_get_contents('./data/municipios/'.$nomeSistema.'.json'),true);

        $tratado = [];
        foreach ($municipios as $key => $value) {
            if(strpos($value,$nome) > -1){
                $tratado[] = ['item' => $value, 'value' => $value];
            }
        }

        $usuarioApi = new UsuarioApi();
        $grupos = $usuarioApi->get('perfis');
        //$grupos = ['cime ce','cime df',];

        $cime = [];
        foreach($grupos as $grupo){
            $grupo = strtolower($grupo);
            if(preg_match('/cime/', $grupo)){
                $cime[] = str_replace('cime ','',$grupo);
            }
        }

        if(count($cime)){
            $limpo = [];
            foreach ($tratado as $item){
                $location = explode(' - ',$item['value']);
                if(in_array(strtolower($location[0]), $cime)){
                    $limpo[] = $item;
                }
            }
            $tratado = $limpo;
        }

        return new JsonModel($tratado);

    }

    public function getCategoriaSubCategoriaRelacionadaAction()
    {
        try{
            $service = new Alertas();
            return new JsonModel(['error'=>false,'dados'=>$service->categoriaSubCategoria()]);
        }catch(\Exception $e){
            return new JsonModel(['error'=>true,'message'=>'Houve um erro ao executar, tente novamente mais tarde.','dados'=>$e->getMessage()]);
        }
    }

    public function getCoordenacaoAction()
    {
        try{

            $request = $this->getRequest();
            $post = $request->getPost();

            $tratados = [];

            if($request->isPost() && $post['uf'] && $post['municipio']) {

                $service = new Alertas();

                $tratados = $service->getCoordenacoes($post['uf'], $post['municipio']);
            }

            return new JsonModel(['error'=>false,'dados'=>$tratados]);
        }catch(\Exception $e){
            return new JsonModel(['error'=>true,'message'=>'Houve um erro ao executar, tente novamente mais tarde.','dados'=>$e->getMessage()]);
        }

    }

    public function getCategoriasSubcategoriasAction()
    {
        $service = new Alertas();
        return new JsonModel(['dados'=>$service->categoriaSubCategoria()]);
    }

    public function removerAnexoAction(){
        $data = $this->getRequest()->getPost();
        $service = new Alertas();
        $service->setId($data['id']);
        $service->load();

        $anexo = explode('|',$service->getAnexo());
        $novo = [];
        foreach($anexo as $clear){
            if($clear == $data['file']) continue;
            $novo[] = $clear;
        }

        $novo = implode('|',$novo);

        $service->setAnexo($novo);
        $service->salvar();

        return new JsonModel(['error'=>false]);
    }

    public function testeTimeZoneAction()
    {
        debug(date('Y-m-d H:i:s'));
    }

    public function anexoAction(){
        $anexo = base64_decode($this->params('id'));
        echo $anexo;die;
        $absoluto = BASE_PATCH.'/public'.$anexo;
    //    $content = file_get_contents($absoluto);
        $name = explode('/', $anexo);

        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . $name . "\"");
        readfile($absoluto); // do the double-download-dance (dirty but worky)
    }

    public function arquivarVariosAction(){
        $post = $this->getRequest()->getPost();
        $dados = $post['dados'];

        $service = new \Classes\Service\Alertas();
        $container = new \Zend\Session\Container('UsuarioApi');

        foreach($dados as $item){
            $service->setId($item);
            $service->setStatus(0);
            $service->setUsuarioTriagem($container->offsetGet('nome') . ' - '. $container->offsetGet('email'));
            $service->salvar();
        }

        $this->addSuccessMessage(count($dados).' Registros Arquivados');
        echo json_encode(['error'=>false]);
        die;
    }

    public function limparDuplicadosAction(){
        $eventos = new Alertas();
        $eventos->setOrigemInformacao('RNC');
        $lista = $eventos->filtrarObjeto();

        foreach($lista as $item){

            echo $item->getTitulo().PHP_EOL;
            $dados = explode(' - ',$item->getTitulo());
            $dados[0] = mb_strtoupper($dados[0], 'UTF-8');
            $dados[1] = mb_strtoupper($dados[1], 'UTF-8');
            $titulo = implode(' - ', $dados);

            $municipio  = mb_strtoupper($item->getMunicipio(), 'UTF-8');

            $service = new Alertas();
            $service->setId($item->getId());
            $service->setTitulo($titulo);
            $service->setMunicipio($municipio);
            echo $item->getTitulo().PHP_EOL;
            $service->salvar();
        }

        echo 'Feito';
        die;
    }

    public function rotinaAceitarAction(){
        $alerta = new Alertas();
        $alerta->setStatus(0);
        $dados = $alerta->filtrarObjeto();
        debug($dados);
    }

    private function aceitar($alerta){
        try{
            $oldSituacao = $alerta->getStatus();

            if($alerta->getStatus() == 3){
                throw new \Exception('A Ocorrências já esta sendo processada, aguarde alguns instantes e verifique se a mesma foi aceita');
            }

            $occ = (preg_match('/Relevantes/', $alerta->getOcorrencia())) ?  'Outras Ocorrências Relevantes ': $alerta->getOcorrencia();

            $event = new Event();

            $categoriaTratada = ($alerta->getSubCategoria())?$alerta->getCategoria().' - '.$alerta->getSubCategoria():$alerta->getCategoria();
            if(preg_match('/Abastecimento de/',$categoriaTratada)) $categoriaTratada = 'Abastecimento de Água';
            if(preg_match('/Acidentes no local de/',$categoriaTratada)) $categoriaTratada = 'Emergências Médicas - Acidentes no local de prova';
            if(preg_match('/no local de prova/',$categoriaTratada)) $categoriaTratada = 'Emergências Médicas - Acidentes no local de prova';
//                if(preg_match('/Atendimento especializado/',$categoriaTratada)) $categoriaTratada = utf8_encode('Aplicação - Atendimento especializado');

            //REGRA: SE CAMPO PROVIDÊNCIAS ADOTADAS ESTIVER PREENCHIDO, O STATUS DE TRATAMENTO SERÁ "Em Tratamento" SENÃO SERÁ "Não Iniciado"
            $statusTratamento = $alerta->getProvidenciasAdotadas()?'Em Tratamento':'Não Iniciado'; //Não Iniciado - Em Tratamento - Finalizado

            //INFORMAÇÕES ADICIONAIS
            $informacoesAdicionais = ($alerta->getInformacoesAdicionais())?' INFORMAÇÕES ADICIONAIS: '.$alerta->getInformacoesAdicionais():'';

            $evento = [
                'title' => (trim($alerta->getTitulo())),
                'description' => (trim($alerta->getDescricao())).$informacoesAdicionais,
                'eventType' => 'Evento SGIR',
                'urgency' => 3,
                'customAttributes' => [
                    'descricao_do_risco_no_alerta_sgir'=>$alerta->getDescricaoRisco(),
                    'nivel_do_alerta_sgir'=>$alerta->getNivelAlerta(),
                    'sistema'=>'Enem',
                    'ano_vigente'=>date('Y'),
                    'caminho_critico'=>'2. Implementação - Aplicação do exame',
                    '__origem_do_alerta'=>'RM - Operação',
                    'categoria_evento'=>$categoriaTratada,
                    'nro_processo'=>$alerta->getNroProcesso(),
                    'impacto_aplicacao'=>($alerta->getImpactoAplicacao() == 'Sim') ? 'Sim' : 'Não',
                    'coordenacao'=>$alerta->getCoordenacao(),
                    'municipio_de_aplicacao'=>$alerta->getMunicipio(),
                    'unidade_federativa'=>$alerta->getUf(),
                    'status_tratamento'=>$statusTratamento,
                    'origem_informacao'=>$alerta->getOrigemInformacao(),
                    'usuario_triagem'=>'Rotina - bruno.silva@modulo.com',
                    'usuario_cadastro'=>$alerta->getUsuario(),
                    'tipos_ocorrencias'=>$occ,
                    'providencias_adotadas'=>$alerta->getProvidenciasAdotadas(),
                    'evento'=>'Interface'
                ]
            ];

            $event->exchangeArray($evento);

            $customAttr = new CustomAttributes();
            $customAttr->exchangeArray($evento['customAttributes']);
            $event->setCustomAttributes($customAttr);

            $people = new People();
            $dadosLocal = explode(' - ', $alerta->getMunicipio());

            $envolvidos = ['operacao','ciccn','ciccr'];
            foreach($envolvidos as $envolvido){
                $people->setPerson(strtolower($envolvido.'.'.$dadosLocal[0]));
            }

            // $event->setInvolved($people);
            $event->save();

            $municipioService = new \Classes\Service\Municipios();
            $municipioService->setNome($alerta->getMunicipio());
            $dadosMunicipio = $municipioService->filtrarObjeto()->current();

            $patch = str_replace('\\', ' > ', $dadosMunicipio->getPatch());
            $ddd = explode(' > ', $patch);

            $estado = end($ddd);
            $municipio = ($dadosMunicipio->getNome());

            $pat = "Monitoramento > Enem > Municípios de Aplicação > ".$estado." > ".$municipio;
            if(preg_match('/Santa/', $pat)){
                $pat = "Monitoramento > Enem > Municípios de Aplicação > Santa Catarina > ".$municipio;
            }
            if(preg_match('/PB -/', $pat)){
                $pat = "Monitoramento > Enem > Municípios de Aplicação > Paraíba > ".$municipio;
            }

            if(preg_match('/RR -/', $pat)){
                $pat = "Monitoramento > Enem > Municípios de Aplicação > Roraima > ".$municipio;
            }

            if(preg_match('/RO -/', $pat)){
                $pat = "Monitoramento > Enem > Municípios de Aplicação > Rondônia > ".$municipio;
            }

            if(preg_match('/ES -/', $pat)){
                $pat = "Monitoramento > Enem > Municípios de Aplicação > Espírito Santo > ".$municipio;
            }

            if(preg_match('/GO -/', $pat)){
                $pat = "Monitoramento > Enem > Municípios de Aplicação > Goías > ".$municipio;
            }

            if(preg_match('/- SAO FRANCISCO DO SUL/', $pat)){
                $pat = "Monitoramento > Enem > Municípios de Aplicação > Santa Catarina > SC - SAO FRANCISCO DO SUL";
            }

            $event->assetAssociate($pat);

            //CHECA ANEXOS
            if($alerta->getAnexo()){
                if(preg_match('/|/',$alerta->getAnexo())){
                    $files = explode('|',$alerta->getAnexo());
                    foreach ($files as $file) {
                        $name = explode('/',$file);
                        $anexo = (preg_match('/public/', $file)) ? $file : './public/'.$file;
                        $event->addAttachment(end($name),'anexar_arquivos',file_get_contents($anexo));
                    }

                }else{
                    $name = explode('/',$alerta->getAnexo());
                    $anexo = (preg_match('/public/', $alerta->getAnexo())) ? $alerta->getAnexo() : './public/'.$alerta->getAnexo();
                    $event->addAttachment(end($name),'anexar_arquivos',file_get_contents($anexo));
                }
            }

            $service = new \Classes\Service\Alertas();
            $service->setId($alerta->getId());
            $service->setStatus(2);
            $service->setUsuarioTriagem('Rotina - bruno.silva@modulo.com');
            $service->setCodigoRm($event->getCode());
            $service->salvar();
        }catch(\Exception $e){
            file_put_contents('./data/erro_triagem.txt', 'Registro '.$alerta->getId().' - '. $e->getMessage(), FILE_APPEND);
        }
    }
} 