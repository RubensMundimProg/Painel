<?php
namespace Estrutura\Helpers;
/**
 *
 * @author ronaldo
 *
 */
class Processo {

	/**
	 *
	 * @param string $processo
	 * @return string $processo
	 * Ex:
	 * $processo = 12345.123456/1234-12
	 * $processo = Modulo_Helpers_Processo::processoFilter($processo); // retorna 12345123456123412
	 */
	public static function processoFilter( $processo ){

		return preg_replace('#[./ -]#', '', $processo);
	}


	/**
	 *
	 * @param int $processo
	 * @return string $processo
	 * Ex:
	 * $processo = 12345123456123412
	 * $processo = Modulo_Helpers_Processo::processoMask($processo); // retorna 12345.123456/1234-12
	 */
	public static function processoMask( $processo ){

		if ($processo){

			return Modulo_Helpers_Utilities::mascaraformato('#####.######/####-##', trim($processo));
		}else{

			return null;
		}
	}
}