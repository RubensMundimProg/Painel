<?php

namespace RiskManager\ERM\Service;

use Base\Service\Request;
use RiskManager\ERM\Entity\Loss as Entity;

/**
 *
 * Classe Service que fornece orientações sobre como acessar as funcionalidades 
 * do módulo de ERM loss
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage ERM\Service
 */
class Loss extends Entity {

    protected $url = '/api/objects/ERMEvent';
    protected $moduloRequest = 'RM';

    /**
     * Cria/Edita um evento de perda.
     * @return type
     */
    public function save()
    {
        if ($this->getId()) {
            return $this->update();
        }

        return $this->insert();
    }

    /**
     * Cria um evento de perda
     * @return \RiskManager\ERM\Service\Loss
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
     * * Edita um evento de perda
     * 
     * @return \RiskManager\ERM\Service\Loss
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
     * Retorna uma lista paginada dos eventos de perda.
     * @return type
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
            $loss = new Loss();
            $loss->exchangeArray($item);
            $lista[] = $loss;
        }

        return $lista;
    }

    /**
     * Retorna informações sobre um evento de perda.
     * @return type
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
        return $request->send();
    }

    /**
     * Retorna o total de eventos de perda. 
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
     * Exclui um evento de perda.
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

    /**
     * Retorna a lista de atributos de controles corporativos.
     * @return \RiskManager\ERM\Service\Controls
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
