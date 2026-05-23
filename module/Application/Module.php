<?php

namespace Application;

use Api\Exception\ApiException;
use Application\View\Helper\CurrentRequest;
use Classes\Service\Acesso;
use Estrutura\Controller\AbstractEstruturaController;
use Estrutura\Form\AbstractForm;
use Estrutura\Service\AbstractEstruturaService;
use Modulo\Service\UsuarioApi;
use RiskManager\MySpace\Service\Me;
use RiskManager\OData\TokenDetails;
use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Translator\Translator as ValidatorTranslator;
use Estrutura\Service\Config;
use Modulo\Service\RiskManager;

class Module
{
    public function onBootstrap(MvcEvent $e)
{
    $eventManager        = $e->getApplication()->getEventManager();
    $moduleRouteListener = new ModuleRouteListener();
    $moduleRouteListener->attach($eventManager);

    $serviceManager = $e->getApplication()->getServiceManager();

    $moduleManager = $serviceManager->get('ModuleManager');
    $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
    $sharedEvents->attach(
        'Laminas\Mvc\Controller\AbstractController',
        MvcEvent::EVENT_DISPATCH,
        array($this, 'controllerDispatch'),
        100
    );

    $translator = $serviceManager->get('translator');
    $translator->addTranslationFile(
        'phpArray',
        './vendor/laminas/laminas-i18n-resources/languages/pt_BR/Laminas_Validate.php'
    );

    AbstractValidator::setDefaultTranslator(new ValidatorTranslator($translator));

    $e->getApplication()->getEventManager()->attach('route', array($this, 'checkAcl'));
}

    /**
     * @param MvcEvent $e
     * @return null|\Laminas\Http\PhpEnvironment\Response
     */
   public function controllerDispatch(MvcEvent $e)
    {

        $serviceManager = $e->getApplication()->getServiceManager();
        AbstractEstruturaService::setServiceManager($serviceManager);
        AbstractForm::setServiceManager($serviceManager);
        AbstractEstruturaController::setServiceManager($serviceManager);
        CurrentRequest::setServiceManager($serviceManager);
        $locator = $serviceManager;
        $route     = $e->getTarget()->getEvent()->getRouteMatch()->getParams();
        $controller  = $e->getTarget();
        $this->syncModernUiPreference($e);

        // Rotas que não precisam de verificação de sessão
        $listaBranca = ['Callback', 'Auth', 'autenticacao', 'twitter', 'api'];
        $isConsoleRequest = class_exists('Laminas\Console\Request')
            && $controller->getRequest() instanceof \Laminas\Console\Request;
        if ($isConsoleRequest || in_array($route['controller'], $listaBranca)) {
            return true;
        }

        // Se o usuário não estiver logado, inicia o processo de autenticação
        if (!$locator->get("UsuarioApi")) {
            // Pega o nome da rota atual
            $routeName = $e->getRouteMatch()->getMatchedRouteName();

            // LÓGICA DE REDIRECIONAMENTO DIFERENCIADA
            if ($routeName === 'mobile') {
                // Se for a rota 'mobile', adiciona o parâmetro de origem
                $redirectUrl = '/autenticacao?redirect=' . urlencode('/mobile') . '&origem=mobile';
            } else {
                // Comportamento padrão para todas as outras rotas
                $requestUri = $e->getApplication()->getRequest()->getUriString();
                $redirectUrl = '/autenticacao?redirect=' . urlencode($requestUri);
            }
            
            $controller->plugin('redirect')->toUrl($redirectUrl);
            $e->stopPropagation();
            return false;
        }

        // Lógica da API
                                                     
                                                             

                 
                                                        
                                                    

        if ($route['controller'] == 'api') {
            try {
                                                        
                ini_set('max_exection_time', 0);
                set_time_limit(0);

                $header = $controller->getRequest()->getHeaders()->toArray();
                                             

                if (!isset($header['Usuario'])) throw new \Exception('Usuário não autenticado');

                return true;
            } catch (\Exception $ex) {
                echo json_encode(['error' => true, 'details' => $ex->getMessage()]);
                die;
            }
        }

        // Se estiver logado, continua com a verificação de inatividade
         
         
        if (!in_array($route['controller'], ['dashboard', 'twitter', 'rss', 'mobile'])) {

                                                     
            $acessoContainer = new Container('ChaveAcesso');

            if (!$acessoContainer->offsetGet('chave')) {
                $controller->addErrorMessage('Sessão desativada por tempo de inatividade');
                if (!$route['action'] == 'sair' && $route['controller'] == 'index') {
                    $controller->plugin('redirect')->toUrl('/index/sair');
                } else {
                    $controller->plugin('redirect')->toUrl('/autenticacao');
                }
                $e->stopPropagation();
                return false;
            }

            $acesso      = new Acesso();
            $acesso->setId($acessoContainer->offsetGet('chave'));
            $dadosAcesso = $acesso->filtrarObjeto()->current();

            if (!$dadosAcesso) {
                $controller->addErrorMessage('Sessão desativada por tempo de inatividade');
                if (!$route['action'] == 'sair' && $route['controller'] == 'index') {
                    $controller->plugin('redirect')->toUrl('/index/sair');
                } else {
                    $controller->plugin('redirect')->toUrl('/autenticacao');
                }
                $e->stopPropagation();
                return false;
            } else {
                $dadosAcesso->setDataAcesso(date('Y-m-d H:i:s'));
                $dadosAcesso->salvar();
            }

        }

        $this->settingsDefault();

        if ($this->shouldUseModernLayout($e, $route)) {
            $controller->layout('layout/modern');
        }

        return true;
    }

    private function syncModernUiPreference(MvcEvent $e)
    {
        $request = $e->getApplication()->getRequest();
        if (!method_exists($request, 'getQuery')) {
            return;
        }

        $ui = $request->getQuery('ui', null);
        $interface = new Container('InterfacePreferencias');

        if ($ui === 'modern') {
            $interface->offsetSet('ui', 'modern');
        }

        if ($ui === 'classic') {
            $interface->offsetUnset('ui');
        }
    }

    private function shouldUseModernLayout(MvcEvent $e, array $route)
    {
        $interface = new Container('InterfacePreferencias');
        if ($interface->offsetGet('ui') !== 'modern') {
            return false;
        }

        $request = $e->getApplication()->getRequest();
        if (method_exists($request, 'isXmlHttpRequest') && $request->isXmlHttpRequest()) {
            return false;
        }

        $controller = $route['controller'] ?? '';
        $excludedControllers = [
            'api',
            'autenticacao',
            'dashboard',
            'dashboard-data',
            'dashboard-index',
            'error',
            'mobile',
            'rss',
            'twitter',
            'Callback',
            'Auth',
        ];

        return !in_array($controller, $excludedControllers, true);
    }

    public function settingsDefault()
    {
        $sistema = new \Laminas\Session\Container('SistemaSelecionado');
        $avaliacaoPedagogica = $sistema->offsetGet('sistema');
        if ($avaliacaoPedagogica == '') {
            $sistema->offsetSet('sistema',file_get_contents('./data/settings/avaliacao-pedagogica-padrao.txt'));
        }
        $diaAplicacao = $sistema->offsetGet('diaAplicacao');
        if ($diaAplicacao == '') {
            $sistema->offsetSet('diaAplicacao',file_get_contents('./data/settings/dia-aplicacao-padrao.txt'));
        }
        return true;
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getAcl(){
        return include __DIR__ . '/config/acl.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Laminas\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
     //           'currentRequest' => 'Application\View\Helper\CurrentRequest',
            ),
        );
    }



    /**
     * Verifica as Permissões
     */
    public function checkAcl(MvcEvent $e){
        /// Verifica se a Aplicação passa por ACL
        if(!Config::getConfig('VEFIFICA_ACL')){
            return true;
        };

        $routeMatch = $e->getRouteMatch();
        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');

        $configuracoes = $this->getAcl();

        $usuarioApi = new UsuarioApi();
        $grupos = $usuarioApi->get('perfis');

        $permitido = false;

        if(!is_array($grupos)){
            $grupos = [];
            $permitido = true;
        }

        foreach($grupos as $grupo){
           $nomeGrupo = strtolower(trim($grupo));
            if(isset($configuracoes[$controller][$action])){
                $permitidos = $configuracoes[$controller][$action];
                if(in_array($nomeGrupo, $configuracoes[$controller][$action])){
                    $permitido = true;
                }
            }else{
                $permitido = true;
            }
        }

        if($permitido) return true;

        if($controller != 'error'){
            $e->getApplication()->getEventManager()->getSharedManager()->attach('Laminas\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
                    $controller = $e->getTarget();
                        $controller->plugin('redirect')->toRoute('nao-autorizado');
            }, 100);
        }
    }

}
