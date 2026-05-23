<?php
namespace Estrutura\View\Helper;
use Laminas\Session\Container;
use Laminas\View\Helper\AbstractHelper;

class Usuario extends AbstractHelper
{
    public function __invoke()
    {
        $container = new Container('UsuarioApi');
        $id = $container->offsetGet('id');

        $objUsuario = new \Usuario\Service\Usuario();
        if(!$id){
            $objUsuario->setNome('Não Logado');
        }else{
            $objUsuario->setNome($container->offsetGet('nome'));
        };

        return $objUsuario;
    }
}
