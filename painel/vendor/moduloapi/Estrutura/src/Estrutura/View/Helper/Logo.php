<?php
namespace Estrutura\View\Helper;
use Estrutura\Service\Config;
use Zend\View\Helper\AbstractHelper;

class Logo extends AbstractHelper
{
    public function __invoke()
    {
        $api = Config::getConfig('API');
        $url = $api['baseRM'].$api['patchRM']."/Organization/Logo";
        
        return $url;
    }
}