<?php

namespace Application\Controller;

use Estrutura\Controller\AbstractEstruturaController;
use Modulo\Service\UsuarioApi;
use Zend\View\Model\ViewModel;

class AjudaController extends AbstractEstruturaController{

    public function indexAction(){
        $this->layout('layout/ajuda');
        return new ViewModel();
    }

    public function downloadManualAction(){
        $this->layout('layout/ajuda');

        mb_internal_encoding('UTF-8');

        $manualDir = scandir('public/manuais/');

        $manuaisTratados = [];
        foreach ($manualDir as $item) {
            if(in_array($item,['.','..'])) continue;
            $aux = str_replace('.pdf','',explode('_',$item)[1]);
            $manuaisTratados[] = mb_strtolower($aux);
        }

        $usuarioApi = new UsuarioApi();
        $grupos = $usuarioApi->get('perfis');

        $manual = '';
        foreach ($grupos as $grupo) {
            foreach ($manuaisTratados as $manuaisTratado) {
                if(substr_compare($grupo,$manuaisTratado,1,9)){
                    $manual = 'Manual Perfil_'.$manuaisTratado.'.pdf';
                }
            }
        }

        $this->redirect()->toUrl('/manuais/'.$manual);
    }

}
