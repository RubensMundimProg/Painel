<?php
namespace Base\View\Helper;

use Estrutura\Service\Config;
use Zend\View\Helper\AbstractHelper;

class RiskManager extends AbstractHelper
{
    protected $ambiente;

    public function __invoke()
    {
        $this->params = $this->initialize();
        return $this;
    }

    protected function initialize()
    {
        if(!$this->ambiente){
            $data = Config::getConfig('API');
            $this->ambiente = $data;
            $this->ambiente['urlRM'] = $data['baseRM'].$data['patchRM'];
        }

        return $this;
    }

    public function get($campo){
        return $this->ambiente[$campo];
    }
}