<?php

namespace Usuario\Service;

Use \Usuario\Entity\Usuario as Entity;
use Zend\Session\Container;

class Usuario extends Entity{
    private function criptografar($senha){
        for($i=0;$i<64000;$i++){
            $senha = md5($senha);
        }

        return $senha;
    }

    public function autenticar(){
        $senha = $this->getSenha();
        $enc = $this->criptografar($senha);
        $this->setSenha($enc);

        $usuario = $this->filtrarObjeto()->current();

        if(!$usuario)
            throw new \Exception("Dados inválidos");

        $this->usuarioSessao( $usuario );
    }

    private function usuarioSessao(\Usuario\Service\Usuario $usuario){
        $container = new Container('Usuario');
        $container->offsetSet('id',$usuario->getId());
    }

    public function preSave(){
        parent::preSave();

        $senha = $this->criptografar($this->getSenha());
        $this->setSenha($senha);
    }

}