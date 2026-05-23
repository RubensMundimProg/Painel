<?php

namespace Estrutura\Validator;

use Laminas\Stdlib\ArrayUtils;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;
use Traversable;

class ArraySize extends AbstractValidator
{
    const NOT_GREATER = 'notGreaterThan';
    const NOT_LESS = 'notLessThanInclusive';

    protected $min;
    protected $max;

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_LESS => "O %s deve conter no mínimo '%min%' valor(es)",
        self::NOT_GREATER => "O %s deve conter no máximo %max% valor(es)",
    );

    /**
     * @var array
     */
    protected $messageVariables = array(
        'min' => 'min',
        'max' => 'max',
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
            $temp['max'] = array_shift($options);
            $options = $temp;
        }

        if (!array_key_exists('min', $options)) {
            $options['min'] = 0;
        }

        if (!array_key_exists('max', $options)) {
            $options['max'] = null;
        }

        $this->setMin($options['min']);
        $this->setMax($options['max']);

        parent::__construct($options);
    }

    public function getMin()
    {
        return $this->min;
    }

    public function getMax()
    {
        return $this->max;
    }

    public function setMin($min)
    {
        $this->min = $min instanceof \Laminas\Form\Element ? $min->getValue() : $min;
        return $this;
    }

    public function setMax($max)
    {
        $this->max = $max instanceof \Laminas\Form\Element ? $max->getValue() : $max;
        return $this;
    }

    /**
     * @param  mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $size = (!is_array($value)) ? 0 : count($value);

        if ($this->getMin() && $size < $this->getMin()) {
            $this->error(self::NOT_LESS);
            return false;
        }

        if ($this->getMax() && $size > $this->getMax()) {
            $this->error(self::NOT_GREATER);
            return false;
        }

        return true;
    }
}
