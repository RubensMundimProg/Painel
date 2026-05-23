<?php

namespace Estrutura\Validator;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;

class Cpf extends AbstractValidator
{
    const INVALID = 'invalid';
    const WRONG_LENGTH = 'wrongLength';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "O CPF informado é inválido",
        self::WRONG_LENGTH => "O CPF informado deve conter 11 números",
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
     * @param  mixed $cpf
     * @return bool
     */
    public function isValid($cpf)
    {
        $cpf = preg_replace('/\D/', '', (string) $cpf);

        if (strlen($cpf) != 11) {
            $this->error(self::WRONG_LENGTH);
            return false;
        }

        if ($cpf === str_repeat($cpf[0], 11)) {
            $this->error(self::INVALID);
            return false;
        }

        $number = array_map('intval', str_split($cpf));

        $sum = 10 * $number[0] + 9 * $number[1] + 8 * $number[2] + 7 * $number[3] +
            6 * $number[4] + 5 * $number[5] + 4 * $number[6] + 3 * $number[7] +
            2 * $number[8];
        $sum -= (11 * intval($sum / 11));
        $result1 = ($sum == 0 || $sum == 1) ? 0 : 11 - $sum;

        if ($result1 != $number[9]) {
            $this->error(self::INVALID);
            return false;
        }

        $sum = $number[0] * 11 + $number[1] * 10 + $number[2] * 9 + $number[3] * 8 +
            $number[4] * 7 + $number[5] * 6 + $number[6] * 5 + $number[7] * 4 +
            $number[8] * 3 + $number[9] * 2;
        $sum -= (11 * intval($sum / 11));
        $result2 = ($sum == 0 || $sum == 1) ? 0 : 11 - $sum;

        if ($result2 != $number[10]) {
            $this->error(self::INVALID);
            return false;
        }

        return true;
    }
}
