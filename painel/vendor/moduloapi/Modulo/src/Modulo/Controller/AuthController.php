<?php

namespace Modulo\Controller;


use Estrutura\Controller\AbstractEstruturaController;
use Modulo\Service\ApiSession;
use Modulo\Service\OAuth;

class AuthController extends AbstractEstruturaController{
    public function indexAction(){
        $ApiSession = new ApiSession();
        if(!$ApiSession->get('token')){
            return $this->redirect()->toRoute('autenticar');
        }

        $this->redirect()->toRoute('home');
    }

    public function loginAction(){
        $apiSession = new ApiSession();
        $apiSession->destroy();

        $auth = new OAuth();
        $url = $auth->login();
        return $this->redirect()->toUrl($url);
    }

    public function getTokenAction(){
        try{
            $request = $this->getRequest();
            $post = $request->getPost();
            $code = $post['auth'];
            $auth = new OAuth();
            $detalhesToken = $auth->getToken($code);

        }catch (\Exception $e){
           debug($e->getMessage());
        }
    }
} 