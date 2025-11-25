<?php

namespace RiskManager\ERM\Service;

use Base\Service\Request;
use RiskManager\ERM\Entity\Risks as Entity;

/**
 *
 * Classe Service que fornece orientações sobre como acessar as funcionalidades 
 * do módulo de ERM Risk
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage ERM\Service
 */
class Risks extends Entity {

    protected $url = '/api/objects/ERMRisk';
    protected $moduloRequest = 'RM';

    /**
     * Cria/Edita um risco corporativo
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
     * Cria um risco corporativo
     * @return \RiskManager\ERM\Service\Risks
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
     * Edita um risco corporativo
     * 
     * @return \RiskManager\ERM\Service\Risks
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
     * Retorna uma lista paginada dos riscos corporativos. 
     * 
     * @return \RiskManager\ERM\Service\Risks
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
            $risks = new Risks();
            $risks->exchangeArray($item);
            $lista[] = $risks;
        }

        return $lista;
    }

    /**
     * Retorna informações sobre um risco corporativo
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
     * Retorna o total de riscos corporativos. 
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

    /**
     * Retorna a lista de atributos de riscos corporativos.
     * @return \RiskManager\ERM\Service\Controls
     * @throws \Exception
     */
    public function getAttributes()
    {
        $request = new Request();
        $request->setType('GET');
        $request->setUrl('/api/objects/info/ERMRisk/attributes');
        $request->setService($this);
        return $request->send();
    }

}
