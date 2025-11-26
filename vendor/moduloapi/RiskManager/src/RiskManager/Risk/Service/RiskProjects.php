<?php

namespace RiskManager\Risk\Service;

use Base\Service\Request;
use RiskManager\Risk\Entity\Controls as Entity;

/**
 *
 * Classe Service fornece orientações sobre como acessar as funcionalidades do módulo de Riscos, Projetos de Risco.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Risk\Service
 */
class RiskProjects extends Entity {

    protected $url = '/api/risk/projects';
    protected $moduloRequest = 'RM';

    /**
     * Cria/Edita um novo projeto de riscos no sistema.
     */
    public function save()
    {
        if ($this->getId()) {
            return $this->update();
        }

        return $this->insert();
    }

    /**
     * Cria um novo projeto de riscos no sistema.
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
     * Edita um projeto cadastrado no módulo de Riscos.
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

}
