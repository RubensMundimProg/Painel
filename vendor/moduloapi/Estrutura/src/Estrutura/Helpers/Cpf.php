<?php
namespace Estrutura\Helpers;
/**
 *
 * @author ronaldo
 *
 */
class Cpf {

	/**
	 *
	 * @param string $cpf
	 * @return string $cpf
	 * Ex:
	 * $cpf = 994.944.691-00
	 * $cpf = Modulo_Helpers_Cpf::cpfFilter($cpf); // retorna 99494469100
	 */
	public static function cpfFilter( $cpf ){

		return preg_replace('#[. -]#', '', $cpf);
	}


	/**
	 *
	 * @param int $cpf
	 * @return string $cpf
	 * Ex:
	 * $cpf = 99494469100
	 * $cpf = Modulo_Helpers_Cpf::cpfMask($cpf); // retorna 994.944.691-00
	 */
	public static function cpfMask( $cpf ){

		if ($cpf){

			return Modulo_Helpers_Utilities::mascaraformato('###.###.###-##', trim($cpf));
		}else{

			return null;
		}
	}

	public static function isValid($cpf){

		// Verifiva se o número digitado contém todos os digitos
		$cpf = str_replace(array('.','-'),'',$cpf);
		$cpf = str_pad(preg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);

		// Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
		if (strlen($cpf) != 11
				|| $cpf == '00000000000'
				|| $cpf == '11111111111'
				|| $cpf == '22222222222'
				|| $cpf == '33333333333'
				|| $cpf == '44444444444'
				|| $cpf == '55555555555'
				|| $cpf == '66666666666'
				|| $cpf == '77777777777'
				|| $cpf == '88888888888'
				|| $cpf == '99999999999'){
			return false;
		}else{
			// Calcula os números para verificar se o CPF é verdadeiro
			for ($t = 9; $t < 11; $t++) {
				for ($d = 0, $c = 0; $c < $t; $c++) {
					$d += $cpf{$c} * (($t + 1) - $c);
				}

				$d = ((10 * $d) % 11) % 10;

				if ($cpf{$c} != $d) {
					return false;
				}
			}
			return true;
		}
	}
}