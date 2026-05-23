<?php

namespace Estrutura\Validator;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;

class Cnpj extends AbstractValidator
{
    const INVALID = 'invalid';
    const WRONG_LENGTH = 'wrongLength';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "O CNPJ informado é inválido",
        self::WRONG_LENGTH => "O CNPJ informado deve conter 14 números",
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
     * @param  mixed $cnpj
     * @return bool
     */
    public function isValid($cnpj)
    {
        $cnpj = preg_replace('/\D/', '', (string) $cnpj);

        if (strlen($cnpj) != 14) {
            $this->error(self::WRONG_LENGTH);
            return false;
        }

        if ($cnpj === str_repeat($cnpj[0], 14)) {
            $this->error(self::INVALID);
            return false;
        }

        $number = array_map('intval', str_split($cnpj));

        $sum = $number[0] * 5 + $number[1] * 4 + $number[2] * 3 + $number[3] * 2 +
            $number[4] * 9 + $number[5] * 8 + $number[6] * 7 + $number[7] * 6 +
            $number[8] * 5 + $number[9] * 4 + $number[10] * 3 + $number[11] * 2;
        $sum -= (11 * intval($sum / 11));
        $result1 = ($sum == 0 || $sum == 1) ? 0 : 11 - $sum;

        if ($result1 != $number[12]) {
            $this->error(self::INVALID);
            return false;
        }

        $sum = $number[0] * 6 + $number[1] * 5 + $number[2] * 4 + $number[3] * 3 +
            $number[4] * 2 + $number[5] * 9 + $number[6] * 8 + $number[7] * 7 +
            $number[8] * 6 + $number[9] * 5 + $number[10] * 4 + $number[11] * 3 +
            $number[12] * 2;
        $sum -= (11 * intval($sum / 11));
        $result2 = ($sum == 0 || $sum == 1) ? 0 : 11 - $sum;

        if ($result2 != $number[13]) {
            $this->error(self::INVALID);
            return false;
        }

        return true;
    }
}
