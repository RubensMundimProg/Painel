<?php

namespace Estrutura\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterAwareInterface;

class AbstractForm extends Form implements InputFilterAwareInterface
{
    protected static $sm;

    protected static $config;

    protected $default = [];

    /**
     * @var \Zend\InputFilter\InputFilter
     */
    protected $inputFilter;

    /**
     * @var \Mvc\Form\FormObject
     */
    protected $formObject;

    public function adicionaInputFilterPadrao(\Zend\InputFilter\InputFilter $inputFilter)
    {
        foreach($this->getElements() as $element)
        {
            if($element instanceof \Zend\Form\Element\Date )
            {
                $inputFilter->add(array(
                    'name'     => $element->getName(),
                    'validators' => array(
                        array(
                            'name'    => 'Date',
                            'options' => [
                                'format' => 'd/m/Y'
                            ]
                        ),
                    ),
                ));
            }
        }
        return $inputFilter;
    }

    /**
     * @return FormObject
     */
    public function formObject()
    {
        return $this->formObject;
    }

    public static function setServiceManager($sm)
    {
        self::$sm = $sm;
    }

    public function sm()
    {
        return self::$sm;
    }

    /**
     * Busca uma parâmetro das configurações do framework
     *
     * Para busca em arrays encadeados, deve ser passado um parâmetro para cada filho, podendo passar quantos parâmetros
     * quanto necessário
     *
     * @param $param1
     * @param null $param2
     */
    public function getConfig($param1,$param2=null)
    {
        if(!$config = self::$config) $config = self::$config = $this->sm()->get("Config");
        $parametros = func_get_args();

        foreach($parametros as $parametro)
        {
            if(!array_key_exists($parametro, $config)) return null;
            $config = $config[$parametro];
        }

        return $config;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }

    public function getElementsKeys(){
        return array_keys($this->getElements());
    }

    public function setData($dados){
        if(count($this->default)){
            foreach($this->default as $chave => $valor){
                if(isset($dados[$chave])) continue;
                $dados[$chave] = $valor;
            }
        }
        parent::setData($dados);
    }
}
