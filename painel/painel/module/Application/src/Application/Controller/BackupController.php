<?php

namespace Application\Controller;

use Application\Service\Backup;
use Estrutura\Controller\AbstractEstruturaController;

class BackupController extends AbstractEstruturaController
{

    public function startAction()
    {
        ini_set('memory_limit', '-1');
        $bkp = new Backup();
        $bkp->backup();
        die;
    }

}
