<?php

namespace RiskManager\Workflow\Entity;

use Base\Service\AbstractApiService;

/**
 *
 * Classe Entity que armazena informações dos Attributos Customizados
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Entity\Entity
 */
class Attributes extends AbstractApiService {
    protected $name;
    protected $variableName;
    protected $description;
    protected $typeName;
    protected $minLength;
    protected $maxLength;
    protected $fieldMask;
    protected $minValue;
    protected $maxValue;
    protected $decimalPlaces;
    protected $applyAlphabeticalOrder;
    protected $allowedValues;
    protected $allowedItems;

    /**
     * @return mixed
     */
    public function getAllowedItems()
    {
        return $this->allowedItems;
    }

    /**
     * @param mixed $allowedItems
     */
    public function setAllowedItems($allowedItems)
    {
        $this->allowedItems = $allowedItems;
    }

    /**
     * @return mixed
     */
    public function getAllowedValues()
    {
        return $this->allowedValues;
    }

    /**
     * @param mixed $allowedValues
     */
    public function setAllowedValues($allowedValues)
    {
        $this->allowedValues = $allowedValues;
    }

    /**
     * @return mixed
     */
    public function getApplyAlphabeticalOrder()
    {
        return $this->applyAlphabeticalOrder;
    }

    /**
     * @param mixed $applyAlphabeticalOrder
     */
    public function setApplyAlphabeticalOrder($applyAlphabeticalOrder)
    {
        $this->applyAlphabeticalOrder = $applyAlphabeticalOrder;
    }

    /**
     * @return mixed
     */
    public function getDecimalPlaces()
    {
        return $this->decimalPlaces;
    }

    /**
     * @param mixed $decimalPlaces
     */
    public function setDecimalPlaces($decimalPlaces)
    {
        $this->decimalPlaces = $decimalPlaces;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getFieldMask()
    {
        return $this->fieldMask;
    }

    /**
     * @param mixed $fieldMask
     */
    public function setFieldMask($fieldMask)
    {
        $this->fieldMask = $fieldMask;
    }

    /**
     * @return mixed
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @param mixed $maxLength
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * @return mixed
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * @param mixed $maxValue
     */
    public function setMaxValue($maxValue)
    {
        $this->maxValue = $maxValue;
    }

    /**
     * @return mixed
     */
    public function getMinLength()
    {
        return $this->minLength;
    }

    /**
     * @param mixed $minLength
     */
    public function setMinLength($minLength)
    {
        $this->minLength = $minLength;
    }

    /**
     * @return mixed
     */
    public function getMinValue()
    {
        return $this->minValue;
    }

    /**
     * @param mixed $minValue
     */
    public function setMinValue($minValue)
    {
        $this->minValue = $minValue;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * @param mixed $typeName
     */
    public function setTypeName($typeName)
    {
        $this->typeName = $typeName;
    }

    /**
     * @return mixed
     */
    public function getVariableName()
    {
        return $this->variableName;
    }

    /**
     * @param mixed $variableName
     */
    public function setVariableName($variableName)
    {
        $this->variableName = $variableName;
    }


}
