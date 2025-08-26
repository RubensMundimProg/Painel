<?php
namespace Estrutura\Helpers;
/**
 *
 * @author ronaldo
 *
 */
class Integer{

	public static function integerFilter( $valor = 0) {

		return filter_var($valor, 519);
	}
}