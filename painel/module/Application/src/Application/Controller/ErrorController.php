<?php

namespace Application\Controller;

use Estrutura\Controller\AbstractEstruturaController;
use Modulo\Service\ApiSession;
use Modulo\Service\RiskManager;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class ErrorController extends AbstractEstruturaController {
    
    public function naoAutorizadoAction(){
        $this->layout('layout/clean');
        return new ViewModel();
    }
    
}
