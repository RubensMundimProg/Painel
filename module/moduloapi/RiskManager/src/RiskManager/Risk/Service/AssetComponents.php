<?php

namespace RiskManager\Risk\Service;

use Base\Service\Request;
use RiskManager\Risk\Entity\Controls as Entity;

/**
 *
 * Classe Service que fornece orientações sobre como acessar as funcionalidades 
 * do módulo de ERM Controls
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Risk\Service
 */
class AssetComponents extends Entity {

    protected $url = '/api/objects/ERMControl';
    protected $moduloRequest = 'RM';

    /**
     * Cria/Edita um controle corporativo.
     */
    public function save()
    {
        if ($this->getId()) {
            return $this->update();
        }

        return $this->insert();
    }

    /**
     * Cria um controle corporativo.
     * @return \RiskManager\Risk\Service\Controls
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
     * Edita um controle corporativo.
     * @return \RiskManager\Risk\Service\Controls
     * @throws \Exception
     */
    public function update()
    {
        if (!$this->getId()) {
            throw new \Exception('Favor informar o ID');
        }

        $request = new Request();
        $request->setType('PUT');
        $request->setUrl($this->url . '/' . $this->getId());
        $request->setService($this);
        $request->send();

        return $this;
    }

    /**
     * 
     * Retorna uma lista paginada dos controles corporativos.
     * @return \RiskManager\Risk\Service\Controls
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
            $controls = new Controls();
            $controls->exchangeArray($item);
            $lista[] = $controls;
        }

        return $lista;
    }

    /**
     * Retorna informações sobre um controle corporativo.
     * @throws \Exception
     */
    public function fetch()
    {

        if (!$this->getId()) {
            throw new \Exception('Favor informar o ID');
        }

        $request = new Request();
        $request->setType('PUT');
        $request->setUrl($this->url . '/' . $this->getId());
        $request->setService($this);
        $request->send();
    }

    /**
     * Retorna o total de controles corporativos. 
     * @throws \Exception
     * @return int
     */
    public function count()
    {
        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url . '/count');
        $request->setService($this);
        return $request->send();
    }

    /**
     * Exclui um controle corporativo.
     * @return \RiskManager\Risk\Service\Controls
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

    /**
     * Retorna a lista de atributos de controles corporativos.
     * @return \RiskManager\Risk\Service\Controls
     * @throws \Exception
     */
    public function getAttributes()
    {
        $request = new Request();
        $request->setType('GET');
        $request->setUrl('/api/objects/info/ERMControl/attributes');
        $request->setService($this);
        return $request->send();
    }

}
