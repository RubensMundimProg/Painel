<?php

namespace Application\Controller;

use Application\Form\Relatorios;
use Application\Service\Charts;
use DOMPDFModule\View\Model\PdfModel;
use Estrutura\Controller\AbstractEstruturaController;
use RiskManager\Workflow\Service\Event;
use Zend\Db\Sql\Ddl\Column\Char;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class RelatoriosController extends AbstractEstruturaController{

    public function indexAction()
    {
        $filtroSistema = new Container('SistemaSelecionado');
        $sistema = $filtroSistema->offsetGet('sistema');

        if(!$sistema){
            $this->addErrorMessage('Selecione a avaliação pedagógica');
            return $this->redirect()->toUrl('/');
        }

        $form = new Relatorios();
        return new ViewModel(['form'=>$form,'sistema'=>$sistema]);
    }

    public function gerarAction()
    {
        try{
            $request = $this->getRequest();
            $post = $request->getPost()->toArray();

            $sistema = new \Zend\Session\Container('SistemaSelecionado');
            $avaliacaoPedagogica = $sistema->offsetGet('sistema');

            if(!$avaliacaoPedagogica){
                $this->addErrorMessage('Favor selecione a avaliação pedagógica');
                return $this->setRedirect('/relatorios', $post);
            }

            if($request->isPost() && isset($post['uf']) && isset($post['dataInicio']) && isset($post['dataFim'])) {
                if($post['categoria'] == 'Selecione') $post['categoria'] = '';
                if($post['categoria'] == 'Todas') $post['categoria'] = '';


                $form = new Relatorios();

                $form->setData($post);
                if (!$form->isValid()) {
                    $this->addValidateMessages($form);
                    return $this->setRedirect('/relatorios', $post);
                }

                $ufSelecionada = $post['uf'];



                $dataSelecionadaPedacos = explode('/',$post['dataInicio']);
                $diaInicioSelecionado = new \DateTime($dataSelecionadaPedacos[2].'-'.$dataSelecionadaPedacos[1].'-'.$dataSelecionadaPedacos[0]);
                $dataSelecionadaPedacos = explode('/',$post['dataFim']);
                $diaFimSelecionado = new \DateTime($dataSelecionadaPedacos[2].'-'.$dataSelecionadaPedacos[1].'-'.$dataSelecionadaPedacos[0]);

                if($post['horaInicio'] == '') $post['horaInicio'] = '00:00';
                if($post['horaFim'] == '') $post['horaFim'] = '23:59';

                $diaInicioSelecionado = \DateTime::createFromFormat('d/m/Y H:i', $post['dataInicio'].' '.$post['horaInicio']);
                $diaFimSelecionado = \DateTime::createFromFormat('d/m/Y H:i', $post['dataFim'].' '.$post['horaFim']);

                $categoriaSelecionada = (isset($post['categoria']))?$post['categoria']:'';
                $categoriaSelecionada = ($categoriaSelecionada && isset($post['subcategoria']) && $post['subcategoria'] != '')?$categoriaSelecionada.' - '.$post['subcategoria']:$categoriaSelecionada;

                set_time_limit(0);
                ini_set('MAX_EXECUTION_TIME', -1);
                ini_set('memory_limit','10000M');

                $data = json_decode(file_get_contents('./data/cache/validados.json'),true);

                // BUILD TABLES AND CHARTS
                $tabelaCategoria = [];
                $tabelaCategoriaSubcategoria = [];
                $tabelaSegurancaPublica = [];
                $graficoAlertasPorEstadoDados = [];
                $graficoAlertasPorEstadoDadosRM = [];
                $graficoAlertasPorEstadoDadosRNC = [];
                $graficoAlertasPorEstado = '';
                $tabelaAlertasRegistrados = [];

                //EPOC TO DATE
                $service = new Event();

                // MODELANDO OS DADOS
                foreach ($data as $item) {

                    //LIMA OS REGISTROS QUE NÃO POSSUEM CATEGORIA OU LOCALIDADE
                    if(!$item['customAttributes']['categoria_evento'] || !$item['customAttributes']['municipio_de_aplicacao']) continue;
                    //PEGA OS REGISTROS DE UM DIA DETERMINADO
                    $dtCreated = $service->epocToDate($item['created'],'Y-m-d H:i');
                   // $dtCreated = '2019-11-30 17:30';
                    $created = new \DateTime($dtCreated);
                    if (($created >= $diaInicioSelecionado) && ($created <= $diaFimSelecionado)){
                        if(strtolower($avaliacaoPedagogica) != strtolower($item['customAttributes']['sistema'])) continue;

                        $local = explode(' - ',$item['customAttributes']['municipio_de_aplicacao']);
                        if(trim($local[0]) != $ufSelecionada && $ufSelecionada != '') continue;

//                        if($categoriaSelecionada && ($categoriaSelecionada != $item['customAttributes']['categoria_evento'])) continue; //old one
                        if($categoriaSelecionada && (!preg_match("/{$categoriaSelecionada}/",$item['customAttributes']['categoria_evento']))) continue;

                        //TABELA CATEGORIA/SUBCATEGORIA
                        @$tabelaCategoriaSubcategoria[$item['customAttributes']['categoria_evento']]++;


                        if(preg_match('/ - /',$item['customAttributes']['categoria_evento'])){
                            $categoria = explode(' - ',$item['customAttributes']['categoria_evento']);
                        }else{
                            $categoria[0] = $item['customAttributes']['categoria_evento'];
                        }

                        // TABELA CATEGORIAS
                        @$tabelaCategoria[$categoria[0]]++;

                        // TABELA CATEGORIAS SEGURANÇA PÚBLICA
                        if($categoria[0] == 'Segurança Pública'){
                            @$tabelaSegurancaPublica[$categoria[1]]++;
                        }

                        //ALERTAS POR ESTADOS
                        $uf = trim($local[0]);
                        $municipio = trim($local[1]);
                        @$graficoAlertasPorEstadoDados[$uf]++;

                        if($item['customAttributes']['origem_informacao'] == 'RM'){
                            @$graficoAlertasPorEstadoDadosRM[$uf]++;
                        }
                        if($item['customAttributes']['origem_informacao'] == 'RNC'){
                            @$graficoAlertasPorEstadoDadosRNC[$uf]++;
                        }

                        //ALERTAS
                        $createdDate = $service->epocToDate($item['created'],'d/m/Y H:i:s');
                        $description = rtrim($item['description'],' INFORMAÇÕES ADICIONAIS:');
                        $diaDaAplicacao = (isset($item['customAttributes']['dia_da_aplicacao']))?$item['customAttributes']['dia_da_aplicacao']:'';
                        $tabelaAlertasRegistrados[$uf.' - '.$municipio.' - '.$createdDate.' - '.$diaDaAplicacao] = ['dia_da_aplicacao'=>$diaDaAplicacao,'created'=>$createdDate,'title'=>$item['title'],'categoria_evento'=>$item['customAttributes']['categoria_evento'],'municipio_de_aplicacao'=>$item['customAttributes']['municipio_de_aplicacao'],'origem_informacao'=>$item['customAttributes']['origem_informacao'],'description'=>$description,'anexo'=>$item['anexo']];

                    }
                }

                if(count($tabelaAlertasRegistrados) == 0){
                    $this->addErrorMessage('Não há registro para o filtro selecionado!');
                    return $this->setRedirect('/relatorios', $post);
                }

                // ORDENAÇÃO DAS TABELAS
                ksort($tabelaCategoria);
                ksort($tabelaCategoriaSubcategoria);
                arsort($tabelaSegurancaPublica);
                ksort($graficoAlertasPorEstadoDados);
                ksort($graficoAlertasPorEstadoDadosRM);
                ksort($graficoAlertasPorEstadoDadosRNC);
                ksort($tabelaAlertasRegistrados);

                //GERA GRÁFICO DE CATEGORIAS
                $graficoCategoriaSubtitle = array_keys($tabelaCategoria);
                $graficoCategoriaValue = array_values($tabelaCategoria);
                $graficoCategoria = new Charts('Bar',$graficoCategoriaValue,'chartCategoria'.$ufSelecionada,$graficoCategoriaSubtitle);
                $graficoCategoria = $graficoCategoria->build();

                // GERA GRÁFICO ALERTAS POR ESTADO GERAL
                $graficoAlertasPorEstadoSubtitle = array_keys($graficoAlertasPorEstadoDados);
                $graficoAlertasPorEstadoValue = array_values($graficoAlertasPorEstadoDados);
                $graficoAlertasPorEstado = new Charts('Bar',$graficoAlertasPorEstadoValue,'chart'.$ufSelecionada,$graficoAlertasPorEstadoSubtitle);
                $graficoAlertasPorEstado = $graficoAlertasPorEstado->build();

                // GERA GRÁFICO ALERTAS POR ESTADO RM
                $graficoAlertasPorEstadoSubtitleRM = array_keys($graficoAlertasPorEstadoDadosRM);
                $graficoAlertasPorEstadoValueRM = array_values($graficoAlertasPorEstadoDadosRM);
                $graficoAlertasPorEstadoRM = new Charts('Bar',$graficoAlertasPorEstadoValueRM,'chart'.$ufSelecionada.'RM',$graficoAlertasPorEstadoSubtitleRM);
                $graficoAlertasPorEstadoRM = $graficoAlertasPorEstadoRM->build();

                // GERA GRÁFICO ALERTAS POR ESTADO RNC
                $graficoAlertasPorEstadoSubtitleRNC = array_keys($graficoAlertasPorEstadoDadosRNC);
                $graficoAlertasPorEstadoValueRNC = array_values($graficoAlertasPorEstadoDadosRNC);
                $graficoAlertasPorEstadoRNC = new Charts('Bar',$graficoAlertasPorEstadoValueRNC,'chart'.$ufSelecionada.'RNC',$graficoAlertasPorEstadoSubtitleRNC);
                $graficoAlertasPorEstadoRNC = $graficoAlertasPorEstadoRNC->build();

                $tratado = [];
                foreach($tabelaAlertasRegistrados as $item){
                    if(count($tratado) >= 100) continue;
                    $tratado[] = $item;
                }
                $tabelaAlertasRegistrados = $tratado;

                //DEFININDO DATA QUE APARECERÁ NO RELATÓRIO
                $dataRelatorio = ($diaInicioSelecionado == $diaFimSelecionado)?$diaInicioSelecionado->format('d/m/Y'):$diaInicioSelecionado->format('d/m/Y').' à '.$diaFimSelecionado->format('d/m/Y');
                $pdfJsonArray = [
                    'versaoRelatorio' => '1.0',
                    'logoCliente' => BASE_PATCH.'/public/assets/img/logo/inep-logo.png',//alterar aqui.
                    'logoModulo' => BASE_PATCH.'/public/assets/img/logo/logo-nova.png',
                    'tabelaCategoria' => $tabelaCategoria,
                    'graficoCategoria' => $graficoCategoria,
                    'tabelaCategoriaSubcategoria' => $tabelaCategoriaSubcategoria,
                    'tabelaSegurancaPublica' => $tabelaSegurancaPublica,
                    'graficoAlertasPorEstado' => $graficoAlertasPorEstado,
                    'graficoAlertasPorEstadoRM' => $graficoAlertasPorEstadoRM,
                    'graficoAlertasPorEstadoRNC' => $graficoAlertasPorEstadoRNC,
                    'tabelaAlertasRegistrados' => $tabelaAlertasRegistrados,
                    'ufSelecionada' => $ufSelecionada,
                    'dataSelecionada' => $dataRelatorio,
                    'categoriaSelecionada' => $categoriaSelecionada,
                    'avaliacaoPedagogica' => strtoupper($avaliacaoPedagogica)

                ];

                $name = ($ufSelecionada == '')?'Relatório':'Relatório '.$ufSelecionada;
                $fileName = $name.' '.$avaliacaoPedagogica.'.pdf';
                if(!preg_match('/.pdf/',$fileName)) $fileName .= '.pdf';
                $this->layout('layout/limpo');
                $pdf = new PdfModel();
                $pdf->setOption('paperSize', 'A4');
                $pdf->setOption('paperOrientation', 'landscape');
                $pdf->setOption("filename", $fileName);
                $pdf->setOption("enable_remote", true);

//                 $pdf = new ViewModel($pdfJsonArray);
                $pdf->setVariables($pdfJsonArray);

                return $pdf;

            }else{
                $this->addErrorMessage('Favor preencher o formulário corretamente.');
                return $this->setRedirect('/relatorios', $post);
            }

        }catch (\Exception $e) {
            $this->addErrorMessage($e->getMessage());
            return $this->setRedirect('/relatorios', $post);
        }

    }

}
