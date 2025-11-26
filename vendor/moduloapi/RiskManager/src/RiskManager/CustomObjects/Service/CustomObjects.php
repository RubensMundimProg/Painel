<?php

namespace RiskManager\CustomObjects\Service;

use Api\Object\AbstractObjectService;
use Base\Service\Request;
use Estrutura\Helpers\Integer;
use RiskManager\CustomObjects\Entity\CustomObjects as Entity;

/**
 *
 * Classe Service que fornece orientações sobre os objetos customizados
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage CustomObjects\Service
 */
class CustomObjects extends Entity {

    protected $url = '/api/objects/';
    protected $moduloRequest = 'RM';
    protected $objeto = '';

    public function __construct(AbstractObjectService $objeto){
        $this->objeto = $objeto;
        $this->url .= $objeto->objectName;
    }

    /**
     * @return AbstractObjectService|string
     */
    public function getObjeto()
    {
        return $this->objeto;
    }

    /**
     * @param AbstractObjectService|string $objeto
     */
    public function setObjeto($objeto)
    {
        $this->objeto = $objeto;
    }



    /**
     * Cria/Edita um objeto customizado
     * @return type
     */
    public function save()
    {
        if ($this->objeto->getId()) {
            return $this->update();
        }

        return $this->insert();
    }

    /**
     * Cadastra um objeto customizado
     * @return \RiskManager\CustomObjects\Service\CustomObjects
     */
    public function insert()
    {
        $request = new Request();
        $request->setType('POST');
        $request->setUrl($this->url);
        $request->setService($this);
        $id = $request->send();
        $this->setId($id);
        return $this;
    }

    /**
     * Edita um objeto customizado
     * 
     * @return \RiskManager\CustomObjects\Service\CustomObjects
     * @throws \Exception
     */
    public function update()
    {
        if (!$this->objeto->getId()) {
            throw new \Exception('Favor informar o ID');
        }

        $request = new Request();
        $request->setType('PUT');
        $request->setUrl($this->url . '/' . $this->objeto->getId());
        $request->setService($this);
        $request->send();

        return $this;
    }

    /**
     * Retorna uma lista paginada dos objetos customizados.
     */
    public function fetchAll()
    {
        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url);
        $request->setService($this);
        $dados = $request->send();
        $lista = [];
        foreach ($dados as $item) {
            $classeObj = get_class($this->objeto);
            $custom = new $classeObj;

            $custom->exchangeArray($item);
            $lista[] = $custom;
        }

        return $lista;
    }

    /**
     * Carrega informações sobre um objeto customizado
     * @return this
     * @throws \Exception
     */
    public function load()
    {
        if (!$this->getId()) {
            throw new \Exception('Favor informar o ID');
        }

        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url . '/' . $this->getId());
        $request->setService($this);
        $result = $request->send();

        $this->objeto->exchangeArray($result);
        return $this->objeto;
    }

    /**
     * Exclui um risco corporativo.
     * @return \RiskManager\ERM\Service\Controls
     * @throws \Exception
     */
    public function delete()
    {
        if (!$this->getId()) {

            throw new \Exception('Favor informar o ID');
        }

        $request = new Request();
        $request->setType('DELETE');
        $request->setUrl($this->url . '/' . $this->getId());
        $request->setService($this);
        $request->send();

        return $this;
    }


    public function hydrate($atribute= null, $clear= true){
        $dados =  $this->objeto->toArray();

        unset($dados['objectName']);
        unset($dados['Id']);

        foreach($dados as $key => $campo){

            if(preg_match('/^[0-9a-z]{8}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{12}$/', $campo)){
                $dados[$key] = [ 'Id'=>  $campo];
            }

            if(preg_match('/^[0-9]+$/', $campo) && $key != 'ano' && $key != 'sequencia'){
                $dados[$key] = (Integer) $campo;
            }

        }


        return $dados;

    }
}
