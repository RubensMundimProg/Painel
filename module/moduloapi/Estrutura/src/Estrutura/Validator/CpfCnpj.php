<?php

namespace Estrutura\Validator;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;

class CpfCnpj extends AbstractValidator
{
    const INVALID = 'invalid';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "O CPF/CNPJ informado é inválido",
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
     * @param  mixed $cpfCnpj
     * @return bool
     */
    public function isValid($cpfCnpj)
    {
        $valor = preg_replace('/\D/', '', (string) $cpfCnpj);

        switch (strlen($valor)) {
            case 11:
                $validator = new Cpf();
                break;
            case 14:
                $validator = new Cnpj();
                break;
            default:
                $this->error(self::INVALID);
                return false;
        }

        if (!$validator->isValid($cpfCnpj)) {
            $this->error(self::INVALID);
            return false;
        }

        return true;
    }
}
