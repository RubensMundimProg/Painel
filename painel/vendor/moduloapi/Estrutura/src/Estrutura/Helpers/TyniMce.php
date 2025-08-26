<?php
namespace Estrutura\Helpers;
/**
 * Classe de auxílio ao tratamento de strings do TyniMce
 * @author ronaldo rodrigues
 */
class TyniMce {

	private static $allowedTags = '<p><strong><em><u><h1><h2><h3><h4><h5><h6><img><li><ol><ul><span><div><br><ins><del><a>';

	/**
	 *
	 * @param string $text
	 * @param string $allowedTags => defult = '<p><strong><em><u><h1><h2><h3><h4><h5><h6><img><li><ol><ul><span><div><br><ins><del><a>'
	 * @return $text => $text filtrado.
	 */
	public static function tyniMceFilter( $text, $allowedTags = null){

		if(is_null($allowedTags)){
			$allowedTags = self::$allowedTags;
		}

		return strip_tags(stripslashes($text), $allowedTags);
	}
}