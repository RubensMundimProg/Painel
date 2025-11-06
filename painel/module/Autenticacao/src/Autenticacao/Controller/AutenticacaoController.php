<?php

namespace Autenticacao\Controller;

use Estrutura\Controller\AbstractEstruturaController;
use Modulo\Service\OAuth;
use Autenticacao\Service\Autenticador;
use Estrutura\Service\Config;
use Modulo\Service\UsuarioApi;
use Zend\Mail\Protocol\Smtp\Auth\Login;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class AutenticacaoController extends AbstractEstruturaController{

    public function indexAction()
    {
        $apiSession = new \Modulo\Service\ApiSession();
        $apiSession->destroy();

        $auth_context = $this->params()->fromQuery('origem', 'default');
        
        $auth = new OAuth();
        $url = $auth->login($auth_context);
        
        return $this->redirect()->toUrl($url);
    }
    public function entrarAction()
    {
        try{
            $lockSsi = file_get_contents('./data/settings/lock-ssi.txt');
            if($lockSsi) throw new \Exception('Falha ao autenticar com o SSI');
            $form = new \Autenticacao\Form\Login();
            $request = $this->getRequest();
            if($request->isPost()) {
                $post = $request->getPost()->toArray();
                $form->setData($post);
                if (!$form->isValid()) {
                    $this->addValidateMessages($form);
                    return $this->setRedirect('/autenticacao', $post);
                }
                $autenticador = new Autenticador();
                $retorno = $autenticador->validate($post['login'],$post['password']);
                if($retorno['response']['status'] == 'FALHA'){
                    $msg = '';
                    foreach ($retorno['response']['messages'] as $item) {
                        $msg .= $item." ";
                    }
                    $this->addErrorMessage($msg);
                    return $this->setRedirect('/autenticacao', $post);
                }
                $email = '';
                foreach ($retorno['response']['result']['usuarioSistema']['usuario']['contatos'] as $contato) {
                    if($contato['ativo'] == true && preg_match('/@/',$contato['txContato'])){
                        $email = $contato['txContato'];
                        break;
                    }
                }
                $perfis = [];
                foreach ($retorno['response']['result']['usuarioSistema']['perfis'] as $perfil) {
                    $perfis[] = $perfil['nome'];
                }
                $details = new \stdClass();
                $details->Id = $retorno['response']['result']['usuarioSistema']['id'];
                $details->Name = $retorno['response']['result']['usuarioSistema']['usuario']['nome'];
                $details->Email = $email;
                $details->Login = $retorno['response']['result']['usuarioSistema']['usuario']['login'];
                $details->Perfis = $perfis;
                $details->Dados = $retorno['response']['result']['usuarioSistema'];
                $details->Ssi = true;
                $userApi = new \Modulo\Service\UsuarioApi();
                $userApi->logar($details);
                return $this->setRedirect('/');
            }
            return $this->setRedirect('/autenticacao');
        } catch (\Exception $e) {
            $this->addErrorMessage($e->getMessage());
            return $this->setRedirect('/autenticacao', $post);
        }
    }

    public function sairAction()
    {
        $container = new Container('UsuarioApi');
        $container->offsetUnset('id');
        $this->addSuccessMessage('Usuário deslogado com sucesso');
        $config = $this->getServiceLocator()->get('config')['API'];
        return $this->redirect()->toUrl($config['baseRM'].$config['patchRM']);
    }
}