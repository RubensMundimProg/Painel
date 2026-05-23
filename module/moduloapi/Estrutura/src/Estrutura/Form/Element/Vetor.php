<?php

namespace Estrutura\Form\Element;

use Laminas\Form\Element;

class Vetor extends \Laminas\Form\Element

{
   public function extract()
   {

       if ($this->object instanceof Traversable) {
           $this->object = ArrayUtils::iteratorToArray($this->object);
       }

       if (!is_array($this->object)) {
           return array();
       }

       $values = array();

       foreach ($this->object as $key => $value) {
           if ($this->hydrator) {
               $values[$key] = $this->hydrator->extract($value);
           } else{
               $targetElement = clone $this->targetElement;
               $targetElement->object = $value;
               $values[$key] = $targetElement->extract();
               if (! $this->createNewObjects() && $this->has($key)) {
                   $fieldset = $this->get($key);
                   if ($fieldset instanceof Fieldset && $fieldset->allowObjectBinding($value)) {
                       $fieldset->setObject($value);
                   }
               }
           }
       }

       return $values;
   }


}
