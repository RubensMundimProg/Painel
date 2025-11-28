<?php
namespace Estrutura\View\Helper;
use Estrutura\Service\Config;
use Laminas\View\Helper\AbstractHelper;

class Projeto extends AbstractHelper
{
    public function __invoke()
    {
        $projeto = Config::getConfig('nomeProjeto');
        return $projeto;
    }
}