<?php

namespace Classes\Service;

use \Classes\Entity\Alertas as Entity;
use Estrutura\Service\ParseCSV;
use RiskManager\Workflow\Service\Attributes;
use Zend\Session\Container;

class Alertas extends Entity{

    public function categoriaSubCategoria($filter = '',$todas=false)
    {
        $info = new Attributes('categoria_evento');
        if($info->getTypeName() != 'SingleSelect') throw new \Exception('Campo inválido para Select');
        $array = $info->getAllowedValues();
        $opcoes = [''=>'Selecione'];
        foreach($array as $item){
            if($filter){
                if(preg_match("/{$filter}/",$item)){
                    $opcoes[$item] = $item;
                }
            }else{
                $opcoes[$item] = $item;
            }
        }

        ksort($opcoes);

        $tratadosCategorias = [];
        $tratadosSubcategorias = [];
        foreach ($opcoes as $opcoe) {
            if(preg_match('/ - /',$opcoe)){
                $aux = explode(' - ',$opcoe);
                if(!in_array($aux[0],$tratadosCategorias)) $tratadosCategorias[$aux[0]] = $aux[0];
                $tratadosSubcategorias[$aux[0]][] = $aux[1];
            }else{
                if($opcoe == 'Selecione'){
                    $tratadosCategorias[""] = $opcoe;
                }else{
                    if(!in_array($opcoe,$tratadosCategorias)) $tratadosCategorias[$opcoe] = $opcoe;
                    $tratadosSubcategorias[$opcoe] = 'Não existe subcategoria';
                }
            }
        }


        $tratado = [];
        if($todas){
            $tratado['Todas'] = 'Todas';
        }
        foreach($tratadosCategorias as $lastFilter){
            if(preg_match('/__/',$lastFilter)) continue;
            $tratado[$lastFilter] = $lastFilter;
        }

        $tratadosCategorias = $tratado;

        $tratado = [];
        foreach($tratadosSubcategorias as $key => $lastFilter){
            if(is_array($lastFilter)){
                $sub = [];
                foreach($lastFilter as $item){
                    if(preg_match('/__/',$item)) continue;
                    $sub[] = $item;
                }
                $lastFilter = $sub;
            }else{
                if(preg_match('/__/',$lastFilter)) continue;
            }
            $tratado[$key] = $lastFilter;
        }
        $tratadosSubcategorias = $tratado;

        if($filter){
            return $tratadosSubcategorias[$filter];
        }

        if($todas) unset($tratadosCategorias['Selecione']);

        return ['categorias'=>$tratadosCategorias,'subcategorias'=>$tratadosSubcategorias];
    }

    public function getListaSubCategoria()
    {
        if(!$this->getCategoria()) return [];
        $tratados = [];
//        $tratados = [''=>'Selecione'];
        if(is_array($this->categoriaSubCategoria($this->getCategoria()))){
            foreach ($this->categoriaSubCategoria($this->getCategoria()) as $item) {
                $tratados[$item] = $item;
            }
        }

        return $tratados;
    }

    public static function removeAccents($str,$case = MB_CASE_UPPER)
    {
        $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );

        return mb_convert_case(strtr( $str, $unwanted_array ), $case, "UTF-8");
    }

    public function getCoordenacoes($uf = null,$municipio = null)
    {

        if($this->getMunicipio()){
            $aux = explode(' - ',$this->getMunicipio());
            $uf = $aux[0];
            $municipio = $aux[1];
        }
        $tratados = [];

        $cache = $this->getServiceLocator()->get('Zend\Cache\Storage\Filesystem');
        $cache->setOptions($cache->getOptions()->setTtl(3600)); //3600 segundos = 1 hora
        $result = $cache->getItem('coordenacoes', $success);
        $success = false;
        if (!$success) {

            $avaliacao = new Container('SistemaSelecionado');
            $exame = $avaliacao->offsetGet('sistema');

            header('Content-Type: text/html; charset=utf-8');
            $csv = new ParseCSV();
            $csv->delimiter = ";";
            $csv->parseCSV('data/coordenacoes/coordenacoes_'.$exame.'.csv');
            $aux = $csv->data;
            $tratado = [];
            foreach($aux as $item){
                $nItem = [];
                foreach($item as $key => $value){
                    $nItem[$key] = $value = utf8_encode($value);
                }
                $tratado[] = $nItem;
            }
            $result = $tratado;
            $cache->addItem('coordenacoes', $result);

        }

        $tratados = [];
        foreach ($result as $item) {
            if(Alertas::removeAccents($uf) == Alertas::removeAccents($item['SG_UF_PROVA'])
                && Alertas::removeAccents($municipio) == Alertas::removeAccents($item['NO_MUNICIPIO_PROVA'])){
                $tratados[$item['CO_COORDENACAO'].' '.$item['NO_LOCAL_PROVA']] = $item['CO_COORDENACAO'].' '.$item['NO_LOCAL_PROVA'];
            }
        }

        return $tratados;
    }

    public function hasAnexos(){
        if(!$this->getId()) return false;
        $service = new Alertas();
        $service->setId($this->getId());
        $service->load();
        return ($service->getAnexo())?1:0;
    }

    public function fetchAnexos(){
        if(!$this->getId()) return [];
        $service = new Alertas();
        $service->setId($this->getId());
        $service->load();
        return explode('|',$service->getAnexo());
    }

    public function setUfFiltro($filtro){
        $this->setUf($filtro);
    }

}