<?php

namespace Modulo\Controller;

use Estrutura\Controller\AbstractEstruturaController;
use Modulo\Service\ApiSession;
use Modulo\Service\OAuth;

class AuthController extends AbstractEstruturaController
{
    public function indexAction()
    {
        $ApiSession = new ApiSession();
        if (!$ApiSession->get('token')) {
            return $this->redirect()->toRoute('autenticar');
        }
        $this->redirect()->toRoute('home');
    }

    public function loginAction()
    {
        $apiSession = new ApiSession();
        $apiSession->destroy();

        // Pega o contexto diretamente do parâmetro da URL. Mais confiável que o HTTP_REFERER.
        // Se o parâmetro não for passado, assume 'default' como padrão.
        $context = $this->params()->fromQuery('context', 'default');

        $auth = new OAuth();
        // Passa o contexto para o serviço OAuth.
        $url = $auth->login($context);
        
        return $this->redirect()->toUrl($url);
    }

    public function getTokenAction()
    {
        try {
            $request = $this->getRequest();
            $post = $request->getPost();
            $code = $post['auth'];
            $auth = new OAuth();
            // Precisamos passar o contexto aqui também no futuro, mas para o fluxo de login inicial,
            // o getToken é chamado no callback, onde já tratamos o contexto.
            $detalhesToken = $auth->getToken($code); 
        } catch (\Exception $e) {
            debug($e->getMessage());
        }
    }
}