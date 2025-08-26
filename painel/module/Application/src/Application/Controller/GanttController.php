<?php

namespace Application\Controller;

use Estrutura\Controller\AbstractEstruturaController;
use Zend\View\Model\ViewModel;

class GanttController extends AbstractEstruturaController {

    public function indexAction()
    {
        return new ViewModel();
    }

    public function dataAction()
    {
        echo str_replace(['-0300','-0200'],[''],file_get_contents('./data/cache/gantt.json'));
        die;
    }

}