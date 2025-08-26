<?php
namespace Estrutura\View\Helper;
use Application\Module as App;
use Zend\Session\Container;
use Zend\View\Helper\AbstractHelper;

class Acl extends AbstractHelper
{
    public function __invoke()
    {

        $app = new App();
        return $app->getAcl();

    }
}