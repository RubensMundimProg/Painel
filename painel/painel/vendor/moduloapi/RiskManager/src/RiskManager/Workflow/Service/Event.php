<?php

namespace RiskManager\Workflow\Service;

use Base\Service\Request;
use RiskManager\Workflow\Entity\Event as Entity;

/**
 *
 * Classe Service que processa informações dos eventos
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Workflow\Service
 */
class Event extends Entity
{
    protected $url = '/api/events';
    protected $moduloRequest = 'WF';

    public function save()
    {

        if ($this->getCode()) {
            return $this->update();
        }

        return $this->insert();
    }

    public function insert()
    {
        $request = new Request();
        $request->setType('POST');
        $request->setUrl($this->url);
        $request->setService($this);
        $id = $request->send();
        $this->setCode($id);

        /// Se tiver CustomAttributes ele atualiza os dados
        if ($this->getCustomAttributes()) {
            $this->update();
        }

        return $this;
    }

    public function update()
    {
        if (!$this->getCode()) {
            throw new \Exception('Favor informar o código do evento');
        }

        $request = new Request();
        $request->setType('PUT');
        $request->setUrl($this->url . '/' . $this->getCode());
        $request->setService($this);
        $request->send();

        return $this;
    }

    public function fetchAll()
    {
        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url);
        $request->setService($this);
        $dados = $request->send();
        $lista = [];
        foreach ($dados as $item) {
            $this->exchangeArray($item);
            $lista[] = clone($this);
        }

        return $lista;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function load()
    {
        if (!$this->getCode()) {
            throw new \Exception('Favor informar o código do evento');
        }

        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url . '/' . $this->getCode());
        $request->setService($this);
        $result = $request->send();
        $this->exchangeArray($result);
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function getUpdates()
    {
        if (!$this->getCode()) {
            throw new \Exception('Favor informar o código do evento');
        }

        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url . '/' . $this->getCode().'/updates');
        $request->setService($this);
        $result = $request->send();
        return $result;
    }

    /**
     * @return String
     * @throws \Exception
     */
    public function getInvolveds()
    {
        if (!$this->getCode()) {
            throw new \Exception('Favor informar o código do evento');
        }

        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url . '/' . $this->getCode() . '/involved');
        $request->setService($this);
        $result = $request->send();

        return $result;
    }

    /**
     * @param $paths
     * @return bool
     */
    public function assetAssociate($pathsIn, $clear = false)
    {
        if (!$this->getCode()) {
            throw new \Exception('Código do Evento é Obrigatório!');
        }

        (is_string($pathsIn)) ? $paths[] = $pathsIn : $paths = $pathsIn;

        $tratado = [];
        foreach ($paths as $path) {
            if (empty($path)) continue;
            $tratado[] = ['Path' => $path];
        }

        if ($clear) {
            $associate = $this->getAssetsAssociate();
            foreach ($associate as $ass) {
                $this->assetsDesassociate($ass->RelationshipId);
            }
        }

        $request = new Request();
        $request->setType('POST');
        $request->setUrl($this->url . '/' . $this->getCode() . '/assets');
        $request->setService($this);
        $request->setJson($tratado);
        $result = $request->send();
        if ($result == '') return true;

        return false;
    }

    /**
     * @return \stdClass
     * @throws \Exception
     */
    public function getAssetsAssociate()
    {
        if (!$this->getCode()) {
            throw new \Exception('Código do Evento é Obrigatório!');
        }

        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url . '/' . $this->getCode() . '/assets');
        $request->setService($this);
        $result = $request->send();
        return $result;
    }

    /**
     * @return \stdClass
     * @throws \Exception
     */
    public function assetsDesassociate($RelationshipId)
    {
        if (!$this->getCode()) {
            throw new \Exception('Código do Evento é Obrigatório!');
        }

        $request = new Request();
        $request->setType('DELETE');
        $request->setUrl($this->url . '/' . $this->getCode() . '/assets/' . $RelationshipId);
        $request->setService($this);
        $result = $request->send();
        if ($result == '') return true;
    }

    /**
     *
     */

    public function cancelEvent()
    {
        if (!$this->getCode()) {
            throw new \Exception('Favor informar o código do evento');
        }

        $evento = new Event();
        $evento->setComment('Evento cancelado via aplicação customizada.');
        $evento->setStatus(0);

        $request = new Request();
        $request->setType('PUT');
        $request->setUrl($this->url . '/' . $this->getCode());
        $request->setService($evento);
        $request->send();

        return $this;
    }

    public function getProgressHistory(){
        if(!$this->getCode()){
            throw new \Exception('Favor informar o código do evento');
        }

        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url.'/'.$this->getCode().'/updates?page_size=1000');
        $request->setService($this);
        $result = $request->send();
        return $result;
    }

    public function getProgressHistoryProgress($action=13, $type=0){
        if(!$this->getCode()){
            throw new \Exception('Favor informar o código do evento');
        }

        $request = new Request();
        $request->setType('GET');
        $request->setFinalUrl($this->moduloRequest, $this->url.'/'.$this->getCode().'/updates?$filter=UpdateType+eq+'.$type.'+and+Action+eq+'.$action.'&page_size=1000', false);
        $request->setService($this);
        $result = $request->send();
        return $result;
    }

    public function addAttachment($fileName, $variavel, $arquivo)
    {
        $url = $this->url.'/'.$this->getCode().'/'.$variavel.'/files';

        $request = new Request();
        $request->setFinalUrl('WF',$url);
        $request->setType('POST');
        $request->setJson(['FileName'=>$fileName,'Data'=>base64_encode($arquivo)]);
        $result = $request->send();
        return $result;
    }

    public function getAttachment($variavel){
        $url = $this->url.'/'.$this->getCode().'/'.$variavel.'/files';

        $request = new Request();
        $request->setFinalUrl('WF',$url);
        $request->setType('GET');
        $result = $request->send();;
        return $result;
    }

    public function getDataAttachment($id, $variavel){
        $url = $this->url.'/'.$this->getCode().'/'.$variavel.'/files/'.$id;

        $request = new Request();
        $request->setFinalUrl('WF',$url);
        $request->setType('GET');
        $result = $request->send();;
        return ['FileName'=>$result->FileName,'Data'=>base64_decode($result->Data)];
    }

    public function getProgressAttachment($id=false){
        $id = ($id) ? '/'.$id : '';
        $url = $this->url.'/'.$this->getCode().'/attachments'.$id;
        $request = new Request();
        $request->setService($this);
        $request->setFinalUrl('WF',$url);
        $request->setType('GET');
        $result = $request->send();
        return $result;
    }
}