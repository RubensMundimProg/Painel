<?php

namespace Application\Controller;

use Application\Service\UltimaMilha;
use Classes\Service\Alertas;
use Estrutura\Controller\AbstractEstruturaController;
use Modulo\Service\UsuarioApi;
use RiskManager\OData\CustomAttributes;
use RiskManager\OData\Filter;
use RiskManager\Workflow\Service\Event;
use RiskManager\Workflow\Service\Queries;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ValidadosController extends AbstractEstruturaController
{
    public function limparAction()
    {

        $red = '';
        if ($this->params('id', false)) {
            $red = '/'.$this->params('id');
        }

        $url = '/validados'.$red;
        $metodo = 'filtro-' . md5($url);
        $container = new \Zend\Session\Container('filtro');
        $container->offsetUnset($metodo);

        return $this->redirect()->toUrl($url);
    }

    public function indexAction()
    {
        $cacheFile = './data/cache/validados.json';
        $dados = json_decode(file_get_contents($cacheFile), true);

        $form = new \Classes\Form\Triagem();
        $form->get('Categoria')->setAttribute('required', false);

        $dadosQuery = $this->getRequest()->getQuery()->toArray();
        $url = '/validados';
        $metodo = 'filtro-' . md5($url);
        $container = new \Zend\Session\Container('filtro');

        if (count($dadosQuery)) {
            $container->offsetSet($metodo, $dadosQuery);
            $dataFiltro = $dadosQuery;
        } else {
            $dataFiltro = $container->offsetGet($metodo);
        }

        $filtro = [];
        $categorias = [];
        if (count($dataFiltro) > 0) {
            $filtro = $dataFiltro;
            if (isset($filtro['Categoria'])) {
                $categorias = $filtro['Categoria'];
                unset($filtro['Categoria']);
            }
            $form->setData($filtro);
        }

        $filtroSistema = new Container('SistemaSelecionado');
        $sistema = $filtroSistema->offsetGet('sistema');

        if (!$sistema) {
            $this->addErrorMessage('Selecione a avaliação pedagógica');
            return $this->redirect()->toUrl('/');
        }

        $usuarioApi = new UsuarioApi();
        $grupos = $usuarioApi->get('perfis');

        $lista = [];
        foreach ($dados as $item) {
            if (!in_array('Administrador INEP', $grupos)) {
                if (!isset($item['customAttributes']['etapas_do_cronograma']) || $item['customAttributes']['etapas_do_cronograma'] != 'Exame') continue;
            }

            if ($item['status'] != 'Aberto') continue;

            $dados = $item['customAttributes'];

            if (count($filtro)) {
                $continue = false;

                if (count($categorias)) {
                    $categoriaEventoArr = explode(' - ', $dados['categoria_evento']);
                    if (!in_array($categoriaEventoArr[0], $categorias)) $continue = true;
                }

                if ($filtro['OrigemInformacao'] != '') {
                    if ($filtro['OrigemInformacao'] != $dados['origem_informacao']) $continue = true;
                }

                if ($filtro['DataRegistro'] != '') {
                    $epoc = new Event();
                    $dataRegistro = $epoc->epocToDate($dados['Created']);
                    if ($dataRegistro != $filtro['DataRegistro']) $continue = true;
                }

                if ($filtro['UfFiltro'] != '') {
                    if ($dados['unidade_federativa'] == '') {
                        $ufMunicipio = explode(' - ', $dados['municipio_de_aplicacao']);
                        if ($filtro['UfFiltro'] != $ufMunicipio[0]) $continue = true;
                    } else {
                        if ($filtro['UfFiltro'] != $dados['unidade_federativa']) $continue = true;
                    }
                }

                if ($continue) continue;
            }


            if ($sistema) {
                if ($dados['sistema'] != $sistema) {
                    continue;
                }
            }

            $service = new Event();
            $service->exchangeArray($item);
            $item['customAttributes']['anexo'] = $item['anexo'];
            $item['customAttributes']['ultimo_progresso'] = $item['ultimo_progresso'];
            $service->setCustomAttributes($item['customAttributes']);
            $lista[$item['customAttributes']['EventID']] = $service;
        }

        $lista = array_values($lista);


        //$grupos = ['cime ce'];

        $cime = [];
        foreach ($grupos as $grupo) {
            $grupo = strtolower($grupo);
            if (preg_match('/cime/', $grupo)) {
                $cime[] = str_replace('cime ', '', $grupo);
            }
        }

        if (count($cime)) {
            $tratado = [];
            foreach ($lista as $item) {
                $location = explode(' - ', $item->getCustomAttribute('municipio_de_aplicacao'));
                if (in_array(strtolower($location[0]), $cime)) {
                    $tratado[] = $item;
                }
            }
            $lista = $tratado;
        }

        $duracao = '';
        if (file_exists('./data/cache/duracao.txt')) {
            $duracao = file_get_contents('./data/cache/duracao.txt');
        }

        return new ViewModel(['lista' => $lista, 'form' => $form, 'sistema' => $sistema, 'categoriasSelecionadas' => $categorias, 'duracao' => $duracao]);
    }

    public function fechadosAction()
    {
        $cacheFile = './data/cache/validados.json';
        $dados = json_decode(file_get_contents($cacheFile), true);

        $form = new \Classes\Form\Triagem();
        $form->get('Categoria')->setAttribute('required', false);

        $dadosQuery = $this->getRequest()->getQuery()->toArray();
        $url = '/validados/fechados';
        $metodo = 'filtro-' . md5($url);
        $container = new \Zend\Session\Container('filtro');

        if (count($dadosQuery)) {
            $container->offsetSet($metodo, $dadosQuery);
            $dataFiltro = $dadosQuery;
        } else {
            $dataFiltro = $container->offsetGet($metodo);
        }

        $filtro = [];
        $categorias = [];
        if (count($dataFiltro) > 0) {
            $filtro = $dataFiltro;
            if (isset($filtro['Categoria'])) {
                $categorias = $filtro['Categoria'];
                unset($filtro['Categoria']);
            }
            $form->setData($filtro);
        }

        $filtroSistema = new Container('SistemaSelecionado');
        $sistema = $filtroSistema->offsetGet('sistema');

        $lista = [];
        foreach ($dados as $item) {
            if ($item['status'] != 'Fechado') continue;
            $dados = $item['customAttributes'];

            if (count($filtro)) {
                $continue = false;

                if (count($categorias)) {
                    $categoriaEventoArr = explode(' - ', $dados['categoria_evento']);
                    if (!in_array($categoriaEventoArr[0], $categorias)) $continue = true;
                }

                if ($filtro['OrigemInformacao'] != '') {
                    if ($filtro['OrigemInformacao'] != $dados['origem_informacao']) $continue = true;
                }
                if ($filtro['DataRegistro'] != '') {
                    $epoc = new Event();
                    $dataRegistro = $epoc->epocToDate($dados['Created']);
                    if ($dataRegistro != $filtro['DataRegistro']) $continue = true;
                }
                if ($filtro['UfFiltro'] != '') {
                    if ($filtro['UfFiltro'] != $dados['unidade_federativa']) $continue = true;
                }

                if ($continue) continue;
            }


            if ($sistema) {
                if ($dados['sistema'] != $sistema) {
                    continue;
                }
            }

            $service = new Event();
            $service->exchangeArray($item);
            $item['customAttributes']['anexo'] = $item['anexo'];
            $service->setCustomAttributes($item['customAttributes']);
            $lista[$item['customAttributes']['EventID']] = $service;
        }

        $lista = array_values($lista);

        $usuarioApi = new UsuarioApi();
        $grupos = $usuarioApi->get('perfis');
        //$grupos = ['cime ce'];

        $cime = [];
        foreach ($grupos as $grupo) {
            $grupo = strtolower($grupo);
            if (preg_match('/cime/', $grupo)) {
                $cime[] = str_replace('cime ', '', $grupo);
            }
        }

        if (count($cime)) {
            $tratado = [];
            foreach ($lista as $item) {
                $location = explode(' - ', $item->getCustomAttribute('municipio_de_aplicacao'));
                if (in_array(strtolower($location[0]), $cime)) {
                    $tratado[] = $item;
                }
            }
            $lista = $tratado;
        }

        return new ViewModel(['lista' => $lista, 'form' => $form, 'sistema' => $sistema, 'categoriasSelecionadas' => $categorias]);
    }

    public function editarAction()
    {
        debug(1);
    }

    public function alterarStatusAction()
    {

    }

    public function atualizarAction()
    {
        try {
            $post = $this->getRequest()->getPost();
            $event = new Event();

            $customAttributes = new CustomAttributes();
            $customAttributes->set('status_tratamento', 'Em Tratamento');
            $customAttributes->set('etapas_do_cronograma', 'Exame');

            if (isset($post['Status'])) {
                if ($post['Status'] == 1) {
                    $event->setStatus(1);
                }
                if ($post['Status'] == 2) {
                    $customAttributes->set('status_tratamento', 'Finalizado');
                    $event->setStatus(2);
                }
            }

            $valida = new Event();
            $valida->setCode($post['Code']);
            $valida->load();

            if ($valida->getStatus() != 1 && $post['Status'] != 1) {
                throw new \Exception('Ocorreu um erro ao salvar o progresso, o alerta pode ter sido fechado enquanto você executada a operação. Tente novamente em alguns instantes.');
            }

            $event->exchangeArray($post);
            $event->setCustomAttributes($customAttributes);

            $event->save();

            //$this->addSuccessMessage();
        } catch (\Exception $e) {
            return new JsonModel(['error' => true, 'message' => $e->getMessage()]);
        }

        $status = ['Cancelado', 'Aberto', 'Fechado'];
        $statusNovo = (isset($post['Status'])) ? $post['Status'] : 1;
        return new JsonModel(['error' => false, 'message' => 'Registro Atualizado com Sucesso', 'status' => $status[$statusNovo]]);
    }

    public function gerarCacheAction()
    {
        $time = new \DateTime();
        $statusRotina = './data/status-rotina.txt';
        while (true) {
            //FORCE THE ROTINE TO STOP WHEN IT'S BETWEEN 23:30 AND 23:59
            if ($time->format('Hi') > 2330 && $time->format('Hi') < 2359) {
                echo $time->format('Hi') . ' Break It!' . PHP_EOL;
                break;
            }
            $startDatetime = date('Y-m-d H:i:s');
            echo 'Iniciando Rotina do Cache ' . $startDatetime . PHP_EOL;

            file_put_contents($statusRotina, 1);

            $alerta = new Alertas();
            $alertas = $alerta->filtrarObjeto();
            $tratadoAlerta = [];
            foreach ($alertas as $alerta) {
                if ($alerta->getCodigoRm() && $alerta->getAnexo()) {
                    $tratadoAlerta[$alerta->getCodigoRm()] = explode('|', $alerta->getAnexo());
                }
            }

            $querie = new Queries();
            $filter = new Filter();
            $filter->setPageSize(1000);
            $querie->setFilter($filter);
            $querie->setAnonimo();
            //      $querie->setId('2ecdd805-4ca4-46e8-ab30-93356e26a218'); Querie Abertos
            $querie->setId('4206cad2-b541-4386-b0c3-14a14a5c72b7'); // Querie Todos
            $dados = $querie->fetchAll();

            $eventosAgrupados = $dados;

            $page = 2;
            while (count($dados) == 1000) {
                echo count($eventosAgrupados) . ' Registros Encontrados' . PHP_EOL;
                $filter->setPage($page);
                $querie->setFilter($filter);
                $dados = $querie->fetchAll();
                $eventosAgrupados = array_merge($eventosAgrupados, $dados);
                $page++;
            }

//            $total = array_merge([], $dados);
//            $i = 0;
//            while(count($dados) == 1000){
//                echo count($total).' Registros Encontrados'.PHP_EOL;
//                $i++;
//                $querie = new Queries();
//                $filter = new Filter();
//
//                $filter->setPageSize(1000);
//                $filter->setPage($i);
//
//                $querie->setFilter($filter);
//                $querie->setAnonimo();
//                //      $querie->setId('2ecdd805-4ca4-46e8-ab30-93356e26a218'); Querie Abertos
//                $querie->setId('cd05aa31-3088-4743-b567-4e83bce208db'); // Querie Todos
//                $dados = $querie->fetchAll();
//
//                $total = array_merge($total, $dados);
//            }

            $dados = $eventosAgrupados;

            $lista = [];
            foreach ($dados as $item) {
//                if($item->sistema != "Enem") continue;
                $event = new Event();
                $event->exchangeArray($item);
                $event->setCustomAttributes($item);
                $lista[] = $event;
            }

            echo 'Filtros Setados, Iniciando Leitura' . PHP_EOL;

            echo 'Leitura Completa, processando os dados' . PHP_EOL;

            $downloadAnexo = file_get_contents('./data/settings/download-anexo.txt');

            $cache = [];
            //$totalGeral = 0;
            /** @var $lista \RiskManager\Workflow\Entity\Event[] */
            foreach ($lista as $item) {
                $dados = $item->toArray();
                $dados['customAttributes'] = $item->getCustomAttributes(true);
                $dados['anexo'] = [];
                $dados['ultimo_progresso'] = '';

                $eventService = new Event();
                $eventService->setCode($dados['customAttributes']['EventID']);
                $eventService->setAnonimo();

                if ($item->getStatus() == 'Aberto') {
                    $updates = $eventService->getUpdates();
                    $updateReady = '';
                    if (count($updates)) {
                        $update = $updates[0];
                        $valor = (isset($update->NewValue)) ? $update->NewValue : $update->Comment;

                        if (!isset($update->Property)) {
                            if ($valor != '') {
                                $updateReady = $item->epocToDate($update->Date, 'd/m/Y H:i:s') . ' ' . $valor;
                            }
                        }
                    }
                    $dados['ultimo_progresso'] = $updateReady;
                }

                /**
                 * ADICIONADO MAIS UMA CONDIÇÃO - STATUS = ABERTO
                 * 8/11/2018 - bruno.rosa
                 */
                if ($downloadAnexo == '1' && $item->getStatus() == 'Aberto') {
                    try {
                        $anexos = $eventService->getProgressAttachment();
                        if (count($anexos)) {
                            foreach ($anexos as $anexo) {
                                $base = './public/anexos/' . $anexo->Id . '_' . $anexo->FileName;
                                if (!file_exists($base)) {
                                    $data = $eventService->getProgressAttachment($anexo->Id);
                                    file_put_contents($base, base64_decode($data->Data));
                                }

                                $fileName = '/anexos/' . $anexo->Id . '_' . $anexo->FileName;
                                $dados['anexo'][] = $fileName;
                            }
                        }
                    } catch (\Exception $e) {
                        echo 'Erro ao ler anexos' . PHP_EOL;
                    }
                }

                if (isset($tratadoAlerta[$dados['customAttributes']['EventID']])) {
                    $dados['anexo'] = $tratadoAlerta[$dados['customAttributes']['EventID']];
                }

                $cache[] = $dados;
            }

            echo 'Dados Processados. ' . count($cache) . ' registros no cache' . PHP_EOL;

            $cacheFile = './data/cache/validados.json';
            file_put_contents($cacheFile, json_encode($cache));
            file_put_contents($statusRotina, 0);

            echo 'Cache Realizado as ' . date('Y-m-d H:i:s') . PHP_EOL;

            file_put_contents('./data/validados_history.txt', 'Sincronizado as: ' . date('d/m/Y H:i:s'));

            if (APPLICATION_ENV == 'production') {
                echo 'Registrando  Ocorrencias no Cache Remoto' . date('Y-m-d H:i:s') . PHP_EOL;
                /// Gera cache dos validados para filtro do CMI
                $file = '\\\DOVERLANDIA\cmi\\cache_alertas.json';
                file_put_contents($file, json_encode($cache));

                echo 'Registrando  Alertas no Cache Remoto' . date('Y-m-d H:i:s') . PHP_EOL;
                ///Gera cache dos Alertas para filtro CMI
                $ocorrencia = new Alertas();
                $ocorrencia->setAno(date('Y'));
//                $ocorrencia->setSistema('Enem');
                $dadosOcorrencias = $ocorrencia->filtrarObjeto();
                $file = '\\\DOVERLANDIA\cmi\\cache_ocorrencias.json';
                file_put_contents($file, json_encode($dadosOcorrencias->toArray()));

                echo 'Registrando  Última Milha no Cache Remoto' . date('Y-m-d H:i:s') . PHP_EOL;
                $milha = new UltimaMilha();
                $listaMilha = $milha->filtrarObjeto();
                $file = '\\\DOVERLANDIA\cmi\\cache_milha.json';
                file_put_contents($file, json_encode($listaMilha->toArray()));
            } else {
                echo 'Registrando  Ocorrencias no Cache Remoto' . date('Y-m-d H:i:s') . PHP_EOL;
                /// Gera cache dos validados para filtro do CMI
                $file = './public/filter/cache_alertas.json';
                file_put_contents($file, json_encode($cache));

                echo 'Registrando  Alertas no Cache Remoto' . date('Y-m-d H:i:s') . PHP_EOL;
                ///Gera cache dos Alertas para filtro CMI
                $ocorrencia = new Alertas();
                $ocorrencia->setAno(date('Y'));
//                $ocorrencia->setSistema('Enem');
                $dadosOcorrencias = $ocorrencia->filtrarObjeto();

                $file = './public/filter/cache_ocorrencias.json';
                file_put_contents($file, json_encode($dadosOcorrencias->toArray()));

                echo 'Registrando  Última Milha no Cache Remoto' . date('Y-m-d H:i:s') . PHP_EOL;
                $milha = new UltimaMilha();
                $listaMilha = $milha->filtrarObjeto();
                $file = './public/filter/cache_milha.json';
                file_put_contents($file, json_encode($listaMilha->toArray()));
            }
            $duracaoText = $this->checkDiffDatetime($startDatetime);
            file_put_contents('./data/cache/duracao.txt', $duracaoText);
            echo $duracaoText . PHP_EOL;
        }

        echo 'Saiu do While';
        die;
    }

    public function checkDiffDatetime($datetime)
    {
        $dateServer = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
        $now = new \DateTime();
        $interval = $dateServer->diff($now);
        return "ÚLTIMA ATUALIZAÇÃO " . date('d/m/Y H:i:s') . " - PRÓXIMA ATUALIZAÇÃO EM {$interval->h} hora(s), {$interval->i} minuto(s), {$interval->s} segundos(s). ";
    }

}
