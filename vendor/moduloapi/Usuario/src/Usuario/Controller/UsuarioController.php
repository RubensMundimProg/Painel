<?php

namespace Usuario\Controller;

use Estrutura\Controller\AbstractCrudController;
use Usuario\Service\Usuario;
use Zend\Session\Container;

class UsuarioController extends AbstractCrudController
{
    /**
     * @var \Usuario\Service\Usuario
     */
    protected $service;

    /**
     * @var \Usuario\Form\Usuario
     */
    protected $form;

    public function __construct(){
        parent::init();
    }

    public function indexAction()
    {
        return parent::index($this->service, $this->form);
    }

    public function gravarAction(){
        return parent::gravar($this->service, $this->form);
    }

    public function cadastroAction()
    {
        return parent::cadastro($this->service, $this->form);
    }

    public function excluirAction()
    {
        return parent::excluir($this->service, $this->form);
    }

    public function sairAction(){
       $container = new Container('UsuarioApi');
       $container->offsetUnset('id');

       $this->addSuccessMessage('Usuário deslogado com sucesso');

       $config = \Estrutura\Service\Config::getConfig('API');

       return $this->redirect()->toUrl($config['baseRM'].$config['patchRM']);
    }

    public function autenticarAction(){
        try{
            $usuario = $this->getServiceLocator()->get('Usuario');

            if($usuario){
                return $this->redirect()->toRoute('application');
            }

            $request = $this->getRequest();

            if($request->isPost()){
                $post = $request->getPost();
                $usuario = new Usuario();
                $usuario->exchangeArray($post);
                $usuario->autenticar();

                $this->addSuccessMessage('Bem vindo '.$usuario->getNome());

                return $this->redirect()->toRoute('application');
            }

            $this->layout('layout/autenticar');

        }catch (\Exception $e){
            $this->addErrorMessage($e->getMessage());
            return $this->redirect()->toRoute('usuario-home',['action'=>'autenticar']);
        }
    }
}
