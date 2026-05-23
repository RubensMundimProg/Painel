<?php

namespace Estrutura\Validator;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;

class Cid extends AbstractValidator
{
    const INVALID = 'invalid';
    const WRONG_LENGTH = 'wrongLength';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "O valor de entrada não corresponde ao valor de um CID válido",
        self::WRONG_LENGTH => "O valor de entrada deve conter 36 digitos",
    );

    /**
     * @param  array|\Traversable $options
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    /**
     * @param  mixed $cid
     * @return bool
     */
    public function isValid($cid)
    {
        $cid = (string) $cid;

        if (strlen($cid) != 36) {
            $this->error(self::WRONG_LENGTH);
            return false;
        } elseif (!preg_match('/^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$/', $cid)) {
            return false;
        }

        return true;
    }
}
