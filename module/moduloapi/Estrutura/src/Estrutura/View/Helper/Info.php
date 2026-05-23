<?php
namespace Estrutura\View\Helper;
use Laminas\Session\Container;
use Laminas\View\Helper\AbstractHelper;
use Modulo\Service\RiskManager;

class Info extends AbstractHelper
{
    public function __invoke()
    {
        $riskmanager = new RiskManager();
        $riskmanager->authAnonymous();
        $info = json_decode($riskmanager->getInfo());

        return $info;

    }
}
