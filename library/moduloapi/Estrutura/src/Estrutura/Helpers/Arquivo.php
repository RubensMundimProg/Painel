<?php
namespace Estrutura\Helpers;
/**
 *
 * @author ronaldo
 *
 */
class Arquivo {

    /**
     * Converte array multidimencional $_FILE por atributos (tmp_name, size...)
     * em um array multidimencional por arquivo
     * @param unknown_type $files
     * @return array
     */
    public static function converterMultiplosArquivos($files) {

        $ret = array();

        if (isset($files['tmp_name']) &&
                is_array($files['tmp_name'])) {

            foreach ($files['name'] as $idx => $name) {

                $ret[$idx] = array('name' => $name,
                    'tmp_name' => $files['tmp_name'][$idx],
                    'size' => $files['size'][$idx],
                    'type' => $files['type'][$idx],
                    'error' => $files['error'][$idx]);
            }
        } else {

            $ret = $files;
        }

        return $ret;
    }

    /**
     * Pega o tamanho do arquivo no servidor
     * @param string $caminhoArquivo
     * @return string
     */
    public static function tamanhoArquivo($caminhoArquivo) {

        if (file($caminhoArquivo)) {

            $nSize = filesize($caminhoArquivo);
            if ($nSize < 1024) {

                return strval($nSize) . ' bytes';
            }
            if ($nSize < pow(1024, 2)) {

                return ((int) ($nSize / 1024)) . ' KB';
            }
            if ($nSize < pow(1024, 3)) {

                return ((int) ($nSize / pow(1024, 2))) . ' MB';
            }
            if ($nSize < pow(1024, 4)) {
                return ((int) ( $nSize / pow(1024, 3))) . ' GB';
            }
            return '';
        }
    }

    /**
     *
     * @param string $nomeArquivo => nome do arquivo
     * @return string extencao
     */
    public static function extensaoArquivo($nomeArquivo) {

        $nomeArray = explode(".", $nomeArquivo);
        return strtolower(end($nomeArray));
    }

}
