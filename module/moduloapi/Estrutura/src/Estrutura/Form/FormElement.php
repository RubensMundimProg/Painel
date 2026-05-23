<?php

namespace Estrutura\Form;

use Laminas\InputFilter\InputFilter;

class FormElement
{
    const ATTR_TITLE = 'title';
    const ATTR_MASK = 'data-mask';
    const ATTR_MAXLENGTH = 'maxlength';
    const ATTR_MINLENGTH = 'minlength';

    /**
     * @var \Estrutura\Form\AbstractForm
     */
    protected $element;

    /**
     * @var int
     */
    protected $minimo;

    /**
     * @var int
     */
    protected $maximo;

    /**
     * @var InputFilter
     */
    protected $inputFilter;

    public function __construct($element, InputFilter $inputFilter)
    {
        $this->element = $element;
        $this->inputFilter = $inputFilter;
    }

    public function element($element = null)
    {
        if ($element instanceof AbstractForm) {
            $this->element = $element;
        }

        return $this->element;
    }

    public function inputFilter($inputFilter = null)
    {
        if ($inputFilter instanceof InputFilter) {
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function setOptions($arrOptions)
    {
        $this->element()->setOptions($arrOptions);
        return $this;
    }

    public function getOption($key)
    {
        return $this->element->getOption($key);
    }

    public function setAttribute($key, $value)
    {
        $this->element->setAttribute($key, $value);
        return $this;
    }

    public function getAttribute($key)
    {
        return $this->element->getAttribute($key);
    }

    public function label($label = null)
    {
        if ($label === null) {
            return $this->element()->getLabel();
        }

        $this->element()->setLabel($label);
        return $this;
    }

    public function required($bool = null)
    {
        $input = $this->inputFilter()->get($this->element()->getName());

        if ($bool === null) {
            return $input->isRequired();
        }

        $input->setRequired($bool);
        $this->setAttribute('class', $this->getAttribute('class') . ' obrigatorio');
        if ($bool == true) {
            $this->setAttribute('required', 'required');
        }

        return $this;
    }

    public function maxLength($maxLength = null)
    {
        if ($maxLength === null) {
            return $this->getAttribute(self::ATTR_MAXLENGTH);
        }

        $this->setAttribute(self::ATTR_MAXLENGTH, $maxLength);
        $this->addValidator('\Laminas\Validator\StringLength', ['encoding' => 'UTF-8', 'max' => $maxLength]);

        return $this;
    }

    public function minLength($minLength = null)
    {
        if ($minLength === null) {
            return $this->getAttribute(self::ATTR_MINLENGTH);
        }

        $this->addValidator('\Laminas\Validator\StringLength', ['encoding' => 'UTF-8', 'min' => $minLength]);
        return $this;
    }

    public function readOnly($bool = null)
    {
        if ($bool === null) {
            return $this->getAttribute('readonly');
        }

        $this->setAttribute('readonly', $bool);
        return $this;
    }

    public function addValidator($name, $options = [])
    {
        $this->inputFilter()->get($this->element()->getName())->getValidatorChain()->attachByName($name, $options);
        return $this;
    }

    public function addFilter($name, $options = [])
    {
        $this->inputFilter()->get($this->element()->getName())->getFilterChain()->attachByName($name, $options);
        return $this;
    }

    public function addTextValidatorsAndFilters()
    {
        $this->addFilter('StripTags');
        $this->addFilter('StringTrim');
        return $this;
    }

    public function value($value = null)
    {
        if ($value === null) {
            return $this->element()->getValue();
        }

        $this->element()->setValue($value);
        return $this;
    }

    public function title($title = null)
    {
        if ($title === null) {
            return $this->element()->getAttribute(self::ATTR_TITLE);
        }

        $this->element()->setAttribute(self::ATTR_TITLE, $title);
        return $this;
    }

    public function mask($mask = null)
    {
        if ($mask === null) {
            return $this->element()->getAttribute(self::ATTR_MASK);
        }

        $this->element()->setAttribute(self::ATTR_MASK, $mask);
        return $this;
    }

    public function max($num = null)
    {
        if ($num === null) {
            return $this->maximo;
        }

        $this->maximo = $num;
        $this->addValidator('\Estrutura\Validator\ArraySize', ['max' => $num]);

        return $this;
    }

    public function min($num = null)
    {
        if ($num === null) {
            return $this->minimo;
        }

        $this->minimo = $num;
        $this->addValidator('\Estrutura\Validator\ArraySize', ['min' => $num]);

        return $this;
    }
}
