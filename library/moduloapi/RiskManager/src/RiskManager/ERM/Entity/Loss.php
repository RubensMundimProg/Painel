<?php

namespace RiskManager\ERM\Entity;

use Base\Service\AbstractApiService;

/**
 * 
 * Classe Entity que fornece orientações sobre como acessar as funcionalidades 
 * do módulo de ERM Loss
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage ERM\Entity
 */
class Loss extends AbstractApiService {

    protected $id;
    protected $name;
    protected $description;
    protected $deleted;
    protected $lossEventResponsible;
    protected $value;
    protected $dateCreated;
    protected $dateUpdated;
    protected $accountingDate;
    protected $type;

}
