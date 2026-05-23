<?php

namespace Estrutura\Validator;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;

class Cep extends AbstractValidator
{
    const INVALID = 'invalid';
    const WRONG_LENGTH = 'wrongLength';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "O CEP informado é inválido",
        self::WRONG_LENGTH => "O CEP informado deve conter 8 números",
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
     * @param  mixed $cep
     * @return bool
     */
    public function isValid($cep)
    {
        $cep = str_replace(['_', '.'], '', (string) $cep);

        if (strlen($cep) != 8) {
            $this->error(self::WRONG_LENGTH);
            return false;
        }

        if (!preg_match('/[0-9]{8}/', $cep)) {
            $this->error(self::INVALID);
            return false;
        }

        return true;
    }
}
