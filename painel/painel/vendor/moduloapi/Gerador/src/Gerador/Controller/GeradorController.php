<?php

namespace Gerador\Controller;

use Estrutura\Controller\AbstractEstruturaController;
use Gerador\Service\Gerador;
use Gerador\Service\GeradorColuna;
use Gerador\Service\GeradorTabela;

class GeradorController extends AbstractEstruturaController{
    protected function playAction(){
        $service = new GeradorTabela();
        $tabelas = $service->filtrarObjeto();

        $colunas = new GeradorColuna();
        $mapa = [];
        foreach($tabelas as $tabela){
            $colunas->setTableName( $tabela->getTableName() );
            $colunas->setTableSchema($tabela->getTableSchema());

            $mapa[ $tabela->getTableName() ] =  $colunas->filtrarObjeto();
        }

        $gerador = new Gerador($mapa);
        $gerador->gerar();

        $this->addSuccessMessage('Efetuado com sucesso');

        return $this->redirect()->toRoute('gerador-home');
    }
} 