<?php

namespace Application\Controller;

use Application\Service\ApiView;
use Application\Service\Categoria;
use Application\Service\UltimaMilha;
use Classes\Service\Alertas;
use Estrutura\Controller\AbstractEstruturaController;
use Estrutura\Service\Config;
use RiskManager\OData\CustomAttributes;
use RiskManager\Workflow\Service\Event;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class ApiController extends AbstractEstruturaController{
    protected $apiView;

    public function __construct()
    {
        $this->apiView = new ApiView();
    }

    public function ocorrenciaAction(){
        //echo base64_encode(strtoupper(hash('sha256', 'InepRest')) . base64_encode(microtime()));
        //Authorization: Basic = base64_encode('InepAuthUser-XXXXXXX:InepAuthPass-*******');​
        try{
            if($this->getRequest()->isPost()){
                return $this->inserirOcorrencia();
            }

            if($this->getRequest()->isPut()){
                return $this->atualizarOcorrencia();
            }

            if(!$this->getRequest()->isPost()) throw new \Exception('Favor enviar um formulário');

        }catch(\Exception $e){
            return $this->apiView->errorReturn($e->getMessage());
        }
    }

    protected function atualizarOcorrencia(){
        $data = $this->getRequest()->getContent();
        $post = json_decode($data, true);

        if(!$post['id']){
            throw  new \Exception('Favor informar um ID');
        }

        $service = new Alertas();
        $service->setCodigoOrigem($post['id']);
        $objeto = $service->filtrarObjeto()->current();
        if(!$objeto) throw new \Exception('Ocorrência não encontrada');

        try{
            if(!$objeto->getCodigoRm()){
                if($objeto->getStatus() == 1){
                    return $this->apiView->errorReturn('Sua Ocorrência ainda esta na lista de triagem');
                }
                if($objeto->getStatus() == 0){
                    return $this->apiView->errorReturn('Sua Ocorrência foi recusada pela triagem');
                }
            }

            $event = new Event();
            $event->setAnonimo();
            $event->setCode($objeto->getCodigoRm());

            $customAttr = new CustomAttributes();
            $customAttr->set('situacao_sistema_origem', $post['situacao']);
            $event->setCustomAttributes($customAttr);

            $event->save();
        }catch(\Exception $e){
            return $this->apiView->errorReturn('Não foi possivel atualizar a situação da ocorrência.');
        }

        return $this->apiView->successReturn(['id'=>$objeto->getId()], 'Sua ocorrência foi atualizada com sucesso');
    }

    protected function inserirOcorrencia(){
        $data = $this->getRequest()->getContent();
        $post = json_decode($data, true);

        $categoria = new Categoria();
        $dadosCategoria = $categoria->getCategoria($post['categoria']);
        $post['categoria'] = $dadosCategoria['categoria'];
        $post['subcategoria'] = $dadosCategoria['subcategoria'];

        $post['municipio'] = $this->tirarAcentos(mb_strtoupper($post['municipio'], 'UTF-8'));
        $post['titulo'] = $post['municipio'].' - '.mb_strtoupper($post['coordenacao'], 'UTF-8').' - '.$post['categoria'];

        $anexo = false;
        if(isset($post['anexo']) && is_array($post['anexo'])){
            if (isset($post['anexo']['nome']) && $post['anexo']['nome'] != '' && isset($post['anexo']['data']) && $post['anexo']['data'] != '') {
                $anexo = $post['anexo'];
            }
            unset($post['anexo']);
        }

        $form = new \Application\Form\ApiTriagem();
        $form->setData($post);

        if(!$form->isValid()){
            throw new \Exception('Campos obrigatórios não foram preenchidos');
        }

        $dataHora = (isset($post['data_hora'])) ? $post['data_hora'] : date('d/m/Y H:i:s');

        $post = array_merge($post, [
            'status' => 1,
            'DataHora' => $dataHora,
            'usuario' => $post['nome_usuario'] . ' - '. $post['email_usuario']
        ]);

        if(isset($post['ocorrencia']) && $post['categoria'] != 'Segurança Pública'){
            $post['ocorrencia'] = '';
        }

        if($anexo){
            $nome = explode('.',$anexo['nome']);
            $randIt = rand(1,1000);
            $nome = $nome[0].'-'.time().$randIt.'.'.$nome[1];
            $finalAnexo = './public/anexos/'.$nome;
            file_put_contents($finalAnexo, base64_decode($anexo['data']));
            $post['anexo'] = $finalAnexo;
        }

        $service = new \Classes\Service\Alertas();
        $service->exchangeArray($post);
        $service->setSistema('Enem');//PROVISORIAMENTE FIXADO
        $service->setAno(date('Y'));
        $service->salvar();

        return $this->apiView->successReturn(['id'=>$service->getId()], 'Sua ocorrência foi inserido com sucesso e está aguardando a triagem.');
    }

    public function indexAction(){
        try{
            return $this->apiView->successReturn(['dados'=>['Teste']],'Dados gerados com sucesso');
        }catch(\Exception $e){
            return $this->apiView->errorReturn($e->getMessage());
        }
    }

    public function jsonAction(){
        try{
            $filename = $this->params('id');
            $file = './data/json_api/'.$filename.'.json';
            if(!file_exists($file)) throw new \Exception('Serviço de API não construido.');

            $data = json_decode(file_get_contents($file), true);

            return $this->apiView->successReturn($data,'Solicitação do Serviço Executada com Sucesso');
        }catch(\Exception $e){
            return $this->apiView->errorReturn($e->getMessage());
        }
    }

    public function tratarDadosJsonAction(){
        /// Dados de Validados
        $dados = $data = json_decode(file_get_contents('./data/cache/validados.json'), true);
        $filtro = true;

        $limpo = [];
        $total = [];
        foreach($dados as $item){
            if(!isset($total[$item['customAttributes']['ano_vigente']])) $total[$item['customAttributes']['ano_vigente']] = 0;
            $total[$item['customAttributes']['ano_vigente']]++;

//            if($item['customAttributes']['ano_vigente'] == '2017' && $item['customAttributes']['sistema'] == 'Enem' && $filtro){
//                $limpo[] = $item;
//            }
//            if(!$filtro){
//                $limpo[] = $item;
//            }

            $limpo[] = $item;
        }
        $dados = $limpo;

        echo count($dados).' Alertas Localizados'.PHP_EOL;

        $ocorrencia = new Alertas();
        if($filtro){
            $ocorrencia->setAno(date('Y'));
            $ocorrencia->setSistema('Enem');
        }
        $dadosOcorrencias = $ocorrencia->filtrarObjeto();
        echo count($dadosOcorrencias).' Ocorrencias Localizados'.PHP_EOL;
        $tratado = [];
        foreach($dadosOcorrencias as $ocorrencia){

//            $dataRegistro = \DateTime::createFromFormat('d/m/Y H:i:s',$ocorrencia->getDataHora());
//            $startDate = \DateTime::createFromFormat('d/m/Y H:i:s','02/12/2016 00:00:00');
//            if($dataRegistro >= $startDate){
//                $tratado[] = $ocorrencia->toArray();
//            }
            $tratado[] = $ocorrencia->toArray();

        }
        $dadosOcorrencias = $tratado;

        /**
         * rm-abstencao-participantes-uf
         */
        $config = new UltimaMilha();
        $config->load();

        $abstencoes = $config->getAbstencoes();
        $this->updateFile('rm-abstencao-participantes-uf', $abstencoes);

        /**
         * risk-tratamento-alerta-detalhamento
         */
        $event = new Event();
        $totalRiskAlerta = [];
        foreach($dados as $item){
            $categoria = $item['customAttributes']['categoria_evento'];
            $dadosCategoria = explode(' - ', $categoria);
            $categoria = $dadosCategoria[0];

            $data = $event->epocToDate($item['created'],'H:i d/m/Y');

            $origem = $item['customAttributes']['origem_informacao'];
            $municipio = explode(' - ',$item['customAttributes']['municipio_de_aplicacao']);
            $uf = $municipio[0];

            $nivel = $item['customAttributes']['nivel_do_alerta_sgir'];
            $dadosNivel = explode(' - ', $nivel);
            $nivel = $dadosNivel[1];

            $dadosFile = [];
            $dadosFile['tipo'] = $categoria;
            $dadosFile['data'] = $data;
            $dadosFile['descricao'] = $item['description'];
            $dadosFile['uf'] = $uf;
            $dadosFile['classificacao'] = $nivel;
            $dadosFile['coordenacao'] = (isset($item['customAttributes']['coordenacao'])) ? $item['customAttributes']['coordenacao'] : '';
            $dadosFile['situacao'] = $item['customAttributes']['status_tratamento'];
            $dadosFile['municipio'] = (isset($municipio[1])) ? $municipio[1] : '';
            $dadosFile['usuario'] = $item['customAttributes']['usuario_cadastro'];

            $totalRiskAlerta[] = $dadosFile;
        }
        krsort($totalRiskAlerta);
        $retornoAlertas = [];
        foreach(array_values($totalRiskAlerta) as $item){
            if(count($retornoAlertas) < 200){
                $retornoAlertas[] = $item;
            }else{
                break;
            }
        }
        $totalRiskAlerta = $retornoAlertas;
        $this->updateFile('risk-tratamento-alerta-detalhamento', $totalRiskAlerta);

        /**
         * risk-ocorrencias-tempo-detalhamento
         */
        $totalRiskAlerta = [];
        $label = ['Arquivado','Triagem','Enviado para Alerta',3=>''];

        foreach($dadosOcorrencias as $item){
            $date = \DateTime::createFromFormat('d/m/Y H:i:s', $item['DataHora']);

            $municipio = explode(' - ',$item['Municipio']);


            $dadosFile = [];
            $dadosFile['tipo'] = $item['Categoria'];
            $dadosFile['data'] = ($date) ? $date->format('H:i d/m/Y') : '';
            $dadosFile['descricao'] = $item['Descricao'];
            $dadosFile['uf'] = $item['Uf'];
            $dadosFile['coordenacao'] = $item['Coordenacao'];
            $dadosFile['situacao'] = $label[$item['Status']];
            $dadosFile['municipio'] = $municipio[1];
            $dadosFile['usuario'] = $item['Usuario'];


            $totalRiskAlerta[$dadosFile['data']] = $dadosFile;
        }
        krsort($totalRiskAlerta);
        $retornoAlertas = [];
        foreach(array_values($totalRiskAlerta) as $item){
            if(count($retornoAlertas) < 200){
                $retornoAlertas[] = $item;
            }else{
                break;
            }
        }
        $totalRiskAlerta = $retornoAlertas;
        $this->updateFile('risk-ocorrencias-tempo-detalhamento', $totalRiskAlerta);

        /**
         * rm-entrega-malotes-uf
         */
        $milha = $config->getMilha();
        $this->updateFile('rm-entrega-malotes-uf', $milha);

        /**
         * geral-rm-entrega-malotes
         */
        $geralMalote = $config->getMilhaGeral();
        $this->updateFile('geral-rm-entrega-malotes', $geralMalote);

        /**
         * geral-rm-abstencao-participantes
         */
        $geralAbstencao = $config->getAbstencaoGeral();
        $this->updateFile('geral-rm-abstencao-participantes', $geralAbstencao);

        /**
         * risk-alertas-origem
         */
        $totalRiskAlerta = [];
        foreach($dados as $item){
            $origem = $item['customAttributes']['origem_informacao'];
            if(!isset($totalRiskAlerta[$origem])) $totalRiskAlerta[$origem] = 0;
            $totalRiskAlerta[$origem]++;
        }

        $totalRiskFile = [];
        foreach($totalRiskAlerta as $origem => $valor){
            $totalRiskFile[] = ['descricao'=>$origem,'valor'=>$valor];
        }
        $this->updateFile('risk-alertas-origem', $totalRiskFile);

        /**
         * risk-alertas-origem-uf
         */
        $totalRiskAlerta = [];
        foreach($dados as $item){
            $origem = $item['customAttributes']['origem_informacao'];
            $municipio = explode(' - ',$item['customAttributes']['municipio_de_aplicacao']);
            $uf = $municipio[0];

            if(!isset($totalRiskAlerta[$uf])) $totalRiskAlerta[$uf] = 0;
            $totalRiskAlerta[$uf]++;
        }

        $totalRiskFile = [];
        foreach($totalRiskAlerta as $origem => $valor){
            $desct = ['descricao'=>$origem,'valor'=>$valor];
            /*foreach($valor as $tipo => $valores){
                $desct['valores'][] = ['descricao'=>$tipo,'valor'=>$valores];
            }*/
            $totalRiskFile[] = $desct;
        }
        $this->updateFile('risk-alertas-origem-uf', $totalRiskFile);

        /**
         * risk-alertas-origem-detalhamento
         */
        $totalRiskAlerta = [];
        foreach($dados as $item){
            $origem = $item['customAttributes']['origem_informacao'];
            $municipio = explode(' - ',$item['customAttributes']['municipio_de_aplicacao']);
            $uf = $municipio[0];

            if(!isset($totalRiskAlerta[$uf][$origem])) $totalRiskAlerta[$uf][$origem] = 0;
            $totalRiskAlerta[$uf][$origem]++;
        }

        $totalRiskFile = [];
        foreach($totalRiskAlerta as $origem => $valor){
            $desct = ['descricao'=>$origem];
            foreach($valor as $tipo => $valores){
                $desct['valores'][] = ['descricao'=>$tipo,'valor'=>$valores];
            }
            $totalRiskFile[] = $desct;
        }
        $this->updateFile('risk-alertas-origem-detalhamento', $totalRiskFile);

        /**
         * risk-ocorrencias-origem-detalhamento
         */
        $totalRiskAlerta = [];
        foreach($dadosOcorrencias as $item){
            $origem = $item['OrigemInformacao'];
            $uf = $item['Uf'];

            if(!isset($totalRiskAlerta[$uf][$origem])) $totalRiskAlerta[$uf][$origem] = 0;
            $totalRiskAlerta[$uf][$origem]++;
        }

        $totalRiskFile = [];
        foreach($totalRiskAlerta as $origem => $valor){
            $desct = ['descricao'=>$origem];
            foreach($valor as $tipo => $valores){
                $desct['valores'][] = ['descricao'=>$tipo,'valor'=>$valores];
            }
            $totalRiskFile[] = $desct;
        }
        $this->updateFile('risk-ocorrencias-origem-detalhamento', $totalRiskFile);

        /**
         * risk-ocorrencias-situacao-detalhamento
         */
        $totalRiskAlerta = [];
        $label = ['Arquivado','Triagem','Enviado para Alerta',3=>''];
        foreach($dadosOcorrencias as $item){
            if($item['Status'] == 1) continue;

            $origem = $label[$item['Status']];
            $uf = $item['Uf'];

            if(!$uf) continue;

            if(!isset($totalRiskAlerta[$uf][$origem])) $totalRiskAlerta[$uf][$origem] = 0;
            $totalRiskAlerta[$uf][$origem]++;
        }

        $totalRiskFile = [];
        foreach($totalRiskAlerta as $origem => $valor){
            $desct = ['descricao'=>$origem];
            foreach($valor as $tipo => $valores){
                $desct['valores'][] = ['descricao'=>$tipo,'valor'=>$valores];
            }
            $totalRiskFile[] = $desct;
        }
        $this->updateFile('risk-ocorrencias-situacao-detalhamento', $totalRiskFile);

        /**
         * risk-nivel-alertas
         */
        $totalRiskAlerta = [];
        foreach($dados as $item){
            $nivel = $item['customAttributes']['nivel_do_alerta_sgir'];
            $dadosNivel = explode(' - ', $nivel);
            $nivel = $dadosNivel[1];

            if(!isset($totalRiskAlerta[$nivel])) $totalRiskAlerta[$nivel] = 0;
            $totalRiskAlerta[$nivel]++;
        }

        $totalRiskFile = [];
        foreach($totalRiskAlerta as $origem => $valor){
            $totalRiskFile[] = ['descricao'=>$origem,'valor'=>$valor];
        }
        $this->updateFile('risk-nivel-alertas', $totalRiskFile);

        /**
         * risk-ocorrencias-categoria
         */
        $totalRiskAlerta = [];
        foreach($dadosOcorrencias as $item){
            $categoria = $item['Categoria'];

            if(!isset($totalRiskAlerta[$categoria])) $totalRiskAlerta[$categoria] = 0;
            $totalRiskAlerta[$categoria]++;
        }

        $totalRiskFile = [];
        foreach($totalRiskAlerta as $origem => $valor){
            $totalRiskFile[] = ['descricao'=>$origem,'valor'=>$valor];
        }
        $this->updateFile('risk-ocorrencias-categoria', $totalRiskFile);

        /**
         * geral-rm-alertas-categoria
        */
        $totalRiskAlerta = [];
        foreach($dados as $item){
        $categoria = $item['customAttributes']['categoria_evento'];
        $dadosCategoria = explode(' - ', $categoria);
        $categoria = $dadosCategoria[0];

        if(!isset($totalRiskAlerta[$categoria])) $totalRiskAlerta[$categoria] = 0;
        $totalRiskAlerta[$categoria]++;
        }

        $totalRiskFile = [];
        foreach($totalRiskAlerta as $origem => $valor){
        $totalRiskFile[] = ['descricao'=>$origem,'valor'=>$valor];
        }
        $this->updateFile('geral-rm-alertas-categoria', $totalRiskFile);

        /**
         * geral-rm-alertas-categoria (OLD)

        $totalRiskAlerta = [];
        foreach($dados as $item){
            $categoria = $item['customAttributes']['categoria_evento'];
            $dadosCategoria = explode(' - ', $categoria);
            $categoria = $dadosCategoria[0];

            if(!isset($totalRiskAlerta[$categoria])) $totalRiskAlerta[$categoria] = 0;
            $totalRiskAlerta[$categoria]++;
        }

        $totalRiskFile = [];
        foreach($totalRiskAlerta as $origem => $valor){
            $totalRiskFile[] = ['descricao'=>$origem,'valor'=>$valor];
        }
        $this->updateFile('geral-rm-alertas-categoria', $totalRiskFile);

        /**
         * risk-ocorrencias-origem
         */
        $totalRiskAlerta = [];
        foreach($dadosOcorrencias as $item){
            $origem = $item['OrigemInformacao'];
            if(!isset($totalRiskAlerta[$origem])) $totalRiskAlerta[$origem] = 0;
            $totalRiskAlerta[$origem]++;
        }

        $totalRiskFile = [];
        foreach($totalRiskAlerta as $origem => $valor){
            $totalRiskFile[] = ['descricao'=>$origem,'valor'=>$valor];
        }
        $this->updateFile('risk-ocorrencias-origem', $totalRiskFile);

        /**
         * risk-ocorrencias-situacao
         */
        $totalRiskAlerta = [];
        foreach($dadosOcorrencias as $item){
            $origem = $item['Status'];
            if(!isset($totalRiskAlerta[$origem])) $totalRiskAlerta[$origem] = 0;
            $totalRiskAlerta[$origem]++;
        }

        $label = ['Arquivado','Triagem','Enviado para Alerta'];
        $totalRiskFile = [];
        foreach($totalRiskAlerta as $origem => $valor){
            if(!isset($label[$origem])) continue;
            $totalRiskFile[] = ['descricao'=>$label[$origem],'valor'=>$valor];
        }
        $this->updateFile('risk-ocorrencias-situacao', $totalRiskFile);


        /**
         * risk-ocorrencias-uf
         */
        $totalRiskAlerta = [];
        foreach($dadosOcorrencias as $item){
            $origem = $item['OrigemInformacao'];
            $uf = $item['Uf'];

            if(!isset($totalRiskAlerta[$uf][$origem])) $totalRiskAlerta[$uf][$origem] = 0;
            $totalRiskAlerta[$uf][$origem]++;
        }

        $totalRiskFile = [];
        foreach($totalRiskAlerta as $origem => $valor){
            $desct = ['descricao'=>$origem];
            foreach($valor as $tipo => $valores){
                $desct['valores'][] = ['descricao'=>$tipo,'valor'=>$valores];
            }
            $totalRiskFile[] = $desct;
        }
        $this->updateFile('risk-ocorrencias-uf', $totalRiskFile);

        /**
         * risk-ocorrencias-consolidado-uf
         */
        $totalRiskAlerta = [];
        foreach($dadosOcorrencias as $item){
            $uf = $item['Uf'];

            if(!isset($totalRiskAlerta[$uf])) $totalRiskAlerta[$uf] = 0;
            $totalRiskAlerta[$uf]++;
        }

        $totalRiskFile = [];
        foreach($totalRiskAlerta as $origem => $valor){
            $desct = ['descricao'=>$origem,'valor'=>$valor];
            $totalRiskFile[] = $desct;
        }
        $this->updateFile('risk-ocorrencias-consolidado-uf', $totalRiskFile);

        /**
         * risk-alertas-consolidado-uf
         */
        $totalRiskAlerta = [];
        foreach($dados as $item){
            $municipio = explode(' - ',$item['customAttributes']['municipio_de_aplicacao']);
            $uf = $municipio[0];

            if(!isset($totalRiskAlerta[$uf])) $totalRiskAlerta[$uf] = 0;
            $totalRiskAlerta[$uf]++;
        }

        $totalRiskFile = [];
        foreach($totalRiskAlerta as $origem => $valor){
            $desct = ['descricao'=>$origem,'valor'=>$valor];
            $totalRiskFile[] = $desct;
        }
        $this->updateFile('risk-alertas-consolidado-uf', $totalRiskFile);

        /**
         * risk-tratamento-alerta
         */
        $totalRiskAlerta = [];
        foreach($dados as $item){
            $categoria = $item['customAttributes']['status_tratamento'];

            if(!isset($totalRiskAlerta[$categoria])) $totalRiskAlerta[$categoria] = 0;
            $totalRiskAlerta[$categoria]++;
        }

        $totalRiskFile = [];
        foreach($totalRiskAlerta as $origem => $valor){
            $totalRiskFile[] = ['descricao'=>$origem,'valor'=>$valor];
        }
        $this->updateFile('risk-tratamento-alerta', $totalRiskFile);

        /**
         * risk-ocorrencias-tempo
         */
        $riskOcorrenciasTempo = [];

        foreach ($dadosOcorrencias as $dado) {
            $date = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $dado['DataHora']))));
            if($date->diff(new \DateTime())->days > 0) continue;

            $aux = explode(':',$date->format('H:i'));
            $minutos = ($aux[0]*60)+$aux[1];

            $tempoInicioInMinutos = 0;
            $tempoProximoInMinutos = $tempoInicioInMinutos+30;
            $varrer=true;
            while($varrer){

                if($minutos >= $tempoInicioInMinutos && $minutos <= $tempoProximoInMinutos){
                    $auxFormato = $tempoInicioInMinutos/60;
                    if(is_float($auxFormato)){
                        $hora = explode('.',$auxFormato);
                        $auxFormato = str_pad($hora[0],2,0,STR_PAD_LEFT).':'.'30';
                    }else{
                        $auxFormato = str_pad($auxFormato,2,0,STR_PAD_LEFT).':'.'00';
                    }
                    @$riskOcorrenciasTempo[$auxFormato]++;
                    $varrer = false;
                }else{
                    $tempoInicioInMinutos = $tempoProximoInMinutos;
                    $tempoProximoInMinutos += 30;
                }

            }

        }
        ksort($riskOcorrenciasTempo);
        $riskOcorrenciasTempoTratados = [];
        foreach ($riskOcorrenciasTempo as $key => $value) {
            $riskOcorrenciasTempoTratados[] = ['descricao'=>$key,'valor'=>$value];
        }
        $this->updateFile('risk-ocorrencias-tempo', $riskOcorrenciasTempoTratados);

        /**
         * risk-triagem-categoria
         */
        $triagem = [];
        foreach ($dadosOcorrencias as $dado) {
            if(!isset($triagem[$dado['Categoria']])) $triagem[$dado['Categoria']] = [0,0];
            if($dado['Status'] == 1){
                $triagem[$dado['Categoria']][0]++;
            }else{
                $triagem[$dado['Categoria']][1]++;
            }
        }

        $triagemFile = [];
        foreach($triagem as $categoria => $valor){
            $triagemFile[] = ['descricao'=>$categoria,'valores'=>$valor];
        }
        $this->updateFile('risk-triagem-categoria', $triagemFile);

        /**
         * Total Registros
         */
//        $alertasCount = 0;
//        foreach($dados as $item){
//            if($item['status'] != 'Aberto' || $item['status'] != 'Fechado') continue;
//            if($item['customAttributes']['sistema'] != 'Enem') continue;
//            if(!$item['customAttributes']['dia_da_aplicacao']) continue;
//            $alertasCount++;
//        }

        $ocorrenciasCount = 0;
        foreach($dadosOcorrencias as $item){
//            if($item['Status'] != 1) continue;
            $ocorrenciasCount++;
        }

        $alertasCount = 0;
        foreach($dadosOcorrencias as $item){
            if($item['Status'] == 2) continue;
            $alertasCount++;
        }

        $this->updateFile('risk-total-registros', ['alertas'=>$alertasCount,'ocorrencias'=>$ocorrenciasCount]);

        echo 'Feito';
        die;
    }

    private function updateFile($arquivo, $data){
        if(APPLICATION_ENV == 'production'){
//            $file = 'Y:\\'.$arquivo.'.json';
            $file = '\\\DOVERLANDIA\cmi\\'.$arquivo.'.json';
        }else{
            $file = './public/json_api/'.$arquivo.'.json';
        }
        $json = ['error'=>false,'details'=>'Solicitação do Serviço Executada com Sucesso','data'=>$data,'datetime'=>date('Y-m-d H:i:s')];
        file_put_contents($file, json_encode($json));
        echo 'Dados do arquivo '.$arquivo.' Atualizado com sucesso'.PHP_EOL;
    }

    public function tirarAcentos($string){
        $string = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
        $string = str_replace(['´','\'','ç','Ç'],[' ',' ', 'c','C'],$string);
        if($string == 'PA - SANTA IZABEL DO PARA') $string = 'PA - SANTA ISABEL DO PARA';
        if($string == 'SP - MOJI MIRIM') $string = 'SP - MOGI-MIRIM';
        if($string == 'AL - OLHO D AGUA DAS FLORES') $string = 'AL - OLHO DAGUA DAS FLORES';

        //$string = str_replace('´',' ',$string);

        return $string;
    }

    public function categoriasAction(){
        header("Content-type:application/json; charset=utf-8");
        $categoria = new Categoria();
        return $this->apiView->successReturn($categoria->getChaves(),'Lista de categorias gerada com sucesso');
    }

    public function categoriasGroupAction(){
        header("Content-type:application/json");
        $objCategoria = new Categoria();
        $tratado = [];
        $keys = [];
        foreach($objCategoria->getChaves() as $categoria){
            $categoria['valor'] = ($categoria['valor']);
            $keys[$categoria['valor']] = $categoria['codigo'];

            if(preg_match('/-/',$categoria['valor'])){
                $exp = explode(' - ',$categoria['valor']);
                if(!isset($tratado[$exp[0]])) $tratado[$exp[0]] = [];
                $tratado[$exp[0]][] = $exp[1];
            }else{
                $tratado[$categoria['valor']][] = $categoria['valor'];
            }
        }

        $retorno = [];
        $byHash = [];
        foreach ($tratado as $pai => $sub){
            $laco = ['nome'=>$pai,'hash'=>md5($pai)];
            $laco['subcategorias'] = [];
            foreach($sub as $cat){
                if($cat == $pai){
                    $codigo = $keys[$pai];
                    $laco['subcategorias'][] = [
                        'nome'=>$pai,
                        'codigo'=>$codigo
                    ];
                }else{
                    $codigo = $keys[$pai.' - '.$cat];
                    $laco['subcategorias'][] = [
                        'nome'=>$cat,
                        'codigo'=>$codigo
                    ];
                }
            }
            $byHash[$laco['hash']] = $laco['subcategorias'];
            $retorno[] = $laco;
        }

        if($this->params('id')){
            if(isset($byHash[$this->params('id')])){
                echo json_encode($byHash[$this->params('id')]);
            }else{
                echo json_encode(['Hash não encontrado']);
            }
            die;
        }

        echo json_encode($retorno);
        die;
    }

    public function getProgressAction(){
        header("Content-type:application/json");
        $post = json_decode($this->getRequest()->getContent(), true);
        $last = \DateTime::createFromFormat('Y-m-d H:i:s',$post['last']);
        $progresso = [];
        foreach($post['ids'] as $code){
            try{
                $event = new Event();
                $event->setCode($code);
                $event->setAnonimo();
                $history = $event->getProgressHistory();
                $historico = [];
                foreach($history as $his){
                    $his = json_decode(json_encode($his), true);
                    if($his['Comment']){
                        $dateTime = \DateTime::createFromFormat('d/m/Y H:i:s',$event->epocToDate($his['Date'], 'd/m/Y H:i:s'));
                        if($dateTime >= $last){
                            $historico[] = ['descricao'=>$his['Comment'],'nome_usuario'=>$his['UpdatedBy'],'data'=>$dateTime->format('d/m/Y H:i:s')];
                        }
                    }
                }
                $progresso[$code][] = $historico;
            }catch (\Exception $e){

            }
        }

        $return = [];
        $return['data'] = $progresso;
        $return['message'] = 'Progresso gerado com sucesso';
        $return['error'] = false;
        $return['datetime'] = date('Y-m-d H:i:s');

        echo json_encode($return);
        die;
    }
}
