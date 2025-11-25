<?php

namespace RiskManager\Workflow\Entity;

use Base\Service\AbstractApiService;

/**
 *
 * Classe Entity que lista os dados das Queries
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Entity\Entity
 */
class Queries extends AbstractApiService {
    protected $result;

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }
}
