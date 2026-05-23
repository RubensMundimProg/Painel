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
use Laminas\Session\Container;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;

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
        $container = new \Laminas\Session\Container('filtro');
        $container->offsetUnset($metodo);

        $interface = new Container('InterfacePreferencias');
        $query = ($this->params()->fromQuery('ui') === 'modern' || $interface->offsetGet('ui') === 'modern') ? '?ui=modern' : '';

        return $this->redirect()->toUrl($url . $query);
    }

    public function indexAction()
    {
        $cacheFile = './data/cache/validados.json';
        if ($this->params()->fromQuery('refresh') == '1' || !file_exists($cacheFile) || filesize($cacheFile) === 0) {
            $refresh = $this->gerarCacheAction();
            if ($refresh instanceof JsonModel && $refresh->getVariable('error')) {
                $this->addErrorMessage($refresh->getVariable('message'));
            }
        }

        $dados = json_decode((string)@file_get_contents($cacheFile), true);
        if (!is_array($dados)) {
            $dados = [];
        }

        $form = new \Classes\Form\Triagem();
        $form->get('Categoria')->setAttribute('required', false);

        $dadosQuery = $this->getRequest()->getQuery()->toArray();
        $interface = new Container('InterfacePreferencias');
        $modernUi = (isset($dadosQuery['ui']) && $dadosQuery['ui'] === 'modern') || $interface->offsetGet('ui') === 'modern';
        if (isset($dadosQuery['ui']) && $dadosQuery['ui'] === 'classic') {
            $modernUi = false;
        }
        unset($dadosQuery['ui'], $dadosQuery['refresh']);
        $url = '/validados';
        $metodo = 'filtro-' . md5($url);
        $container = new \Laminas\Session\Container('filtro');

        if (count($dadosQuery)) {
            $container->offsetSet($metodo, $dadosQuery);
            $dataFiltro = $dadosQuery;
        } else {
            $dataFiltro = $container->offsetGet($metodo);
        }

        $filtro = [];
        $categorias = [];
        //MEXIDO RUBENS
        $dataFiltro = $dataFiltro ?? [];
        //ATE AQUI
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

                if (($filtro['OrigemInformacao'] ?? '') != '') {
                    if ($filtro['OrigemInformacao'] != $dados['origem_informacao']) $continue = true;
                }

                if (($filtro['DataRegistro'] ?? '') != '') {
                    $epoc = new Event();
                    $dataRegistro = $epoc->epocToDate($dados['Created']);
                    if ($dataRegistro != $filtro['DataRegistro']) $continue = true;
                }

                if (($filtro['UfFiltro'] ?? '') != '') {
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

        $viewModel = new ViewModel(['lista' => $lista, 'form' => $form, 'sistema' => $sistema, 'categoriasSelecionadas' => $categorias, 'duracao' => $duracao]);

        if ($modernUi) {
            $this->layout('layout/modern');
            $viewModel->setTemplate('application/validados/index-modern');
        }

        return $viewModel;
    }

    public function fechadosAction()
    {
        $cacheFile = './data/cache/validados.json';
        if ($this->params()->fromQuery('refresh') == '1' || !file_exists($cacheFile) || filesize($cacheFile) === 0) {
            $refresh = $this->gerarCacheAction();
            if ($refresh instanceof JsonModel && $refresh->getVariable('error')) {
                $this->addErrorMessage($refresh->getVariable('message'));
            }
        }

        $dados = json_decode((string)@file_get_contents($cacheFile), true);
        if (!is_array($dados)) {
            $dados = [];
        }

        $form = new \Classes\Form\Triagem();
        $form->get('Categoria')->setAttribute('required', false);

        $dadosQuery = $this->getRequest()->getQuery()->toArray();
        $interface = new Container('InterfacePreferencias');
        $modernUi = (isset($dadosQuery['ui']) && $dadosQuery['ui'] === 'modern') || $interface->offsetGet('ui') === 'modern';
        if (isset($dadosQuery['ui']) && $dadosQuery['ui'] === 'classic') {
            $modernUi = false;
        }
        unset($dadosQuery['ui'], $dadosQuery['refresh']);
        $url = '/validados/fechados';
        $metodo = 'filtro-' . md5($url);
        $container = new \Laminas\Session\Container('filtro');

        if (count($dadosQuery)) {
            $container->offsetSet($metodo, $dadosQuery);
            $dataFiltro = $dadosQuery;
        } else {
            $dataFiltro = $container->offsetGet($metodo);
        }

        $filtro = [];
        $categorias = [];
        //MEXIDO RUBENS
        $dataFiltro = $dataFiltro ?? [];
        //ATE AQUI
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

                if (($filtro['OrigemInformacao'] ?? '') != '') {
                    if ($filtro['OrigemInformacao'] != $dados['origem_informacao']) $continue = true;
                }
                if (($filtro['DataRegistro'] ?? '') != '') {
                    $epoc = new Event();
                    $dataRegistro = $epoc->epocToDate($dados['Created']);
                    if ($dataRegistro != $filtro['DataRegistro']) $continue = true;
                }
                if (($filtro['UfFiltro'] ?? '') != '') {
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

        $viewModel = new ViewModel([
            'lista' => $lista,
            'form' => $form,
            'sistema' => $sistema,
            'categoriasSelecionadas' => $categorias,
            'fechados' => true,
        ]);

        if ($modernUi) {
            $this->layout('layout/modern');
            $viewModel->setTemplate('application/validados/index-modern');
        }

        return $viewModel;
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

            // [GUARDIÃO DO PAINEL]: INÍCIO DA CORREÇÃO
            // 1. Recupera a Avaliação Pedagógica ('sistema') da sessão, conforme definido nas actions de listagem.
            $filtroSistema = new Container('SistemaSelecionado');
            $sistema = $filtroSistema->offsetGet('sistema');
            
            if (!$sistema) {
                // Se não houver sistema na sessão, lança exceção para informar o usuário.
                throw new \Exception('O campo "Sistema" (Avaliação Pedagógica) é obrigatório e não está definido na sessão. Selecione-o na barra superior antes de prosseguir.');
            }
            // [GUARDIÃO DO PAINEL]: FIM DA CORREÇÃO (Recuperação da sessão)


            $customAttributes = new CustomAttributes();
            $customAttributes->set('status_tratamento', 'Em Tratamento');
            $customAttributes->set('etapas_do_cronograma', 'Exame');
            
            // [GUARDIÃO DO PAINEL]: INÍCIO DA CORREÇÃO
            // 2. Adiciona o campo 'sistema' com o valor da Avaliação Pedagógica recuperado da sessão.
            $customAttributes->set('sistema', $sistema); 
            // [GUARDIÃO DO PAINEL]: FIM DA CORREÇÃO (Adição do atributo)

            $statusPost = isset($post['Status']) ? (int)$post['Status'] : null;
            if ($statusPost !== null) {
                if ($statusPost === 1) {
                    $event->setStatus(1);
                }
                if ($statusPost === 2) {
                    $customAttributes->set('status_tratamento', 'Finalizado');
                    $event->setStatus(2);
                }
            }

            $valida = new Event();
            $valida->setCode($post['Code']);
            $valida->load();

            if ($valida->getStatus() != 1 && $statusPost !== 1) {
                throw new \Exception('Ocorreu um erro ao salvar o progresso, o alerta pode ter sido fechado enquanto você executada a operação. Tente novamente em alguns instantes.');
            }

            $event->exchangeArray($post);
            $event->setCustomAttributes($customAttributes);

            $event->save();

            $status = ['Cancelado', 'Aberto', 'Fechado'];
            $statusNovo = $statusPost ?? 1;
            $statusTexto = $status[$statusNovo] ?? 'Aberto';
            $cacheAtualizado = false;

            if ($statusPost !== null && isset($post['Code'])) {
                $cacheAtualizado = $this->updateValidadosCacheStatus(
                    $post['Code'],
                    $statusTexto,
                    $customAttributes,
                    $post['Comment'] ?? null
                );
            }

            //$this->addSuccessMessage();
        } catch (\Exception $e) {
            return new JsonModel(['error' => true, 'message' => $e->getMessage()]);
        }

        return new JsonModel([
            'error' => false,
            'message' => 'Registro Atualizado com Sucesso',
            'status' => $statusTexto,
            'code' => $post['Code'] ?? null,
            'removeFromList' => $statusPost !== null,
            'cacheUpdated' => $cacheAtualizado,
        ]);
    }

    private function updateValidadosCacheStatus($code, $status, ?CustomAttributes $customAttributes = null, $comment = null)
    {
        $cacheUpdated = false;
        $files = [
            './data/cache/validados.json',
            './public/filter/cache_alertas.json',
        ];

        foreach ($files as $file) {
            if ($this->updateStatusInCacheFile($file, $code, $status, $customAttributes, $comment)) {
                $cacheUpdated = true;
            }
        }

        return $cacheUpdated;
    }

    private function updateStatusInCacheFile($file, $code, $status, ?CustomAttributes $customAttributes = null, $comment = null)
    {
        if (!$code || !is_file($file)) {
            return false;
        }

        $contents = @file_get_contents($file);
        if ($contents === false || trim($contents) === '') {
            return false;
        }

        $items = json_decode($contents, true);
        if (!is_array($items)) {
            return false;
        }

        $updated = false;
        $customAttributeValues = $customAttributes ? $customAttributes->getCampos() : [];
        foreach ($items as &$item) {
            if (!$this->cacheItemMatchesCode($item, $code)) {
                continue;
            }

            $item['status'] = $status;
            if (!isset($item['customAttributes']) || !is_array($item['customAttributes'])) {
                $item['customAttributes'] = [];
            }

            $item['customAttributes']['Status'] = $status;
            foreach ($customAttributeValues as $attribute => $value) {
                $item['customAttributes'][$attribute] = $value;
            }

            if ($comment !== null && trim((string)$comment) !== '') {
                $item['ultimo_progresso'] = date('d/m/Y H:i:s') . ' ' . trim((string)$comment);
                $item['customAttributes']['LastProgressComment'] = trim((string)$comment);
            }

            $updated = true;
            break;
        }
        unset($item);

        if (!$updated) {
            return false;
        }

        $json = json_encode($items, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return false;
        }

        return @file_put_contents($file, $json, LOCK_EX) !== false;
    }

    private function cacheItemMatchesCode($item, $code)
    {
        if (!is_array($item)) {
            return false;
        }

        if (isset($item['code']) && (string)$item['code'] === (string)$code) {
            return true;
        }

        return isset($item['customAttributes']['EventID'])
            && (string)$item['customAttributes']['EventID'] === (string)$code;
    }

    public function gerarCacheAction()
    {
        $oneShot = defined('PAINEL_CLI')
            && PAINEL_CLI
            && defined('PAINEL_CLI_COMMAND')
            && PAINEL_CLI_COMMAND === 'cache-validados';
        if (!$oneShot) {
            $oneShot = php_sapi_name() !== 'cli';
        }
        if (!$oneShot) {
            $oneShot = (bool)$this->params()->fromQuery('once', false);
        }

        if ($oneShot) {
            ob_start();
        }

        $time = new \DateTime();
        $statusRotina = './data/status-rotina.txt';
        try {
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

            if (APPLICATION_ENV == 'production' && !$oneShot) {
                echo 'Registrando  Ocorrencias no Cache Remoto' . date('Y-m-d H:i:s') . PHP_EOL;
                /// Gera cache dos validados para filtro do CMI
                $file = '\\\\DOVERLANDIA\\cmi\\cache_alertas.json';
                $this->writeCacheFile($file, json_encode($cache), 'cache_alertas remoto');

                echo 'Registrando  Alertas no Cache Remoto' . date('Y-m-d H:i:s') . PHP_EOL;
                ///Gera cache dos Alertas para filtro CMI
                $ocorrencia = new Alertas();
                $ocorrencia->setAno(date('Y'));
//                $ocorrencia->setSistema('Enem');
                $dadosOcorrencias = $ocorrencia->filtrarObjeto();
                $file = '\\\\DOVERLANDIA\\cmi\\cache_ocorrencias.json';
                $this->writeCacheFile($file, json_encode($dadosOcorrencias->toArray()), 'cache_ocorrencias remoto');

                echo 'Registrando  Última Milha no Cache Remoto' . date('Y-m-d H:i:s') . PHP_EOL;
                $milha = new UltimaMilha();
                $listaMilha = $milha->filtrarObjeto();
                $file = '\\\\DOVERLANDIA\\cmi\\cache_milha.json';
                $this->writeCacheFile($file, json_encode($listaMilha->toArray()), 'cache_milha remoto');
            } else {
                echo 'Registrando  Ocorrencias no Cache Remoto' . date('Y-m-d H:i:s') . PHP_EOL;
                /// Gera cache dos validados para filtro do CMI
                $file = './public/filter/cache_alertas.json';
                $this->writeCacheFile($file, json_encode($cache), 'cache_alertas local');

                echo 'Registrando  Alertas no Cache Remoto' . date('Y-m-d H:i:s') . PHP_EOL;
                ///Gera cache dos Alertas para filtro CMI
                $ocorrencia = new Alertas();
                $ocorrencia->setAno(date('Y'));
//                $ocorrencia->setSistema('Enem');
                $dadosOcorrencias = $ocorrencia->filtrarObjeto();

                $file = './public/filter/cache_ocorrencias.json';
                $this->writeCacheFile($file, json_encode($dadosOcorrencias->toArray()), 'cache_ocorrencias local');

                echo 'Registrando  Última Milha no Cache Remoto' . date('Y-m-d H:i:s') . PHP_EOL;
                $milha = new UltimaMilha();
                $listaMilha = $milha->filtrarObjeto();
                $file = './public/filter/cache_milha.json';
                $this->writeCacheFile($file, json_encode($listaMilha->toArray()), 'cache_milha local');
            }
            $duracaoText = $this->checkDiffDatetime($startDatetime);
            file_put_contents('./data/cache/duracao.txt', $duracaoText);
            echo $duracaoText . PHP_EOL;

            if ($oneShot) {
                $log = ob_get_clean();
                return new JsonModel(['error' => false, 'message' => 'Cache atualizado com sucesso.', 'dados' => ['log' => $log]]);
            }
        }
        } catch (\Throwable $e) {
            file_put_contents($statusRotina, 0);
            file_put_contents('./data/erro_validados_cache.txt', '[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL, FILE_APPEND);

            if ($oneShot) {
                $log = ob_get_clean();
                return new JsonModel(['error' => true, 'message' => 'Erro ao atualizar cache dos validados: ' . $e->getMessage(), 'dados' => ['log' => $log]]);
            }

            throw $e;
        }

        if ($oneShot) {
            $log = ob_get_clean();
            return new JsonModel(['error' => false, 'message' => 'Rotina encerrada.', 'dados' => ['log' => $log]]);
        }

        echo 'Saiu do While';
        die;
    }

    private function writeCacheFile($file, $contents, $label)
    {
        $dir = dirname($file);
        $isUncPath = strncmp($dir, '\\\\', 2) === 0;

        if (!is_dir($dir) && !$isUncPath) {
            @mkdir($dir, 0775, true);
        }

        if (!is_dir($dir)) {
            $this->logCacheWarning('Destino indisponivel para ' . $label . ': ' . $dir);
            echo 'Aviso: destino indisponivel para ' . $label . ': ' . $dir . PHP_EOL;
            return false;
        }

        if (@file_put_contents($file, $contents) === false) {
            $this->logCacheWarning('Nao foi possivel gravar ' . $label . ': ' . $file);
            echo 'Aviso: nao foi possivel gravar ' . $label . ': ' . $file . PHP_EOL;
            return false;
        }

        return true;
    }

    private function logCacheWarning($message)
    {
        file_put_contents('./data/erro_validados_cache.txt', '[' . date('Y-m-d H:i:s') . '] WARNING: ' . $message . PHP_EOL, FILE_APPEND);
    }

    public function checkDiffDatetime($datetime)
    {
        $dateServer = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
        $now = new \DateTime();
        $interval = $dateServer->diff($now);
        return "ÚLTIMA ATUALIZAÇÃO " . date('d/m/Y H:i:s') . " - PRÓXIMA ATUALIZAÇÃO EM {$interval->h} hora(s), {$interval->i} minuto(s), {$interval->s} segundos(s). ";
    }

}
