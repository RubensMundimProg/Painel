<?php

namespace Base\Service;

use Modulo\Service\UsuarioApi;
use Zend\View\Helper\Placeholder\Container;
use Api\Exception\ApiException;

/**
 * Classe padrão para disparos de requisições HTTP
 *
 * @author Bruno Moraes <bruno.silva@modulo.com>
 * @version 1.0
 * @copyright  GPL © 2006, genilhu ltda.
 * @access public
 * @package RiskManager
 * @subpackage Base\Service
 */
class Request {
    /// Dados da Requisição
    protected $url;
    protected $type;
    protected $data;
    protected $response;
    protected $moduloRequest;
    protected $json;
    protected $service;

    /// Dados da Api
    protected $token;
    protected $urlRm;
    protected $urlWf;
    protected $urlRiskManager;
    protected $host;
    protected $anonymous = false;

    /**
     * Constroi o objeto e seta as variaveis de ambiente
     */
    public function __construct(){
        $api = new ApiSession();

        $this->token = $api->get('token');
        $this->host = $api->get('host');
        $this->urlRm = $api->get('url_rm');
        $this->urlWf = $api->get('url_wf');

    }

    public function setUrl($url){
        $this->url = $url;
    }

    public function setType($type){
        $this->type = $type;
    }

    /**
     * Gera o json do array inserido para disparo via post ou get
     * @param Object \Base\Service\AbstractApiService
     */
    public function setService($service){

        $usuario = new UsuarioApi();
        $ssi = $usuario->get('ssi');
        if($ssi) $service->setAnonimo();

        if($service->isAnonimo()){
            $container = new \Zend\Session\Container('Anonimo');
            $dadosAnonimo = $container->offsetGet('dados');
            foreach($dadosAnonimo as $key => $item){
                $this->{$key} = $item;
            }
        };

        $strFilter = ($service->getFilter()) ? $service->getFilter()->serialize() : '';

        $baseUrl = ($service->getModuloRequest() == 'RM') ? $this->urlRm : $this->urlWf;
        $urlRequest = $this->url;
        $this->url = $baseUrl.$urlRequest.$strFilter;

        /// Encoda o objeto em um JSON
        $this->json = json_encode($service->hydrate());

        //debug($this->json, false);
        $this->service = $service;
    }

    public function setFinalUrl($moduloRequest,$url){
        $baseUrl = ($moduloRequest == 'RM') ? $this->urlRm : $this->urlWf;
        $this->url = $baseUrl.$url;
    }

    /**
     * Envia a requisição HTTP
     * @return String $response
     */
    public function send(){
        /**
         * Configurando o tempo de processamento para infinito.
         */
        set_time_limit(0);
        $type = ($this->type == 'GET') ? CURLOPT_HTTPGET : CURLOPT_POST;
        $http = new Curl($this->url);

        $http->setopt($type, true);
        $http->setopt(CURLOPT_HTTPHEADER, $this->getHeader());
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);

        /// Se não for GET Manda o json
        if($this->type != 'GET') $http->setopt(CURLOPT_POSTFIELDS, $this->json);
        $this->response = $http->exec();
        //echo $this->json;

        $http->close();

        /// Valida a requisição procurando erros;
        $this->validarResponse();

        return json_decode($this->response);
    }

    public function validarResponse(){
        if(!$this->response && $this->type == 'GET')
            throw new \Exception('Falha ao enviar a requisição: '.$this->url);

        ///Verificar se não deu erro 400 na requisição
        if(preg_match('/Bad Request/', $this->response))
            throw new \Exception('Erro ao executar a requisição: '.$this->url);

        $dataResponse = json_decode($this->response, true);

        if(isset($dataResponse['error'])){
            $details = isset($dataResponse['error_description']) ? $dataResponse['error_description'] : '';
            $stringErros = implode(';', $dataResponse['error_details']);
            $erro = 'Erro na API -> '.$details.' Detalhes: '.$stringErros;

            if(preg_match('/token/',$erro) || preg_match('/Token/',$erro)){
//                header('Location: /nao-autorizado');
//                header('Location: /autenticar');
                header('Location: /autenticacao');
                die;
            }

            throw new \Exception($erro);
        }
    }

    private function getHeader(){
        if(is_array($this->host)){
            $this->host = str_replace('/','',$this->host[1]);
        }

        if($this->type == 'GET'){
            $header = [
                'Host: ' . $this->host,
                'Authorization: OAuth2 ' . $this->token
            ];
        }else{
            $header = [
                'Host: ' . $this->host,
                'Authorization: OAuth2 ' . $this->token,
                'Content-Type: application/json',
                'X-HTTP-Method-Override: '.$this->type,
                'Content-Length: ' . strlen($this->json)
            ];
        }

        return $header;
    }

    /**
     * Seta o Host do Risk Manager
     * @param $baseRM
     */
    public function setHost($baseRM)
    {
        $this->host = str_replace(['https',':','/'],'',$baseRM);
    }

    /**
     * Seta o Token enviado
     * @param $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @param $array
     */
    public function setJson($array){
        $this->json = json_encode($array);
    }
} 