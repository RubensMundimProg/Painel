<?php
namespace RiskManager\OData;

/**
 * Classe de Manipulação da filtros a serem gerados pela aplicação.
 * 
 * @author João Barbosa <joao.barbosa@modulo.com>
 * @version 0.1
 * @copyright © 2015, Módulo Security Solutions S/A.
 * @access public
 * @package OData
 * @subpackage Service
 */
class Filter {
    
    // Constantes de Attributos
    const A_GUID            = 'guid';
    const A_STRING          = 'string';
    const A_INT             = 'int';
    const A_FLOAT           = 'float';
    const A_DATETIME        = 'datetime';
    const A_SINGLESELECT    = 'singleselect';
    const A_MULTISELECT     = 'multiselect';
    const A_BOOLEAN         = 'boolean';
    
    // Constantes dos Tipos de Filtros
    const F_EQ          = 'eq';
    const F_NE          = 'ne';
    const F_SUBSTRING   = 'substringof';
    const F_STARTSWITH  = 'startswith';
    const F_ENDSWITH    = 'endswith';
    const F_GT          = 'gt';
    const F_GE          = 'ge';
    const F_LT          = 'lt';
    const F_LE          = 'le';
    const F_GUID        = 'guid';
    
    //Constantes de Ordenação
    const O_ASC     = 'asc';
    const O_DESC    = 'desc';
        
    // Tipos de atributos com os tipos de filtro
    private $guid         = ['eq', 'ne'];
    private $string       = ['eq', 'ne', 'substringof', 'startswith', 'endswith'];
    private $int          = ['eq', 'ne', 'gt', 'ge', 'lt', 'le'];
    private $float        = ['eq', 'ne', 'gt', 'ge', 'lt', 'le'];
    private $datetime     = ['eq', 'ne', 'gt', 'ge', 'lt', 'le'];
    private $singleselect = ['eq', 'ne'];
    private $multiselect  = ['eq', 'ne'];
    private $boolean      = ['eq'];

    // Atributos de Armazenamento
    protected $filters  = [];
    protected $orders   = [];
    protected $page     = 1;
    protected $pageSize = 10;
    protected  $status = false;
    
    /**
     *  Adiciona uma Nova condição ao Filtro
     * 
     * @param String $filterType (Tipo de Filtro a ser Efetuado)
     * @param String $attrType (Tipo do Atributo a ser Filtrado)
     * @param String $attr ( Nome do Atributo a ser Filtrado)
     * @param Array|String $filterOptions (Opções do Filtro a serem filtradas)
     * @param String $junctionType (Tipo de Ação a Junção)
     */
    public function where($filterType, $attrType, $attr, $filterOptions, $junctionType = 'and'){
        
        $attrTypes = [
            'guid',
            'string',
            'int',
            'float',
            'datetime',
            'singleselect',
            'multiselect',
            'boolean'
        ];
        
        
        if(!in_array($attrType, $attrTypes)){
            throw new \Exception("O Tipo de Informado é Inválido", 1);
        }
        
        if(!in_array($filterType, $this->{$attrType})){
            throw new \Exception("O Tipo de Filtro Informado é inválido ou não é permitido para o Tipo de Atributo Informado", 1);
        }

        if($attrType == 'datetime'){
            $filterOptions = 'datetime\''.$filterOptions.'\'';
        }

        $this->filters[] = [
            'filterType'    => $filterType,
            'attrType'      => $attrType,
            'attr'          => $attr,
            'filterOptions' => $filterOptions,
            'junctionType'  => $junctionType
        ];

        return $this;
    }

    /**
     *  Adiciona uma Nova condição ao Filtro
     * 
     * @param String $filterType (Tipo de Filtro a ser Efetuado)
     * @param String $attrType (Tipo do Atributo a ser Filtrado)
     * @param String $attr ( Nome do Atributo a ser Filtrado)
     * @param Array|String $filterOptions (Opções do Filtro a serem filtradas)
     */
    public function andWhere($filterType, $attrType, $attr, $filterOptions){
        
        $this->where($filterType, $attrType, $attr, $filterOptions, 'and');
        
        return $this;
    }

    /**
     *  Adiciona uma Nova condição ao Filtro
     * 
     * @param String $filterType (Tipo de Filtro a ser Efetuado)
     * @param String $attrType (Tipo do Atributo a ser Filtrado)
     * @param String $attr ( Nome do Atributo a ser Filtrado)
     * @param Array|String $filterOptions (Opções do Filtro a serem filtradas)
     */
    public function orWhere($filterType, $attrType, $attr, $filterOptions){
        
        $this->where($filterType, $attrType, $attr, $filterOptions, 'or');

        return $this;
    } 
    
    
    /**
     * Adiciona um novo Grupo ao Filtro com a confição And
     * 
     * @param RiskManager\OData\Filter  $group Grupo de Filtros
     */
    public function addGroup(\RiskManager\OData\Filter $group, $junctionType = 'and'){
        
        $this->filters[] = [
            'filterType'    => 'group',
            'group'         => $group,
            'junctionType'  => $junctionType
        ];

        return $this;
    } 
    
    /**
     * Adiciona um novo Grupo ao Filtro com a confição And
     * 
     * @param RiskManager\OData\Filter  $group Grupo de Filtros
     */
    public function andGroup(\RiskManager\OData\Filter $group){
        
        $this->addGroup($group, 'and');

        return $this;
    }
    
    /**
     * Adiciona um novo Grupo ao Filtro com a confição Or
     * 
     * @param RiskManager\OData\Filter  $group Grupo de Filtros
     */
    public function orGroup(\RiskManager\OData\Filter $group){
        
        $this->addGroup($group, 'or');

        return $this;
    }
    
    /**
     * Adiciona uma nova ordenação para a Requisição
     * 
     * @param String $field Campo a Ser Ordenado
     * @param String $order Ordenação para o Campo
     */
    public function addOrder($field, $order){
        $this->orders[] = [
            'field' => $field,
            'order' => $order
        ];
        
        return $this;
    }
    
    /**
     * Define a Página da Consulta
     * 
     * @param Integer $page Pagina 
     */
    public function setPage($page = 1){
        $this->page = $page;
        
        return $this;
    }
    
    /**
     * Define a Quantidade de Registros por Página
     * 
     * @param Integer $pageSize Tamanho da Pagina
     */
    public function setPageSize($pageSize = 10){
        $this->pageSize = $pageSize;
        
        return $this;
    }
    
    /**
     * Serializa as informações setadas na Aplicação
     */
    public function serialize($group = false){
        
        $serialize = '';
        
        foreach($this->filters as $key => $filter){

            if($key == 0){
                $filter['junctionType'] = '';
            }
            
             switch($filter['filterType']){
                 case 'eq':
                     
                     if($filter['attrType'] == 'guid'){
                         
                         $serialize .= sprintf(" %s %s eq guid'%s'", $filter['junctionType'], $filter['attr'], $filter['filterOptions']);
                         
                     } elseif($filter['attrType'] == 'singleselect'){
                         
                         $serialize .= sprintf(" %s %s/Caption eq '%s'", $filter['junctionType'], $filter['attr'], $filter['filterOptions']);

                     } elseif($filter['attrType'] == 'boolean'){

                         $serialize .= sprintf(" %s %s eq %s", $filter['junctionType'], $filter['attr'], $filter['filterOptions']);

                     } else {
                         
                         $serialize .= sprintf(" %s %s eq '%s'", $filter['junctionType'], $filter['attr'], $filter['filterOptions']);
                         
                     }
                     
                    break;
                 case 'ne':
                     
                     if($filter['attrType'] == 'guid'){
                         
                         $serialize .= sprintf(" %s %s ne guid'%s'", $filter['junctionType'], $filter['attr'], $filter['filterOptions']);
                         
                     } elseif($filter['attrType'] == 'singleselect'){
                         
                         $serialize .= sprintf(" %s %s/Caption ne '%s'", $filter['junctionType'], $filter['attr'], $filter['filterOptions']);
                         
                     } else {
                         
                         $serialize .= sprintf(" %s %s ne '%s'", $filter['junctionType'], $filter['attr'], $filter['filterOptions']);
                         
                     }
                                          
                     break;
                 case 'substringof':
                     $serialize .= sprintf(" %s substringof('%s',%s)", $filter['junctionType'], $filter['filterOptions'], $filter['attr']);
                     break;
                 case 'startswith':
                     $serialize .= sprintf(" %s startswith(%s,'%s')", $filter['junctionType'], $filter['attr'], $filter['filterOptions']);
                     break;
                 case 'endswith':
                     $serialize .= sprintf(" %s endswith(%s,'%s')", $filter['junctionType'], $filter['attr'], $filter['filterOptions']);
                     break;
                 case 'gt':
                     $serialize .= sprintf(" %s %s gt %s", $filter['junctionType'], $filter['attr'], $filter['filterOptions']);
                     break;
                 case 'ge':
                     $serialize .= sprintf(" %s %s ge %s", $filter['junctionType'], $filter['attr'], $filter['filterOptions']);
                     break;
                 case 'lt':
                     $serialize .= sprintf(" %s %s lt %s", $filter['junctionType'], $filter['attr'], $filter['filterOptions']);
                     break;
                 case 'le':
                     $serialize .= sprintf(" %s %s le %s", $filter['junctionType'], $filter['attr'], $filter['filterOptions']);
                     break;
                 case 'group':
                     $serialize .= sprintf(" %s (%s)", $filter['junctionType'], $filter['group']->serialize(true));
                     break;
             }
            
        }

        if(empty($this->orders)){
            $order = '';
        } else {
            $orders = [];
            foreach($this->orders as $ord){
                $orders[] = sprintf("%s %s", $ord['field'], $ord['order']);
            }
            
            $order = '&$orderby='.urlencode(implode($orders, ', '));
        }

        /// Adiciona ao filtro o Status se for setado
        $status = ($this->status) ? 'status='.$this->status.'&' : '';
        $serialize = urlencode(trim($serialize));
//$serialize='ExpectedEndDate+ge+datetime\'2015-04-27\'';
        if($group){
            return (trim($serialize));
        } else {
            return sprintf("?%s\$filter=%s&page=%s&page_size=%s%s",$status,(trim($serialize)), $this->page, $this->pageSize, $order);
        }

    }

    /**
     * Seta o status para consultas na Api
     * @param $cod_status
     */
    public function setStatus($cod_status)
    {
        $this->status = $cod_status;
    }
}
