<?php

namespace Dashboard\Controller;

use Application\Service\UltimaMilha;
use Application\Service\WriteFile;
use Classes\Service\Acesso;
use Classes\Service\Alertas;
use Classes\Service\Atividade;
use Classes\Service\Aviso;
use Dashboard\Form\Dashboard;
use Estrutura\Controller\AbstractEstruturaController;
use Modulo\Service\RiskManager;
use Modulo\Service\UsuarioApi;
use RiskManager\OData\Filter;
use RiskManager\Organization\Service\Asset;
use RiskManager\Workflow\Service\Attributes;
use RiskManager\Workflow\Service\Event;
use RiskManager\Workflow\Service\Queries;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractEstruturaController
{

    public $showConsole = true;

    public function indexAction()
    {
        $container = new Container('SistemaSelecionado');
        $sistema = $container->offsetGet('sistema');

        if (!$sistema) {
            $this->addErrorMessage('Favor selecione a avaliação pedagógica');
            return $this->redirect()->toUrl('/');
        }

        $form = new Dashboard();

        $usuarioApi = new UsuarioApi();
        $grupos = $usuarioApi->get('perfis');

        return new ViewModel(['form' => $form, 'isAdmin' => in_array('Administrador INEP', $grupos)]);
    }

    public function getDashboardJsonAction()
    {
        $request = $this->getRequest();
        $json = '';
        if ($request->isPost()) {
            $post = $request->getPost()->toArray();

            if (count($post)) {

                $container = new Container('SistemaSelecionado');
                $sistema = $container->offsetGet('sistema');

                $filename = 'dashboard_' . $sistema;

                if ($post['start-date'] && $post['end-date']) {
                    $filename .= '_' . str_replace('/', '-', implode('_', [$post['start-date'], $post['end-date']]));
                }

                if (!file_exists('./data/json/' . $filename . '.json')) {
                    $json = $this->buildDashboardDataAction($post, $filename);
                } else {
                    $time = filemtime('./data/json/' . $filename . '.json');
                    $created = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', $time));
                    $now = new \DateTime();
                    $diff = $created->diff($now);
                    if ($diff->days > 0 || $diff->h > 0 || $diff->i > 30) {
                        $json = $this->buildDashboardDataAction($post, $filename);
                    } else {
                        $json = true;
                    }
                }

                if (!$json) {
                    $json = json_encode(['error' => true, 'message' => 'Não há massa de dados para o período e avaliação pedagógica selecionados.', 'dados' => []]);
                } else {
                    $update = filemtime('./data/json/' . $filename . '.json');
                    $update = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', $update));
                    $json = json_encode(['error' => false, 'message' => '', 'dados' => file_get_contents('./data/json/' . $filename . '.json'), 'update' => $update->format('d/m/Y H:i:s')]);
                }

            } else {
                $container = new Container('SistemaSelecionado');
                $sistema = $container->offsetGet('sistema');

                if (file_exists("./data/json/dashboard_{$sistema}.json")) {
                    $json = json_encode(['error' => false, 'message' => '', 'dados' => file_get_contents("./data/json/dashboard_{$sistema}.json")]);
                } else {
                    $json = json_encode(['error' => true, 'message' => 'Não há massa de dados para o período e avaliação pedagógica selecionados.', 'dados' => []]);
                }
            }

        }
        echo $json;
        die;
    }

    public function buildDashboardDataAction($period = null, $filename = null)
    {
        if ($period) {
            $startDatePieces = explode('/', $period['start-date']);
            $startDate = new \DateTime($startDatePieces[2] . '-' . $startDatePieces[1] . '-' . $startDatePieces[0]);
            $endDatePieces = explode('/', $period['end-date']);
            $endDate = new \DateTime($endDatePieces[2] . '-' . $endDatePieces[1] . '-' . $endDatePieces[0]);

            $period = true;

            $this->showConsole = false;
        }

        $service = new Event();
        $today = new \DateTime(date('Y-m-d'));

        $serviceEvent = new Event();

        $hoje = new \DateTime();

        $this->consoleLog("Carregando consulta do Workflow...");

        $querie = new Queries();
        $querie->setAnonimo();
        $querie->setId('58c1665e-fd66-49e5-a4da-454341e72575');
        $filter = new Filter();
        $filter->setPageSize(1000);
        $querie->setFilter($filter);
        $eventos = $querie->fetchAll();

        $eventosAgrupados = $eventos;

        $page = 2;
        while (count($eventos) == 1000) {
            $this->consoleLog('página ' . $page . ' de eventos...');
            $filter->setPage($page);
            $querie->setFilter($filter);
            $eventos = $querie->fetchAll();
            $eventosAgrupados = array_merge($eventosAgrupados, $eventos);
            $page++;
        }

        $this->consoleLog("Modelando dados de " . count($eventosAgrupados) . " Registros...");

        $sistema = '';
        if ($period) {
            $container = new Container('SistemaSelecionado');
            $sistema = $container->offsetGet('sistema');

            if (!$sistema) {
                $this->addErrorMessage('Favor selecione a avaliação pedagógica');
                return $this->redirect()->toUrl('/');
            }
        }

        $tratadosPorAvaliacaoPedagogica = [];
        foreach ($eventosAgrupados as $item) {
            if (!$item->sistema) continue;
            if ($sistema && ($sistema != $item->sistema)) continue;
            $tratadosPorAvaliacaoPedagogica[$item->sistema][] = $item;
        }

        $groupPorAvaliacao = [];
        foreach ($tratadosPorAvaliacaoPedagogica as $keyAvaliacaoPedagogica => $item) {
            if (!isset($groupPorAvaliacao[$keyAvaliacaoPedagogica])) $groupPorAvaliacao[$keyAvaliacaoPedagogica] = 0;
            $groupPorAvaliacao[$keyAvaliacaoPedagogica]++;

            $semCategoria = 0;
            $semLocalidade = 0;

            $statusTratamento = ['Finalizado' => 0, 'Em Tratamento' => 0, 'Não Iniciado' => 0];

            $categorias = [];
            $categoriasSubCategoria = [];
            $categoriasSubCategoriasUf = [];
            $top10CategoriaSubcategoria = [];

            $ufs = [];
            $ufsCategorias = [];
            $ufsCategoriasSubcategorias = [];

            $avaliacaoPedagogica = [];
            $avaliacaoPedagogicaUf = [];
            $avaliacaoPedagogicaUfCategoria = [];

            $ufNivel = [];

            $top10Usuarios = [];

            $origemOcorrencias = [];

            $eventosTratados = [];

            $linhaTempo = 0;

            $categoriasAbertosFechados = [];
            $categoriasAbertosFechadosMode = ['Aberto' => 0, 'Fechado' => 0];

            $caminhoCritico = [];
            $severidade = [];
            $caminhoCriticoStatus = [];

            foreach ($item as $evento) {

                //SE A SOLICITAÇÃO FOR POR PERIODO ENTÃO PASSA PELA VERIFICAÇÃO ABAIXO
                $created = new \DateTime($service->epocToDate($evento->Created, 'Y-m-d'));
                if ($period) {

                    if (!(($created >= $startDate) && ($created <= $endDate))) continue;
                    if ($sistema != $evento->sistema) continue;

                } else {
                    if ($created != $today) continue;
                }

                if ($evento->caminho_critico) {
                    @$caminhoCritico[$evento->caminho_critico][$evento->Status]++;
                    @$caminhoCritico[$evento->caminho_critico]['Total']++;
                    @$severidade[$evento->nivel_do_alerta_sgir]++;
                    @$caminhoCriticoStatus[$evento->caminho_critico . '_-_' . $evento->Status]++;
                }

                if ($evento->categoria_evento == '' || $evento->municipio_de_aplicacao == '') {
                    if ($evento->categoria_evento == '') $semCategoria++;
                    if ($evento->municipio_de_aplicacao == '') $semLocalidade++;
                    continue;
                }

                $local = [];
                if (preg_match('/ - /', $evento->municipio_de_aplicacao)) {
                    $local = explode(' - ', $evento->municipio_de_aplicacao);
                }

                $local[0] = trim($local[0]);

                @$ufs[$local[0]]++;

                @$avaliacaoPedagogica[$evento->sistema]++;

                @$top10CategoriaSubcategoria[$evento->categoria_evento]++;

                if (preg_match('/ - /', $evento->categoria_evento)) {

                    $auxA = explode(' - ', $evento->categoria_evento);

                    @$categorias[$auxA[0]]++;
                    @$categoriasSubCategoria[$auxA[0]][$auxA[1]]++;
                    @$categoriasSubCategoriasUf[$auxA[0]][$auxA[1]][$local[0]]++;

                    @$ufsCategorias[$local[0]][$auxA[0]]++;
                    @$ufsCategoriasSubcategorias[$local[0]][$auxA[0]][$auxA[1]]++;

                    @$avaliacaoPedagogicaUf[$evento->sistema][$local[0]]++;
                    @$avaliacaoPedagogicaUfCategoria[$evento->sistema][$local[0]][$auxA[0]]++;
                } else {
                    @$categorias[$evento->categoria_evento]++;
                    @$categoriasSubCategoria[$evento->categoria_evento][$local[0]]++;
                    @$categoriasSubCategoriasUf[$evento->categoria_evento][$local[0]][$local[0]]++;

                    @$ufsCategorias[$local[0]][$evento->categoria_evento]++;
                    @$ufsCategoriasSubcategorias[$local[0]][$evento->categoria_evento][$evento->categoria_evento]++;

                    @$avaliacaoPedagogicaUf[$evento->sistema][$local[0]]++;
                    @$avaliacaoPedagogicaUfCategoria[$evento->sistema][$local[0]][$evento->categoria_evento]++;
                }

                $user = '';
                if ($evento->usuario_triagem) {
                    if (preg_match('/ - /', $evento->usuario_triagem)) {
                        $user = explode(' - ', $evento->usuario_triagem)[0];
                    } else {
                        $user = $evento->usuario_triagem;
                    }
                } else {
                    if (isset($evento->Author)) $user = $evento->Author;
                }

                @$ufNivel[$local[0] . ' - ' . $evento->nivel_do_alerta_sgir]++;

                @$top10Usuarios[$user]++;

                @$origemOcorrencias[($evento->origem_informacao) ? $evento->origem_informacao : 'Risk Manager']++;

                @$statusTratamento[$evento->status_tratamento]++;


//            $linhaTempoCreated = new \DateTime($serviceEvent->epocToDate($evento->Created,'Y-m-d H:i:s'));
//            if($linhaTempoCreated->diff($hoje)->days == 0 && ($linhaTempoCreated->diff($hoje)->h == 0 && $linhaTempoCreated->diff($hoje)->m <= 5)){
//                $linhaTempo++;
//            }

                $categ = explode(' - ', $evento->categoria_evento)[0];
                if ($evento->Status == 'Aberto') {

                    $linhaTempo++;

                    if (!isset($categoriasAbertosFechados[$categ])) $categoriasAbertosFechados[$categ] = $categoriasAbertosFechadosMode;
                    $categoriasAbertosFechados[$categ][$evento->Status]++;
                }
                if ($evento->Status == 'Fechado') {
                    if (!isset($categoriasAbertosFechados[$categ])) $categoriasAbertosFechados[$categ] = $categoriasAbertosFechadosMode;
                    $categoriasAbertosFechados[$categ][$evento->Status]++;
                }


                $evt = new Event();
                $evento->data_criacao = $evt->epocToDate($evento->Created, 'd/m/Y H:i:s');
                $eventosTratados[] = $evento;

            }

            //TRATANDO CAMINHO CRÍTICO
            foreach ($caminhoCritico as $caminho => $item) {
                if (!isset($item['Aberto'])) $caminhoCritico[$caminho]['Aberto'] = 0;
                if (!isset($item['Fechado'])) $caminhoCritico[$caminho]['Fechado'] = 0;

            }
            //PIE QTD POR CAMINHO CRÍTICO
            $qtdCaminhoCriticoTratado = [];
            foreach ($caminhoCritico as $caminho => $value) {
                $qtdCaminhoCriticoTratado[] = ['name' => $caminho, 'y' => $value['Total']];
            }

            //TRATANDO SEVERIDADE
            if (!isset($severidade['1 - Baixo'])) $severidade['1 - Baixo'] = 0;
            if (!isset($severidade['2 - Atenção'])) $severidade['2 - Atenção'] = 0;
            if (!isset($severidade['3 - Elevado'])) $severidade['3 - Elevado'] = 0;
            if (!isset($severidade['4 - Alto'])) $severidade['4 - Alto'] = 0;
            if (!isset($severidade['5 - Severo'])) $severidade['5 - Severo'] = 0;

            $qtdSeveridadeTratado = [];
            krsort($severidade);
            foreach ($severidade as $name => $value) {
                $qtdSeveridadeTratado[] = ['name' => $name, 'y' => $value];
            }

            //BAR - TRATADO CAMINHO CRÍTICO POR STATUS
            $caminhoCriticoStatusAux = [];
            $caminhoCriticoStatusCategorias = [];
            $caminhoCriticoStatusName = [];
            ksort($caminhoCriticoStatus);
            foreach ($caminhoCriticoStatus as $key => $value) {
                $aux = explode('_-_', $key);

                if (!in_array($aux[0], $caminhoCriticoStatusCategorias)) $caminhoCriticoStatusCategorias[] = $aux[0];
                if (!in_array($aux[1], $caminhoCriticoStatusName)) $caminhoCriticoStatusName[] = $aux[1];

                @$caminhoCriticoStatusAux[$aux[0]][$aux[1]] += $value;
            }

            foreach ($caminhoCriticoStatusAux as $caminhoCriticoItem => $nivel) {
                foreach ($caminhoCriticoStatusName as $nivelName) {
                    if (!array_key_exists($nivelName, $nivel)) $caminhoCriticoStatusAux[$caminhoCriticoItem][$nivelName] = 0;
                }
            }

            $niveisCaminhoCriticoTratados = [];
            foreach ($caminhoCriticoStatusAux as $item) {
                foreach ($item as $key => $value) {
                    $niveisCaminhoCriticoTratados[$key][] = $value;
                }
            }

            $caminhoCriticoStatusTratado = [];
            foreach ($niveisCaminhoCriticoTratados as $key => $value) {
                $caminhoCriticoStatusTratado[] = ['name' => $key, 'data' => $value];
            }


            //TRATANDO CATEGORIA/SUBCATEGORIA/UF
            $categoriaSubcategoriaUf = [];
            $categoriaSubcategoriaUf['serie']['name'] = 'Categorias';
            $categoriaSubcategoriaUf['serie']['colorByPoint'] = true;

            $ufsAux = [];
            $subCategoriaAux = [];
            ksort($categorias);
            foreach ($categorias as $keyA => $valueA) {
                $categoriaSubcategoriaUf['serie']['data'][] = ['name' => $keyA, 'y' => $valueA, 'drilldown' => $keyA];
                foreach ($categoriasSubCategoria[$keyA] as $keyB => $valueB) {
                    $subCategoriaAux[] = ['name' => $keyB, 'y' => $valueB, 'drilldown' => $keyB];
                    foreach ($categoriasSubCategoriasUf[$keyA][$keyB] as $keyC => $valueC) {
                        $ufsAux[] = ['name' => $keyC, 'y' => $valueC];
                    }
                    array_multisort($ufsAux, SORT_ASC);
                    $categoriaSubcategoriaUf['drilldown']['series'][] = ['id' => $keyB, 'name' => $keyB, 'data' => $ufsAux];
                    $ufsAux = [];
                }
                array_multisort($subCategoriaAux, SORT_ASC);
                $categoriaSubcategoriaUf['drilldown']['series'][] = ['id' => $keyA, 'name' => $keyA, 'data' => $subCategoriaAux];
                $subCategoriaAux = [];
            }

            //TRATANDO UF/CATEGORIA/SUBCATEGORIA
            $UfcategoriaSubcategoria = [];
            $UfcategoriaSubcategoria['serie']['name'] = 'UF';
            $UfcategoriaSubcategoria['serie']['colorByPoint'] = true;

            $ufsAux = [];
            $subCategoriaAux = [];
            ksort($ufs);
            foreach ($ufs as $keyA => $valueA) {
                $UfcategoriaSubcategoria['serie']['data'][] = ['name' => $keyA, 'y' => $valueA, 'drilldown' => $keyA];
                foreach ($ufsCategorias[$keyA] as $keyB => $valueB) {
                    $subCategoriaAux[] = ['name' => $keyA . ' - ' . $keyB, 'y' => $valueB, 'drilldown' => $keyA . ' - ' . $keyB];
                    foreach ($ufsCategoriasSubcategorias[$keyA][$keyB] as $keyC => $valueC) {
                        $ufsAux[] = ['name' => $keyA . ' - ' . $keyC, 'y' => $valueC];
                    }
                    array_multisort($ufsAux, SORT_ASC);
                    $UfcategoriaSubcategoria['drilldown']['series'][] = ['id' => $keyA . ' - ' . $keyB, 'name' => $keyA . ' - ' . $keyB, 'data' => $ufsAux];
                    $ufsAux = [];
                }
                array_multisort($subCategoriaAux, SORT_ASC);
                $UfcategoriaSubcategoria['drilldown']['series'][] = ['id' => $keyA, 'name' => $keyA . ' - ' . $keyB, 'data' => $subCategoriaAux];
                $subCategoriaAux = [];
            }


            //TRATANDO AVALIACAO PEDAGÓGICA / UF / CATEGORIA
            $AvaliacaoPedagogicaUfCategoria = [];
            $AvaliacaoPedagogicaUfCategoria['serie']['name'] = 'AVALIAÇÃO PEDAGÓGICA';
            $AvaliacaoPedagogicaUfCategoria['serie']['colorByPoint'] = true;

            $avaliacaoPegagogicaAux = [];
            $avaliacaoPegagogicaCategoriaAux = [];
            ksort($avaliacaoPedagogica);
            foreach ($avaliacaoPedagogica as $keyA => $valueA) {
                $AvaliacaoPedagogicaUfCategoria['serie']['data'][] = ['name' => $keyA, 'y' => $valueA, 'drilldown' => $keyA];
                foreach ($avaliacaoPedagogicaUf[$keyA] as $keyB => $valueB) {
                    $avaliacaoPegagogicaCategoriaAux[] = ['name' => $keyA . ' - ' . $keyB, 'y' => $valueB, 'drilldown' => $keyA . ' - ' . $keyB];
                    foreach ($avaliacaoPedagogicaUfCategoria[$keyA][$keyB] as $keyC => $valueC) {
                        $avaliacaoPegagogicaAux[] = ['name' => $keyA . ' - ' . $keyB . ' - ' . $keyC, 'y' => $valueC];
                    }
                    array_multisort($avaliacaoPegagogicaAux, SORT_ASC);
                    $AvaliacaoPedagogicaUfCategoria['drilldown']['series'][] = ['id' => $keyA . ' - ' . $keyB, 'name' => $keyA . ' - ' . $keyB, 'data' => $avaliacaoPegagogicaAux];
                    $avaliacaoPegagogicaAux = [];
                }
                array_multisort($avaliacaoPegagogicaCategoriaAux, SORT_ASC);
                $AvaliacaoPedagogicaUfCategoria['drilldown']['series'][] = ['id' => $keyA, 'name' => $keyA . ' - ' . $keyB, 'data' => $avaliacaoPegagogicaCategoriaAux];
                $avaliacaoPegagogicaCategoriaAux = [];
            }


            //TRATANDO CATEGORIA X ABERTO X FECHADO
            ksort($categoriasAbertosFechados);
            $categoriasAbertosFechadosCategory = [];
            $categoriasAbertosFechadosData = [];
            $categoriasAbertosFechadosColorIndex = 0;
            $categoriasAbertosFechadosColor = ["#2b908f", "#90ee7e", "#f45b5b", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee", "#55BF3B", "#DF5353", "#7798BF", "#aaeeee", "#2b908f", "#90ee7e", "#f45b5b", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee", "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"];
            foreach ($categoriasAbertosFechados as $key => $value) {
                $categoriasAbertosFechadosCategory[] = $key;
                $categoriasAbertosFechadosData[] = ['y' => $value['Aberto'] + $value['Fechado'], 'color' => $categoriasAbertosFechadosColor[$categoriasAbertosFechadosColorIndex], 'drilldown' => ['name' => $key, 'categories' => ['Abertos', 'Fechados'], 'data' => [$value['Aberto'], $value['Fechado']], 'color' => $categoriasAbertosFechadosColor[$categoriasAbertosFechadosColorIndex]]];
                $categoriasAbertosFechadosColorIndex++;
            }
            $categoriasAbertosFechadosTratado['category'] = $categoriasAbertosFechadosCategory;
            $categoriasAbertosFechadosTratado['data'] = $categoriasAbertosFechadosData;


            //TRATANDO TOP 10 CATEGORIA>SUBCATEGORIA
            arsort($top10CategoriaSubcategoria);
            $top10CategoriaSubcategoriaTratado = [];
            $top10CategoriaSubcategoriaCount = 0;
            foreach ($top10CategoriaSubcategoria as $key => $value) {
                $top10CategoriaSubcategoriaCount++;
                $top10CategoriaSubcategoriaTratado[] = ['name' => $key, 'y' => $value];
                if ($top10CategoriaSubcategoriaCount == 10) break;
            }

            //TRATANDO SITUAÇÃO DE TRATAMENTO
            $statusTratamentoTratado = [];
            foreach ($statusTratamento as $key => $value) {
                $statusTratamentoTratado[] = ['name' => $key, 'y' => $value];
            }

            //TRATADO UF POR NIVEL
            $ufNivelAux = [];
            $ufNivelCategorias = [];
            $ufNivelName = [];
            ksort($ufNivel);
            foreach ($ufNivel as $key => $value) {
                $aux = explode(' - ', $key);

                if (!in_array($aux[0], $ufNivelCategorias)) $ufNivelCategorias[] = $aux[0];
                if (!in_array($aux[1] . '-' . $aux[2], $ufNivelName)) $ufNivelName[] = $aux[1] . '-' . $aux[2];

                @$ufNivelAux[$aux[0]][$aux[1] . '-' . $aux[2]] += $value;
            }

            foreach ($ufNivelAux as $uf => $nivel) {
                foreach ($ufNivelName as $nivelName) {
                    if (!array_key_exists($nivelName, $nivel)) $ufNivelAux[$uf][$nivelName] = 0;
                }
            }

            $niveisTratados = [];
            foreach ($ufNivelAux as $item) {
                foreach ($item as $key => $value) {
                    $niveisTratados[$key][] = $value;
                }
            }


            $ufNivelTratado = [];

            $ordemNivel = [];
            if (isset($niveisTratados['5-Severo'])) {
                $ordemNivel['5-Severo'] = $niveisTratados['5-Severo'];
            }
            if (isset($niveisTratados['4-Alto'])) {
                $ordemNivel['4-Alto'] = $niveisTratados['4-Alto'];
            }
            if (isset($niveisTratados['3-Elevado'])) {
                $ordemNivel['3-Elevado'] = $niveisTratados['3-Elevado'];
            }
            if (isset($niveisTratados['2-Atenção'])) {
                $ordemNivel['2-Atenção'] = $niveisTratados['2-Atenção'];
            }
            if (isset($niveisTratados['1-Baixo'])) {
                $ordemNivel['1-Baixo'] = $niveisTratados['1-Baixo'];
            }

            foreach ($ordemNivel as $key => $value) {
                $ufNivelTratado[] = ['name' => $key, 'data' => $value];
            }

            $this->consoleLog("Carregando registros de ocorrência...");
            $triagemServ = new Alertas();
            $triagem = $triagemServ->fetchAll();

            $totalOcorrencias = ['Triagem' => 0, 'Alertas' => 0, 'Arquivados' => 0];

            /** @var $triagem \Classes\Service\Alertas[] */
            foreach ($triagem as $item) {

                if (strlen($item->getDataHora()) < 12) $item->setDataHora($item->getDataHora() . ' 00:00:00');
                $created = \DateTime::createFromFormat('d/m/Y H:i:s', $item->getDataHora());
                //SE A SOLICITAÇÃO FOR POR PERIODO ENTÃO PASSA PELA VERIFICAÇÃO ABAIXO
                if ($period) {
                    if (!(($created >= $startDate) && ($created <= $endDate))) continue;
                    if ($sistema != $item->getSistema()) continue;
                } else {
                    //if ($created < $today) continue;
                }


                if ($item->getStatus() == 0) $totalOcorrencias['Arquivados']++;
                if ($item->getStatus() == 1) $totalOcorrencias['Triagem']++;
                if ($item->getStatus() == 2) $totalOcorrencias['Alertas']++;
            }


            //TRATANDO TOTAL DE OCORRENCIAS
            $totalOcorrenciasTratado = [];
            foreach ($totalOcorrencias as $key => $value) {
                $totalOcorrenciasTratado[] = ['name' => $key, 'y' => $value];
            }

            //TRATANDO TOP 10 USUARIOS
            $top10UsuariosTratados = [];
            foreach ($top10Usuarios as $key => $value) {
                $top10UsuariosTratados[] = ['name' => $key, 'y' => $value];
            }

            //TRATANDO LISTA DE USUÁRIOS ONLINE
            $userOnlineService = new Acesso();
            $usersOnline = $userOnlineService->fetchAll()->toArray();
            $usersOnlineCount = count($usersOnline);

            $this->consoleLog("Carregando registros de abstenção...");

            $ultimaMilhaService = new UltimaMilha();
            $ultimaMilhaService->setId(1);
            $ultimaMilha = $ultimaMilhaService->load()->toArray();
            $tratadosAbstencao = [];
            $tratadosAbstencaoCategorias = [];
            $totalAbstencao = [];
            $totalPresentes = [];
            foreach ($ultimaMilha as $key => $value) {
                if ($key == 'Id') continue;
                $aux = (int)explode('|', $value)[1];
                $tratadosAbstencaoCategorias[] = strtoupper($key);
                $totalAbstencao[] = $aux;
                $totalPresentes[] = 100 - $aux;

            }
            $tratadosAbstencao[] = ['name' => 'Abstenção', 'data' => $totalAbstencao];
            $tratadosAbstencao[] = ['name' => 'Presentes', 'data' => $totalPresentes];

            $this->consoleLog("Consolidando dados...");

            $dataDia = [];
            foreach ($eventosTratados as $evtTratado) {
                if (!isset($dataDia[$evtTratado->dia_da_aplicacao])) $dataDia[$evtTratado->dia_da_aplicacao] = 0;
                $dataDia[$evtTratado->dia_da_aplicacao]++;
            }

            $graDia = [];
            foreach ($dataDia as $dia => $total) {
                $graDia[] = ['name' => $dia, 'y' => $total];
            }

            $json[$keyAvaliacaoPedagogica]['dashboard'] = [
                'problemas' => ['sem-categoria' => $semCategoria, 'sem-localidade' => $semLocalidade],
                'dados' => [
                    'status_tratamento' => $statusTratamentoTratado,
                    'categoria_subcategoria_uf' => $categoriaSubcategoriaUf,
                    'uf_categoria_subcategoria' => $UfcategoriaSubcategoria,
                    'avaliacao_pedagogica_uf_categoria' => $AvaliacaoPedagogicaUfCategoria,
                    'top_dez_categoria_subcategoria' => $top10CategoriaSubcategoriaTratado,
                    'top_dez_usuarios' => $top10UsuariosTratados,
                    'origem_ocorrencia' => $origemOcorrencias,
                    'total_ocorrencia' => $totalOcorrenciasTratado,
                    'uf_nivel_categoria' => $ufNivelCategorias,
                    'uf_nivel' => $ufNivelTratado,
                    'categoria_abertos_fechados' => $categoriasAbertosFechadosTratado,
                    'linha_tempo' => $linhaTempo,
                    'abstencao_categoria' => $tratadosAbstencaoCategorias,
                    'abstencao' => $tratadosAbstencao,
                    'caminho_critico' => $caminhoCritico,
                    'pie_qtd_por_caminho_critico' => $qtdCaminhoCriticoTratado,
                    'pie_qtd_por_severidade' => $qtdSeveridadeTratado,
                    'bar_caminho_critico_categoria' => $caminhoCriticoStatusCategorias,
                    'bar_caminho_critico' => $caminhoCriticoStatusTratado,
                    'users_online_list' => $usersOnline,
                    'users_online_number' => $usersOnlineCount,
                    'eventos' => $eventosTratados,
                    'dia_aplicacao' => $graDia
                ]
            ];

        }

        $this->consoleLog("Gerando arquivo JSON...");

        if ($period) {
            if (!isset($json[$sistema]) || !$json[$sistema]) return false;
            $jsonDone = json_encode($json[$sistema]);
            file_put_contents('./data/json/' . $filename . '.json', $jsonDone);
            return $jsonDone;
        } else {
            foreach ($json as $avalPedag => $arquivo) {
                file_put_contents("./data/json/dashboard_{$avalPedag}.json", json_encode($arquivo));
            }
            $this->consoleLog("Pronto!");
            die;
        }

    }

    public function getEventByCodeAction()
    {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) throw new \Exception('É necessário enviar um formulário');
            $post = $request->getPost();

            $event = new Event();
            $event->setAnonimo();
            $event->setCode($post['code']);
            $event->load();

            $progress = new Event();
            $progress->setAnonimo();
            $progress->setCode($post['code']);
            $dados = $progress->getUpdates();

            if (!is_array($dados)) $dados = [];

            $tratados = [];
            foreach ($dados as $item) {
                if (!$item->Comment) continue;
                $tratados[] = $item;
            }

            $return = ['error' => false, 'dados' => $event->toArray(), 'progresso' => $tratados];
        } catch (\Exception $e) {
            $return = ['error' => true, 'message' => $e->getMessage()];
        }
        return new JsonModel($return);
    }

    public function getProgressEventByCodeAction()
    {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) throw new \Exception('É necessário enviar o código do evento.');
            $post = $request->getPost();

            $event = new Event();
            $event->setAnonimo();
            $event->setCode($post['code']);
            $dados = $event->getUpdates();

            if (!is_array($dados)) $dados = [];

            $tratados = [];
            foreach ($dados as $item) {
                if (!$item->Comment) continue;
                $tratados[] = $item;
            }

            $return = ['error' => false, 'dados' => $tratados];
        } catch (\Exception $e) {
            $return = ['error' => true, 'message' => $e->getMessage()];
        }
        return new JsonModel($return);
    }

    public function midiaAction()
    {
        return new ViewModel();
    }

    public function mapaAlertaAction()
    {
        return new ViewModel();
    }

    public function centerMunicipioAction()
    {
        $municipio = $this->params('id');
        $dados = json_decode(file_get_contents('./data/municipios.json'), true);
        return new JsonModel($dados[$municipio]);
    }

    public function ultimaMilhaAction()
    {
        $container = new Container('SistemaSelecionado');
        $sistema = $container->offsetGet('sistema');

        if (!$sistema) {
            $this->addErrorMessage('Favor selecione a avaliação pedagógica');
            return $this->redirect()->toUrl('/');
        }

        return new ViewModel();
    }

    public function processaKmlUltimaMilhaAction()
    {
        echo 'Carregando dados da Última Milha' . PHP_EOL;
        $ultimaMilhaService = new UltimaMilha();
        $ultimaMilhaService->setId(1);
        $dados = $ultimaMilhaService->load()->toArray();

        foreach ($dados as $key => $value) {
            if ($key == 'Id') continue;
            $aux = explode('|', $value)[0];
            echo 'Alterando KML da UF ' . $key . PHP_EOL;
            $kml = file_get_contents('./public/mapa/estados/' . $key . '.kml');
            $kml = str_replace($ultimaMilhaService->colors(), $ultimaMilhaService->getColorByPercentage($aux), $kml);
            file_put_contents('./public/mapa/estados/' . $key . '.kml', $kml);
        }

        echo 'Pronto!';
        die;
    }

    public function getKmlsEstadosAction()
    {
        try {

            $kmls = scandir('public/mapa/estados/');

            $kmlsTratados = [];
            foreach ($kmls as $kml) {
                if (in_array($kml, ['.', '..'])) continue;
                $kmlsTratados[] = $kml;
            }

            $return = ['error' => false, 'dados' => $kmlsTratados];
        } catch (\Exception $e) {
            $return = ['error' => true, 'message' => $e->getMessage()];
        }

        return new JsonModel($return);
    }

    public function getPorcentageUltimaMilhaAction()
    {
        try {

            $service = new UltimaMilha();
            $service->setId(1);
            $dados = $service->load()->toArray();

            $tratados = [];
            foreach ($dados as $key => $value) {
                if ($key == "Id") continue;
                $aux = (float)explode('|', $value)[0];
                $tratados[strtoupper($key)] = $aux;
            }

            $return = ['error' => false, 'dados' => $tratados];
        } catch (\Exception $e) {
            $return = ['error' => true, 'message' => $e->getMessage()];
        }

        return new JsonModel($return);
    }

    public function buildGeojsonAplicadoresAction()
    {
        $asset = new Asset();
        $asset->setAnonimo();
        //$asset->setPath('Monitoramento > Enem > Municípios de Aplicação >');
        $filter = new Filter();
        $filter->setPageSize(1000);
        $filter->where(Filter::F_EQ, Filter::A_STRING, 'AssetType', 'Município');
        $filter->andWhere(Filter::F_SUBSTRING, Filter::A_STRING, 'Path', 'Monitoramento >');
        $asset->setFilter($filter);
        $dados = $asset->fetchAll();

        $ativosAgrupados = $dados;

        $page = 2;
        while (count($dados) == 1000) {
            echo $page . '° página de ativos...' . PHP_EOL;
            $filter->setPage($page);
            $asset->setFilter($filter);
            $dados = $asset->fetchAll();
            $ativosAgrupados = array_merge($ativosAgrupados, $dados);
            $page++;
        }

        $aplicadores = [];
        $aplicadoresJson = [];
        $municipios = [];
        $municipioLocale = [];
        $municipiosExame = [];
        echo count($ativosAgrupados) . ' Ativos do tipo Município encontrados' . PHP_EOL;
        foreach ($ativosAgrupados as $dado) {

            $caminho = explode(' > ', $dado->getPath());
            if ($caminho[2] != 'Municípios de Aplicação') continue;

            $nomeExame = $caminho[1];

            echo 'Processando ' . $dado->getName() . PHP_EOL;

            $municipiosExame[$nomeExame][] = $dado->getName();

            $municipios[$nomeExame][] = [
                'type' => 'Feature',
                'properties' => [
                    'municipio' => $dado->getId(),
                    'title' => $dado->getName(),
                    'name' => $dado->getName(),
                    'tipo' => 'Município',
                    'url' => '/dashboard/details-assets/' . $dado->getId()
                ],
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [
                        $dado->getLongitude(),
                        $dado->getLatitude()
                    ]
                ]
            ];

            $municipioLocale[$dado->getName()] = [
                'lat' => $dado->getLatitude(),
                'long' => $dado->getLongitude()
            ];

            if ($dado->getCustomAttributes()) {
                $aplicadoresJson[$nomeExame][] = [
                    'id' => $dado->getId(),
                    'title' => $dado->getName(),
                    'tipo' => @$dado->getCustomAttributes()->consorcioaplicador,
                    'url' => '/dashboard/details-assets/' . $dado->getId(),
                    'coordinates' => [
                        $dado->getLongitude(),
                        $dado->getLatitude()
                    ]
                ];
                $aplicadoresGeoJson[$nomeExame][] = [
                    'type' => 'Feature',
                    'properties' => [
                        'municipio' => $dado->getId(),
                        'title' => $dado->getName(),
                        'tipo' => @$dado->getCustomAttributes()->consorcioaplicador,
                        'url' => '/dashboard/details-assets/' . $dado->getId()
                    ],
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            $dado->getLongitude(),
                            $dado->getLatitude()
                        ]
                    ]
                ];
            }


        }

        if (count($ativosAgrupados)) {
            echo 'Gerando arquivos GeoJson...' . PHP_EOL;

            foreach ($municipiosExame as $exame => $muni) {
                file_put_contents('./data/municipios/' . $exame . '.json', json_encode($muni));
            }

            foreach (array_keys($municipios) as $exame) {
                $municipiosTratado = json_encode(['type' => 'FeatureCollection', 'features' => $municipios[$exame]]);
                file_put_contents('./public/dashboard-assets/geojson/municipios_' . $exame . '.geojson', $municipiosTratado);
            }
            file_put_contents('./data/municipios.json', json_encode($municipioLocale));

            foreach ($aplicadoresGeoJson as $exame => $data) {
                $pathDir = './public/dashboard-assets/geojson/' . $exame;
                if (!file_exists($pathDir)) mkdir($pathDir, 0777, true);

                $geoJson = json_encode(['type' => 'FeatureCollection', 'features' => $data]);
                file_put_contents('./public/dashboard-assets/geojson/' . $exame . '/aplicadores.geojson', $geoJson);
            }

            foreach ($aplicadoresJson as $exame => $aplicador) {
                $pathDir = './public/dashboard-assets/geojson/' . $exame;
                if (!file_exists($pathDir)) mkdir($pathDir, 0777, true);

                file_put_contents('./public/dashboard-assets/geojson/' . $exame . '/aplicadores.json', json_encode($aplicador));
            }
            echo 'Pronto!';
        } else {
            echo 'Ativos não localizados.' . PHP_EOL;
        }

        die;
    }

    public function getAplicadoresAction()
    {
        $container = new Container('SistemaSelecionado');
        $exame = $container->offsetGet('sistema');
        $data = json_decode(file_get_contents('./public/dashboard-assets/geojson/' . $exame . '/aplicadores.json'), true);
        $tratado = [];
        $laco = 1;
        foreach ($data as $item) {
            if (!isset($tratado[$laco])) $tratado[$laco] = [];
            if (count($tratado[$laco]) > 50) $laco++;
            $tratado[$laco][] = $item;
        }

        echo json_encode($tratado);
        die;
    }

    public function detailsAssetsAction()
    {
        try {
            $id = $this->params('id');
            $asset = new Asset();
            $asset->setAnonimo();
            $asset->setId($id);
            $asset->load();

            $atributos = json_decode(file_get_contents('./data/arquivos/atributos_ativo.json'), true);

            if (!$asset->getName()) throw  new \Exception('Não localizado');

            $view = new ViewModel(['ativo' => $asset->toArray(), 'atributos' => $atributos]);
            $view->setTerminal(true);
            return $view;
        } catch (\Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    public function alertasPreAction()
    {
        if (!$this->params('id')) {
            $evento = new Event();
            $evento->setAnonimo();

            $filter = new Filter();
            $filter->where(Filter::F_EQ, Filter::A_STRING, 'CustomAttributes/__origem_do_alerta', 'RM - Pré Aplicação');
            $filter->setStatus(1);
            $filter->setPageSize(1000);
            $evento->setFilter($filter);

            $retorno = [];

            $dadosMunicipio = json_decode(file_get_contents('./data/municipios.json'), true);
            $tratado = [];
            $tratadoMunicipio = [];
            foreach ($evento->fetchAll() as $item) {
                $custom = $item->getCustomAttributes(true);
                if (!isset($custom['municipio_de_aplicacao'])) continue;
                $municipio = $custom['municipio_de_aplicacao'];
                if (!$municipio) continue;
                $municipio = $this->tirarAcentos(mb_strtoupper($municipio, 'UTF-8'));
                if (!isset($dadosMunicipio[$municipio])) continue;

                @$locale = $dadosMunicipio[$municipio];

                $retorno[$custom['sistema']][$municipio] = ['lat' => $locale['lat'], 'long' => $locale['long'], 'municipio' => $municipio];
                $tratadoMunicipio[$custom['sistema']][$municipio][] = ['title' => $item->getTitle(), 'description' => $item->getDescription()];
            }

            foreach ($retorno as $sistema => $data) {
                file_put_contents('./data/cache/alertas_pre_' . $sistema . '.json', json_encode(array_values($data)));
            }

            $infoAttr = new Attributes('sistema');
            $opcoes = $infoAttr->getAllowedValues();
            foreach ($opcoes as $itemSistema) {
                if (!isset($retorno[$itemSistema])) {
                    file_put_contents('./data/cache/alertas_pre_' . $itemSistema . '.json', json_encode(array_values([])));
                }
            }

            file_put_contents('./data/cache/full_pre.json', json_encode($tratadoMunicipio));
            echo 'Feito';
            die;
        } else {
            $container = new Container('SistemaSelecionado');
            $sistema = $container->offsetGet('sistema');

            echo file_get_contents('./data/cache/alertas_pre_' . $sistema . '.json');
            die;
        }
    }

    public function alertaDetalhesPreAction()
    {
        $municipio = $this->params('id');

        $container = new Container('SistemaSelecionado');
        $sistema = $container->offsetGet('sistema');


        /*   $evento = new Event();
           $evento->setAnonimo();
   
           $filter = new Filter();
           $filter->where(Filter::F_EQ, Filter::A_STRING,'CustomAttributes/__origem_do_alerta','RM - Pré Aplicação');
           $filter->andWhere(Filter::F_EQ,Filter::A_STRING,'CustomAttributes/sistema',$sistema);
           $filter->andWhere(Filter::F_EQ,Filter::A_STRING,'CustomAttributes/municipio_de_aplicacao',$municipio);
           $filter->setStatus(1);
           $filter->setPageSize(1000);
           $evento->setFilter($filter);
   
           $tratado = [];
   
           foreach($evento->fetchAll() as $item){
               $tratado[] = ['title'=>$item->getTitle(),'description'=>$item->getDescription()];
           }*/

        $data = json_decode(file_get_contents('./data/cache/full_pre.json'), true);

        $tratado = $data[$sistema][$municipio];

        $view = new ViewModel(['dados' => $tratado]);
        $view->setTerminal(true);
        return $view;
    }

    public function alertasMapaAction()
    {
        $dados = json_decode(file_get_contents('./data/cache/validados.json'), true);
        $dadosMunicipio = json_decode(file_get_contents('./data/municipios.json'), true);
        $retorno = [];
        $anexo = [];

        $container = new Container('SistemaSelecionado');
        $sistema = $container->offsetGet('sistema');

        foreach ($dados as $item) {
            if ($item['customAttributes']['sistema'] != $sistema) continue;
            $municipio = $item['customAttributes']['municipio_de_aplicacao'];
            if (!$municipio) continue;
            $code = $item['customAttributes']['EventID'];
            $municipio = $this->tirarAcentos(mb_strtoupper($municipio, 'UTF-8'));
            if (!isset($dadosMunicipio[$municipio])) continue;

            @$locale = $dadosMunicipio[$municipio];

            if (count($item['anexo'])) {
                $anexo[$municipio.'-'.$item['status']] = true;
            }

            $retorno[$municipio.'-'.$item['status']] = ['lat' => $locale['lat'], 'long' => $locale['long'], 'municipio' => $municipio, 'status' => $item['status'], 'anexo' => false];
        }

        foreach ($anexo as $muni => $tem) {
            $retorno[$muni]['anexo'] = true;
        }

        return new JsonModel(array_values($retorno));
    }


    public function alertaDetalhesAction()
    {
        $explode = explode('@',$this->params('id'));
        $municipio = $explode[0];
        $status = $explode[1];

        $dados = json_decode(file_get_contents('./data/cache/validados.json'), true);
        $tratado = [];

        $containerSistema = new Container('SistemaSelecionado');
        $sistema = $containerSistema->offsetGet('sistema');

        foreach ($dados as $item) {

            if ($item['customAttributes']['sistema'] != $sistema) continue;

            $municipioEvt = $item['customAttributes']['municipio_de_aplicacao'];
            $municipioEvt = $this->tirarAcentos(mb_strtoupper($municipioEvt, 'UTF-8'));

            $code = $item['customAttributes']['EventID'];
            $anexo = [];

            if ($municipio == $municipioEvt && $item['status'] == $status) {
                $anexo = $item['anexo'];
                $tratado[] = ['title' => $item['title'], 'description' => $item['description'], 'status' => $item['status'], 'anexo' => $anexo];
            }
        }

        $view = new ViewModel(['dados' => $tratado]);
        $view->setTerminal(true);
        return $view;
    }

    public function tirarAcentos($string)
    {
        $string = preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a A e E i I o O u U n N"), $string);
        $string = str_replace(['´', '\'', 'ç', 'Ç'], [' ', ' ', 'c', 'C'], $string);
        if ($string == 'PA - SANTA IZABEL DO PARA') $string = 'PA - SANTA ISABEL DO PARA';
        if ($string == 'SP - MOJI MIRIM') $string = 'SP - MOGI-MIRIM';
        if ($string == 'AL - OLHO D AGUA DAS FLORES') $string = 'AL - OLHO DAGUA DAS FLORES';

        //$string = str_replace('´',' ',$string);

        return $string;
    }

    public function mapaAtividadesAction()
    {
        return new ViewModel();
    }

    public function getKmlsFusoHorarioAction()
    {
        try {

            $kmls = scandir('public/mapa/fuso-horario/');

            $kmlsTratados = [];
            foreach ($kmls as $kml) {
                if (in_array($kml, ['.', '..'])) continue;
                $kmlsTratados[] = $kml;
            }

            $return = ['error' => false, 'dados' => $kmlsTratados];
        } catch (\Exception $e) {
            $return = ['error' => true, 'message' => $e->getMessage()];
        }

        return new JsonModel($return);
    }

    public function consoleLog($text)
    {
        if ($this->showConsole)
            echo $text . PHP_EOL;
    }

    public function dadosAtividadesAction()
    {
        $service = new Atividade();

        $sistema = new Container('SistemaSelecionado');

        $service->setData(date('Y-m-d'));
        $service->setSistema($sistema->offsetGet('sistema'));

        $dados = $service->filtrarObjeto();
        $tratado = [];
        foreach ($dados as $item) {
            $tratado[$item->getHoraInicio()] = ['descricao' => $item->getDescricao(), 'inicio' => $item->getHoraInicio(), 'fim' => $item->getHoraFim()];
        }
        ksort($tratado);


        return new JsonModel(['dados' => array_values($tratado)]);
    }

    public function dadosAvisosAction()
    {
        $aviso = new Aviso();
        $listaAvisos = $aviso->filtrarObjeto();
        $lista = [];
        /** @var $listaAvisos \Classes\Service\Aviso[] */
        foreach ($listaAvisos as $aviso) {
            $inicio = \DateTime::createFromFormat('Y-m-d H:i:s', $aviso->getDataInicio() . ' ' . $aviso->getHoraInicio());
            $fim = \DateTime::createFromFormat('Y-m-d H:i:s', $aviso->getDataFim() . ' ' . $aviso->getHoraFim());
            $now = new \DateTime();
            if ($now >= $inicio && $now <= $fim) {
                $lista[] = $aviso->toArray();
            }
        }

        return new JsonModel(['dados' => $lista]);
    }

    public function atualizaLabelAtivosAction()
    {
        //Atualiza os dados dos atributos de ativos e armazena em um arquivo
        $asset = new Asset();
        $asset->setAnonimo();
        $att = $asset->getInfoAttributes();
        $tratado = [];
        foreach ($att as $atributo) {
            $tratado[$atributo->VariableName] = $atributo->Name;
        }
        file_put_contents('./data/arquivos/atributos_ativo.json', json_encode($tratado));
        die;
    }


    public function infoAction()
    {
        phpinfo();
        die;
    }
}