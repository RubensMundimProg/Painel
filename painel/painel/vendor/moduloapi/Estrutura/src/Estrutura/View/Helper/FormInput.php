<?php
namespace Estrutura\View\Helper;
use Zend\Session\Container;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\BasePath;

class FormInput extends AbstractHelper
{
    public function __invoke($element, $possuilabel = true)
    {
    	
		

		
		
		$messages = $element->getMessages();
		if(count($messages) > 0){
			 $class = $element->getAttribute('class');
			 $element->setAttribute('class', $class." error-input");
		}

		$input =  $this->getView()->formElement($element);
		
		$label = $this->getView()->formLabel($element);
		
		$retorno = "";
		if($possuilabel){
			$retorno .= "<div class='form-group'>";
			$retorno .= $label;
		}
		
		$retorno .= $input;
		
		if(count($messages) > 0){
			$message = implode(', ', $messages);
			$retorno .= '<a href=""#" title="'.$message.'">';
			$retorno .= "<img src='{$this->view->basePath('/assets/img/ico/ico_validation_error.png')}'>";
			$retorno .= '</a>';
		}
		
		if($possuilabel){
			$retorno .= "</div>";
		}
		
        return $retorno;
    }
}