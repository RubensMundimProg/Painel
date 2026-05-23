<?php

namespace Estrutura\Validator;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;

class Ano extends AbstractValidator
{
    const INVALID = 'invalid';
    const WRONG_LENGTH = 'wrongLength';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "O valor de entrada não corresponde a um Ano válido",
        self::WRONG_LENGTH => "O valor de entrada deve conter 4 números",
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
     * @param  mixed $ano
     * @return bool
     */
    public function isValid($ano)
    {
        $ano = (string) $ano;

        if (strlen($ano) != 4) {
            $this->error(self::WRONG_LENGTH);
            return false;
        } elseif (!preg_match('/^\d{4}$/', $ano)) {
            return false;
        }

        return true;
    }
}
