<?php
namespace Estrutura\Helpers;
/**
 *
 * @author ronaldo
 *
 */
class StringMap {

    /**
     *
     * @param string $str
     * @return string $str
     */
    public static function underscore2Camelcase($str) {

        // Split string in words.
        $words = explode('_', ($str));

        $return = '';
        foreach ($words as $word) {

            $return .= ucwords(substr(trim($word), 0, 1)) . substr(trim($word), 1);
        }

        return $return;
    }
    
    public static function camelcase2underscore($name){
        
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));    
    }    

    public static function spacify($camel, $glue = ' ') {

        return $camel[0] . substr(
            implode(
                $glue, 
                array_map(
                    'implode', 
                    array_chunk(
                        preg_split(
                            '/([A-Z])/', 
                            ucfirst($camel), 
                            -1, 
                            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
                        ), 
                        2
                    )
                )
            ), 
            1
        );
    }

    /**
     *
     * @param string $str
     * @return string $str
     */
    public static function retiraEspacos($str) {

        // Split string in words.
        $words = explode(' ', strtolower($str));

        $return = '';
        foreach ($words as $word) {

            $return .= ucfirst(trim($word));
        }

        return $return;
    }

    /**
     *
     * @param string $string
     * @return string
     */
    public static function retiraAcento($string) {

        $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßŔàáâãäåæçèéêëìíîïðñòóôõöøùúûýþÿŕ';
        $b = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYBBRaaaaaaaceeeeiiiidnoooooouuuybyr';
        $string = utf8_decode($string);
        $string = strtr($string, utf8_decode($a), $b);
        return utf8_encode($string);
    }

    /**
     *
     * @param unknown_type $data
     */
    public static function base64UrlEncode($data) {
        return strtr(rtrim(base64_encode($data), '='), '+/', '-_');
    }

    /**
     *
     * @param unknown_type $base64
     */
    public static function base64UrlDecode($base64) {

        return base64_decode(strtr($base64, '-_', '+/'));
    }

    /**
     *
     * @param unknown_type $texto
     * @param unknown_type $limite
     * @param unknown_type $quebra
     */
    public static function limitaCaracteres($texto, $limite, $quebra = true) {

        $tamanho = strlen($texto);
        if ($tamanho <= $limite) { //Verifica se o tamanho do texto é menor ou igual ao limite
            $novo_texto = $texto;
        } else { // Se o tamanho do texto for maior que o limite
            if ($quebra == true) { // Verifica a opção de quebrar o texto
                $novo_texto = trim(substr($texto, 0, $limite)) . "...";
            } else { // Se não, corta $texto na última palavra antes do limite
                $ultimo_espaco = strrpos(substr($texto, 0, $limite), " "); // Localiza o útlimo espaço antes de $limite
                $novo_texto = trim(substr($texto, 0, $ultimo_espaco)) . "..."; // Corta o $texto até a posição localizada
            }
        }
        return $novo_texto; // Retorna o valor formatado
    }

    /**
     *
     * @param string $texto
     * @param int $tamanho
     * @param string $preenchimento
     * @return string
     */
    private function preencheEsquerda($texto, $tamanho, $preenchimento = ' ') {

        return Utilities::preencheEsquerda($texto, $tamanho, $preenchimento);
    }

    /**
     *
     * @param string $texto
     * @param int $tamanho
     * @param string $preenchimento
     * @return string
     */
    private function preencheDireita($texto, $tamanho, $preenchimento = ' ') {

        return Utilities::preencheDireita($texto, $tamanho, $preenchimento);
    }

    /**
     * Mascar formato de acordo com a mascara informada
     * @param string $mask
     * @param string $string
     * @return string
     */
    public static function mascaraformato($mask, $string) {

        return Utilities::mascaraformato($mask, $string);
    }

}
