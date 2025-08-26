<?php
/**
 * Created by PhpStorm.
 * User: bruno.silva
 * Date: 11/02/2015
 * Time: 16:36
 */

namespace Application\View\Helper;

use \Zend\Form\View\Helper\FormRow;
use Zend\Form\ElementInterface;

class FormRowNoLabel extends FormRow{
    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @param null|ElementInterface $element
     * @param null|string $labelPosition
     * @param bool $renderErrors
     * @param string|null $partial
     * @return string|FormRow
     */
        public function __invoke(ElementInterface $element = null, $labelPosition = null, $renderErrors = null, $partial = null)
    {
        if (!$element) {
            return $this;
        }

        /// Remove Label of Element
        $element->setLabel('');

        if ($labelPosition !== null) {
            $this->setLabelPosition($labelPosition);
        } elseif ($this->labelPosition === null) {
            $this->setLabelPosition(self::LABEL_PREPEND);
        }
        if ($renderErrors !== null) {
            $this->setRenderErrors($renderErrors);
        }
        if ($partial !== null) {
            $this->setPartial($partial);
        }

        return $this->render($element);
    }
} 