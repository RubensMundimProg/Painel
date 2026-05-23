<?php
namespace Estrutura\Helpers;
/**
 *
 * Contém métodos utilitários
 *
 * @created 28/05/2012
 *
 */
class Utilities {

    /**
     * Ajusta a apresentação dos números de documentos
     *
     * @param int|string $nu_doc
     * @return string
     */
    public static function ajustarNuDoc($nu_doc) {

        if (strlen($nu_doc)) {

            switch (strlen($nu_doc)) {

                case 7:
                    //RG
                    //0.000.000
                    $slice[0] = substr($nu_doc, 0, 1);
                    $slice[1] = substr($nu_doc, 1, 3);
                    $slice[2] = substr($nu_doc, 4, 3);
                    $rg = $slice[0] . '.' . $slice[1] . '.' . $slice[2];
                    return $rg;
                    break;

                case 11:
                    //CPF
                    //000.000.000-00
                    $slice = str_split($nu_doc, 3);
                    $cpf = $slice[0] . '.' . $slice[1] . '.' . $slice[2] . '-' . $slice[3];
                    return $cpf;
                    break;

                case 14:
                    //CNPJ
                    //00.000.000/0000-00
                    $slice[0] = substr($nu_doc, 0, 2);
                    $slice[1] = substr($nu_doc, 2, 3);
                    $slice[2] = substr($nu_doc, 5, 3);
                    $slice[3] = substr($nu_doc, 8, 4);
                    $slice[4] = substr($nu_doc, 12, 2);
                    $cnpj = $slice[0] . '.' . $slice[1] . '.' . $slice[2] . '/' . $slice[3] . '-' . $slice[4];
                    return $cnpj;
                    break;
            }
        }
    }

    /**
     * Mascar formato de acordo com a mascara informada
     * @param string $maskara
     * @param string $string
     * @return string
     */
    public static function mascaraformato($mask, $string) {

        //Retira caracteres especiais da string
        $outputString = str_replace('/', '', (str_replace('-', '', (str_replace(' ', '', (str_replace('.', '', $string)))))));
        //Retira caracteres especiais da string
        $outputMask = str_replace('/', '', (str_replace('-', '', (str_replace(' ', '', (str_replace('.', '', $mask)))))));

        //conta a quantidade de caracteres em cada uma das strings
        $sizeString = (strlen($outputString));
        $sizeMask = (strlen($outputMask));

        //verifica se as strings são compativeis para mascaramento
        if ($sizeString < $sizeMask) {

            $outputString = str_pad($outputString, ($sizeMask), '0', STR_PAD_LEFT);
        } elseif ($sizeString > $sizeMask) {

            $mask = str_pad($mask, ($sizeString + strlen(str_replace('#', '', $mask))), '#', STR_PAD_LEFT);
        }

        $index = -1;

        $tam = strlen($mask);

        //aplica a mascara
        for ($i = 0; $i < $tam; $i++) {

            if ($mask[$i] == '#') {

                $mask[$i] = $outputString[++$index];
            }
        }

        return $mask;
    }

    /**
     * Executa a funcão map em todos os valores do array
     * 
     * @param type $function
     * @param array $arrParam
     * @return array $arrParam
     */
    public static function arrayMapArray($function, array $arrParam) {

        $ret = array();
        foreach ($arrParam as $key => $value) {
            if (is_array($value)) {
                $ret[$key] = self::arrayMapArray($function, $value);
            } else {
                $ret[$key] = $function($value);
            }
        }
        return $ret;
    }

    /**
     *
     * @param string $texto
     * @param int $tamanho
     * @param string $preenchimento
     * @return string
     */
    public static function preencheEsquerda($texto, $tamanho, $preenchimento = ' ') {

        return substr(str_pad(trim(utf8_decode($texto)), $tamanho, $preenchimento, STR_PAD_LEFT), 0, ($tamanho));
    }

    /**
     *
     * @param string $texto
     * @param int $tamanho
     * @param string $preenchimento
     * @return string
     */
    public static function preencheDireita($texto, $tamanho, $preenchimento = ' ') {

        return substr(str_pad(trim(utf8_decode($texto)), $tamanho, $preenchimento, STR_PAD_RIGHT), 0, ($tamanho));
    }
}