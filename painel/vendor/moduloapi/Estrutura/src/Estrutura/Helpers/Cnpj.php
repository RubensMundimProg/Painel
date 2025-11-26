<?php
namespace Estrutura\Helpers;
/**
 *
 * @author ronaldo
 *
 */
class Cnpj {

	/**
	 *
	 * @param unknown_type $cnpj
	 */
	public static function isValid($cnpj){

		// Verifiva se o número digitado contém todos os digitos
		$cnpj = preg_replace ("@[./-]@", "", $cnpj);

		if (strlen ($cnpj) <> 14 or !is_numeric ($cnpj)){
			return false;
		}

		if($cnpj == '00000000000000'){
			return false;
		}

		$j = 5;
		$k = 6;
		$soma1 = "";
		$soma2 = "";

		for ($i = 0; $i < 13; $i++){
			$j = $j == 1 ? 9 : $j;
			$k = $k == 1 ? 9 : $k;
			$soma2 += ($cnpj{$i} * $k);

			if ($i < 12){
				$soma1 += ($cnpj{$i} * $j);
			}
			$k--;
			$j--;
		}

		$digito1 = $soma1 % 11 < 2 ? 0 : 11 - $soma1 % 11;
		$digito2 = $soma2 % 11 < 2 ? 0 : 11 - $soma2 % 11;
		return (($cnpj{12} == $digito1) and ($cnpj{13} == $digito2));
	}

	/**
	 *
	 * @param string $cnpj
	 * @return string $cnpj
	 * Ex:
	 * $cnpj = 18.756.321/0001-86
	 * $cnpj = Modulo_Helpers_Cnpj::cnpjFilter($cnpj); // retorna 18756321000186
	 */
	public static function cnpjFilter( $cnpj ){

		return preg_replace('#[./ -]#', '', $cnpj);
	}


	/**
	 *
	 * @param int $cnpj
	 * @return string $cnpj
	 * Ex:
	 * $cnpj = 18756321000186
	 * $cnpj = Modulo_Helpers_Cnpj::cnpjMask($cnpj); // retorna 18.756.321/0001-86
	 */
	public static function cnpjMask( $cnpj ){

		if ($cnpj){

			return Modulo_Helpers_Utilities::mascaraformato('##.###.###/####-##', trim($cnpj));
		}else{

			return null;
		}
	}
}