<?php

namespace Modulo\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ModuloController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}
