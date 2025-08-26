<?php

namespace Modulo\Service;

use Classes\Service\Acesso;
use Zend\Session\Container;

class UsuarioApi extends ServiceAPi{

    protected  $container = false;

    public function logar($details)
    {
        if(!$details->Name) return true;

        /// Armazena a chave de acesso
        $acesso = new Acesso();
        $acesso->setUsuario($details->Name);
        $acesso->excluir();

        $acesso->setIp($_SERVER['REMOTE_ADDR']);
        $acesso->setNavegador($_SERVER['HTTP_USER_AGENT']);
        $acesso->setDataAcesso(date('Y-m-d H:i:s'));
        $acesso->salvar();

        $container = new Container('ChaveAcesso');
        $container->offsetSet('chave', $acesso->getId());

        $container = new Container('UsuarioApi');
        $container->offsetSet('id',$details->Id);
        $container->offsetSet('nome',$details->Name);
        $container->offsetSet('email',$details->Email);
        $container->offsetSet('login',$details->Login);
        $container->offsetSet('perfis',$details->Perfis);
        if (isset($details->Dados)) $container->offsetSet('dados',$details->Dados);
        if (isset($details->Ssi)) $container->offsetSet('ssi',$details->Ssi);

        return true;
    }

    public function sair(){
        session_destroy();
    }

    public function get($chave){
        $container = new Container('UsuarioApi');
        return $container->offsetGet($chave);
    }

    public function isConectado(){
        $container = new Container('UsuarioApi');
        $id = $container->offsetGet('id');
        if(!$id) return false;

        return true;
    }
}