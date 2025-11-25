<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Estrutura\Validator;

class StringLength extends \Zend\Validator\StringLength
{
    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID   => "Tipo de dado inválido passado. Texto esperado",
        self::TOO_SHORT => "O campo %s contém menos caracteres do que os %min% caracteres esperado",
        self::TOO_LONG  => "O campo %s contém mais caracteres do que os %max% esperados",
    );
}
