<?php

namespace Estrutura\Form\Element;

use Zend\Form\Element;

class Float extends Element
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
        $value = str_replace( ['.',','] , ['','.'] , $value );
        if($value) $this->value = number_format($value,'2',',','.');
        return $this;
    }
}
