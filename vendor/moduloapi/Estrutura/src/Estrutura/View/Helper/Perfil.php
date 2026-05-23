<?php
namespace Estrutura\View\Helper;
use Estrutura\Service\Config;
use Modulo\Service\UsuarioApi;
use RiskManager\Organization\Service\People;
use Usuario\Module;
use Zend\Mvc\Application;
use Zend\Session\Container;
use Zend\View\Helper\AbstractHelper;
use Modulo\Service\RiskManager;
use Application\Module as App;

class Perfil extends AbstractHelper
{
    protected $acl;

    public function __invoke($controller = NULL, $action = NULL)
    {
        $valida = Config::getConfig('VEFIFICA_ACL');
        if(!$valida) return true;

        $usuario = new Container('UsuarioApi');
        if(!$usuario->id){
            return false;
        }

        $apiUser = new UsuarioApi();
        $grupos = $apiUser->get('perfis');

        $this->acl = new App();
        $configuracoes = $this->acl->getAcl();

        $permitido = false;
        foreach($grupos as $grupo){
            $nomeGrupo = strtolower(trim($grupo));
            if(isset($configuracoes[$controller][$action])){
                $permitidos = $configuracoes[$controller][$action];
                if(in_array($nomeGrupo, $permitidos)){
                    $permitido = true;
                }
            }else{
                $permitido = true;
            }
        }

        return $permitido;
/*

        $permitido = false;
        foreach($grupos as $grupo){
            $nameGrupo = strtolower($grupo);

            if(isset($configuracoes[$nameGrupo])) {
                if (in_array('*', $configuracoes[$nameGrupo])) {
                    $permitido = true;
                }
            }

            if(isset($configuracoes[$nameGrupo][$controller])){
                if(in_array($action, $configuracoes[$nameGrupo][$controller])){
                    $permitido = true;
                }
// Aberto
                if(in_array('*', $configuracoes[$nameGrupo][$controller])){
                    $permitido = true;
                }
            }
        }

        return $permitido;
*/
    }
}