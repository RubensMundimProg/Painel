<?php

namespace Estrutura\Validator;

use Laminas\Stdlib\ArrayUtils;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;
use Traversable;

class DateGreaterThan extends AbstractValidator
{
    const NOT_GREATER = 'notGreaterThan';
    const NOT_GREATER_INCLUSIVE = 'notGreaterThanInclusive';

    protected $min;
    protected $inclusive;
    protected $format;
    protected $reverse;

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_GREATER => "A data não é maior que '%min%'",
        self::NOT_GREATER_INCLUSIVE => "A data não não é maior ou igual a '%min%'",
    );

    /**
     * @var array
     */
    protected $messageVariables = array(
        'min' => 'min',
    );

    /**
     * @param  array|Traversable|null $options
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!is_array($options)) {
            $options = func_get_args();
            $temp['min'] = array_shift($options);

            if (!empty($options)) {
                $temp['inclusive'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['format'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['reverse'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!array_key_exists('min', $options)) {
            throw new Exception\InvalidArgumentException("Missing option 'min'");
        }

        if (!array_key_exists('inclusive', $options)) {
            $options['inclusive'] = false;
        }

        if (!array_key_exists('format', $options)) {
            $options['format'] = 'd/m/Y';
        }

        if (!array_key_exists('reverse', $options)) {
            $options['reverse'] = 'false';
        }

        $this->setMin($options['min'])
            ->setInclusive($options['inclusive'])
            ->setFormat($options['format'])
            ->setReverse($options['reverse']);

        parent::__construct($options);
    }

    public function getMin()
    {
        return $this->min;
    }

    public function setMin($min)
    {
        $this->min = $min instanceof \Laminas\Form\Element ? $min->getValue() : $min;
        return $this;
    }

    public function getInclusive()
    {
        return $this->inclusive;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function getReverse()
    {
        return $this->reverse;
    }

    public function setInclusive($inclusive)
    {
        $this->inclusive = $inclusive;
        return $this;
    }

    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    public function setReverse($reverse)
    {
        $this->reverse = $reverse;
        return $this;
    }

    /**
     * @param  mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $dataMaior = \DateTime::createFromFormat($this->getFormat(), (string) $value);
        $dataMenor = \DateTime::createFromFormat('d/m/Y', (string) $this->getMin());
        $this->setValue($dataMaior ?: $value);

        if (!$dataMaior || !$dataMenor) {
            $this->error($this->inclusive ? self::NOT_GREATER_INCLUSIVE : self::NOT_GREATER);
            return false;
        }

        if ($this->inclusive) {
            if ((!$this->getReverse() && $dataMenor > $dataMaior) || ($this->getReverse() && $dataMenor < $dataMaior)) {
                $this->error(self::NOT_GREATER_INCLUSIVE);
                return false;
            }
        } else {
            if ((!$this->getReverse() && $dataMenor >= $dataMaior) || ($this->getReverse() && $dataMenor <= $dataMaior)) {
                $this->error(self::NOT_GREATER);
                return false;
            }
        }

        return true;
    }
}
