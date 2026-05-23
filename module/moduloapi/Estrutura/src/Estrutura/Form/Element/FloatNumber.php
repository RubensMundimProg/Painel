<?php

namespace Estrutura\Form\Element;

use Laminas\Form\Element;

class FloatNumber extends Element
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'text',
    );

    public function setValue($value)
    {
        if ($value === null || $value === '') {
            return parent::setValue($value);
        }

        $value = str_replace( ['.',','] , ['','.'] , $value );
        if($value) $this->value = number_format($value,'2',',','.');
        return $this;
    }
}
