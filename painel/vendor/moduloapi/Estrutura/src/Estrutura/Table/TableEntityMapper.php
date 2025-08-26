<?php

namespace Estrutura\Table;

use ReflectionMethod;
use Traversable;
use Zend\Debug\Debug;
use Zend\Stdlib\Exception;
use Zend\Stdlib\Hydrator\AbstractHydrator;
use Zend\Stdlib\Hydrator\HydratorOptionsInterface;

class TableEntityMapper extends AbstractHydrator implements HydratorOptionsInterface
{
    protected $_dataMap = true;

    public function __construct($map)
    {
        parent::__construct();
        $this->_dataMap = $map;
    }

    public function extract($object) {
        if (!is_object($object)) {
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided $object to be a PHP object)',
                __METHOD__
            ));
        }
        $arr = array();

        foreach( $this->_dataMap as $dbField => $entityField )
        {
            $method = 'get'.ucfirst($entityField);
            $attribute = method_exists($object,$method) ? $object->{$method}() : null ;
            if($attribute !== null) $arr[$dbField] = $attribute ;
        }

        $custom = (method_exists($object, 'getCustomAttributes')) ? $object->getCustomAttributes() : false;
        if($custom != false){
            $arr['CustomAttributes'] = $custom;
        }

        return $arr;
    }

    public function hydrate(array $data, $object)
    {
        if (!is_object($object)) {
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided $object to be a PHP object)',
                __METHOD__
            ));
        }

        $object->exchangeArray($data);
        foreach ($data as $property => $value) {
            if (!property_exists($this, $property)) {
                if (in_array($property, array_keys($this->_dataMap))) {
                    $_prop = $this->_dataMap[$property];
                    $object->{'set'.$_prop}($value);
//                    $object->set($_prop,$value);
//                    $object->$_prop = $value;
                } else {
                    //pula atributos não conhecidos
                }
            } else {
                $object->$property = $value;
            }
        }


        return $object;
    }

    /**
     * @param  array|\Traversable $options
     * @return HydratorOptionsInterface
     */
    public function setOptions($options)
    {
        //Implement setOptions() method.
    }


}