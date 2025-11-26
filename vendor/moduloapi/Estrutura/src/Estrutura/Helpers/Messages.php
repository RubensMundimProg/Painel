<?php
namespace Estrutura\Helpers;
/**
 *
 * @author ronaldo
 *
 */
class Messages {

	public $messages = array();

	public function __construct( $messagesIni = 'maps_messages.ini' ){

		Zend_Registry::getInstance()->set(	'messages',
				new Zend_Config_Ini(APPLICATION_PATH . '/configs/' . $messagesIni,
									APPLICATION_ENV));
	}

	/**
	 *
	 * Retorna as messagens
	 *
	 * @return array
	 */
	public function getMessages(){

		return json_encode($this->array_flat($this->messages));
	}

	public function array_flat($array) {

		$arrayAux1 = array();
		$arrayAux2 = array();

		foreach ( $array as $key => $value ) {

			if(is_array($value)){

				$arrayAux1[$key] = current($value);
			}
		}

		$arrayAux1 = array_unique($arrayAux1);

		foreach ($arrayAux1 as $key => $value) {

			$arrayAux2[$key] = $array[$key];

		}

		return $arrayAux2;
	}

	public function addMessage( $tipo, $message, $key = null) {

		if(is_null($key)){

			$this->messages[] = array($tipo => $message);
		}else{

			$this->messages[$key] = array($tipo => $message);
		}
	}

	public function addMessageForKey( $key, array $tokens = array() ) {

		$message = Zend_Registry::get('messages')->message->$key;

		$message = $message->toArray();

		$message['message'] = $this->substituiTokens($message['message'], $tokens);

		$this->messages[$key] = array($message['tipo'] => $message['message']);
	}

	public function substituiTokens( $message, array $tokens = array()){

		if(!empty($tokens)){
			foreach ($tokens as $token => $value) {

				$message = preg_replace('/<' . $token . '>/', '"' . $value . '"', $message);
			}
		}
		return $message;
	}


	/**
	 *
	 * @param array $result
	 */
	public function tratarRespostaService(array $result){

		if(	strcasecmp($result['status'], 'failure') ==  0){

			$this->messages[] = array('erro' => 'WS: ' . $result['response']);
			throw new Exception('WS: ' . $result['response']);
			return false;
		}elseif(strcasecmp($result['status'], 'success') ==  0){

			return $result['response'];
		}
	}
}