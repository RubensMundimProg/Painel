<?php

namespace RiskManager\MySpace\Service;

use RiskManager\MySpace\Entity\ListQueries as Entity;
use Base\Service\Request;

/**
 * 
 * Classe Service que retorna uma lista com todas as consultas a que o usuário tem acesso. 
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage MySpace\Entity
 */
class ListQueries extends Entity {

    protected $url = '/api/info/me/profiles';
    protected $moduloRequest = 'RM';

    /**
     * 
     * Retorna uma lista com todas as consultas a que o usuário tem acesso.
     * @return array
     */
    public function fetchAll()
    {
        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url);
        $request->setService($this);
        $dados = $request->send();
        $lista = [];
        if (!empty($dados)) {
            foreach ($dados as $item) {
                $listQueries = new ListQueries();
                $listQueries->exchangeArray($item);
                $lista[] = $listQueries;
            }
        }
        return $lista;
    }

}
