<?php
namespace Estrutura\Helpers;
/**
 *
 * @author ronaldo
 *
 */
class Email{

	/**
	 *
	 * @param string $email
	 */
	public static function isValid( $email ){

        $regex = '/^[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\-]+\.[a-z]{2,4}$/i';

        // Run the preg_match function on regex 1
        if (preg_match($regex, $email)) {

            return true;
        } else {

            return false;
        }
    }
}