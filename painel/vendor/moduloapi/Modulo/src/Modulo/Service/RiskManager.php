<?php

namespace Modulo\Service;

use Zend\Session\Container;

class RiskManager {

    public $token;
    public $host;
    public $url_rm;
    public $url_wf;

    public function __construct() {

        $api = new \Modulo\Service\ApiSession();
        $this->token = $api->get('token');
        $this->host = $api->get('host');
        $this->url_rm = $api->get('url_rm');
        $this->url_wf = $api->get('url_wf');

        $container = new Container('UsuarioApi');
        if($container->offsetGet('ssi')){
            $this->authAnonymous();
        }

    }

    /**
     * Autenticaçção Anonima para o console
     */
    public function authAnonymous() {

        $config = \Estrutura\Service\Config::getConfig('API');


        $this->host = "localhost";
        $url_rm = $config['baseRM'] . $config['patchRM'];
        $url_wf = $config['baseRM'] . $config['workFlowRM'] . "/";

        $params = [
            "client_id" => $config['idRM'],
            "client_secret" => $config['secretRM'],
            "grant_type" => "client_credentials"
        ];

        $head = "Host: " . $this->host;
        $head .= "Content-Type: application/x-www-form-urlencoded";

        // Cria sessão URL
        $ch = curl_init();

        // Prepara parâmetros cURL
        curl_setopt($ch, CURLOPT_URL, $url_rm . "/APIIntegration/Token");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HEADER, $head);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CAINFO, 0);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $exec = json_decode(curl_exec($ch));
        curl_close($ch);

        $this->token = $exec->access_token;
        $this->url_rm = $url_rm;
        $this->url_wf = $url_wf;

        return true;
    }

    public function getInfo() {

        $token = $this->token;
        $host = $this->host;

        $http = new Curl($this->url_wf . "/api/info");
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    /**
     * 
     * @return type
     */
    public function getMeDetails() {

        $token = $this->token;
        $url_rm = $this->url_rm;

        $http = new Curl($url_rm . "/api/info/me");
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function getMePrivileges() {
        $token = $this->token;
        $host = $this->host;
        $url_rm = $this->url_rm;

        $http = new Curl($url_rm . "/api/info/me/privileges");
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Host: ' . $host, 'Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);

        $response = $http->exec();
        $http->close();

        return $response;
    }

    public function getMeProfiles() {

        $token = $this->token;
        $host = $this->host;
        $url_rm = $this->url_rm;

        $http = new Curl($url_rm . "/api/info/me/profiles");
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Host: ' . $host, 'Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function countEvent($parameters) {

        $token = $this->token;
        $host = $this->host;
        $url_wf = $this->url_wf;

        $parameters = $this->gerarFiltro($parameters);
        $filter = $parameters;
        $http = new Curl($url_wf . "/api/events/count?" . $filter);
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Host: ' . $host, 'Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function getEventByCode($code) {

        $token = $this->token;
        $url_wf = $this->url_wf;

        $http = new Curl($url_wf . "api/events/" . $code);
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();

        return $response;
    }

    public function assocEventAsset($code, $assets) {

        $token = $this->token;
        $url_wf = $this->url_wf;

        $assets = json_encode($assets);

        $http = new Curl($url_wf . "api/events/" . $code . "/assets");
        $http->setopt(CURLOPT_POST, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token, 'Content-Type: application/json', 'X-HTTP-Method-Override: POST', 'Content-Length: ' . strlen($assets)));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $http->setopt(CURLOPT_POSTFIELDS, $assets);
        $response = $http->exec();

        $http->close();

        return $response;
    }

    public function disassocEventAsset($code, $assets) {

        $token = $this->token;
        $url_wf = $this->url_wf;

        $assets = json_encode($assets);

        $http = new Curl($url_wf . "api/events/" . $code . "/assets");
        $http->setopt(CURLOPT_POST, true);
        $http->setopt(CURLOPT_HTTPHEADER, array(
            'Authorization: OAuth2 ' . $token,
            'Content-Type: application/json',
            'X-HTTP-Method-Override: DELETE',
            'Content-Length: ' . strlen($assets)
        ));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $http->setopt(CURLOPT_POSTFIELDS, $assets);
        $response = $http->exec();

        $http->close();

        return $response;
    }

    /**
     * 
     * @param type $parameters
     * @param type $page
     * @param type $page_size
     * @param type $status
     * @param type $order
     * @return type
     */
    public function getEvents($parameters = '', $page = 1, $page_size = 10, $status = NULL, $order = NULL) {

        $token = $this->token;
        $url_wf = $this->url_wf;

        $status = (!is_null($status) ? '&status=' . (int) $status : '');
        $order = (!is_null($order) ? '&$orderby=' . urlencode($order) : '');
        $filter = urlencode($parameters);

        $http = new Curl($url_wf . "/api/events?page=" . (int) $page . "&page_size=" . (int) $page_size . "&$" . "filter=" . $filter . $order . $status);
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function getDataAttachmentEvent($code, $file) {
        $token = $this->token;
        $host = $this->host;
        $url_wf = $this->url_wf;

        $http = new Curl($url_wf . "/api/events/" . $code . "/attachments/" . $file);
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Host: ' . $host, 'Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function getAttachmentsEvent($code) {
        $token = $this->token;
        $host = $this->host;
        $url_wf = $this->url_wf;

        $http = new Curl($url_wf . "/api/events/" . $code . "/attachments");
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Host: ' . $host, 'Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function getProgressEvent($code) {
        $token = $this->token;
        $host = $this->host;
        $url_wf = $this->url_wf;

        $http = new Curl($url_wf . "/api/events/" . $code . "/updates?show_child_events=false&page=1&page_size=1000");
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Host: ' . $host, 'Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    /**
     * Listar ativos associados a um evento
     * 
     * @param type $code
     * @return type
     */
    public function getAssocEventAsset($code) {

        $token = $this->token;
        $host = $this->host;
        $url_wf = $this->url_wf;

        $http = new Curl($url_wf . "/api/events/" . $code . "/assets");
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function createEvent($event) {

        $event = json_encode($event);

        $token = $this->token;
        $host = $this->host;
        $url_wf = $this->url_wf;

        $http = new Curl($url_wf . "api/events");
        $http->setopt(CURLOPT_POST, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token, 'Content-Type: application/json', 'X-HTTP-Method-Override: POST', 'Content-Length: ' . strlen($event)));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $http->setopt(CURLOPT_POSTFIELDS, $event);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    /**
     * 
     * @param type $code
     * @param type $perimeter
     * @return type
     */
    public function changePerimeter($code, $perimeter) {

        $perimeter = json_encode($perimeter);

        $token = $this->token;
        $url_rm = $this->url_rm;

        $http = new Curl($url_rm . "api/Organization/perimeters/" . $code);
        $http->setopt(CURLOPT_POST, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token, 'Content-Type: application/json', 'X-HTTP-Method-Override: PUT', 'Content-Length: ' . strlen($perimeter)));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $http->setopt(CURLOPT_POSTFIELDS, $perimeter);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    /**
     * 
     * Edita um ativo na estrutura organizacional.
     * 
     * @param type $code
     * @param type $asset
     * @return type HTTP status code 204: NoContent
     */
    public function changeAsset($code, $asset) {

        $asset = json_encode($asset);


        $token = $this->token;
        $url_rm = $this->url_rm;

        $http = new Curl($url_rm . "api/Organization/assets/" . $code);
        $http->setopt(CURLOPT_POST, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token, 'Content-Type: application/json', 'X-HTTP-Method-Override: PUT', 'Content-Length: ' . strlen($asset)));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $http->setopt(CURLOPT_POSTFIELDS, $asset);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function getAssetByCode($code) {

        $token = $this->token;
        $url_rm = $this->url_rm;

        $http = new Curl($url_rm . "api/Organization/assets/" . $code);
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function changeEvent($code, $event) {

        $event = json_encode($event);
        $token = $this->token;
        $host = $this->host;
        $url_wf = $this->url_wf;

        $http = new Curl($url_wf . "api/events/" . $code);
        $http->setopt(CURLOPT_POST, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token, 'Content-Type: application/json', 'X-HTTP-Method-Override: PUT', 'Content-Length: ' . strlen($event)));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $http->setopt(CURLOPT_POSTFIELDS, $event);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function addFileEvent($code, $attachment) {
        $token = $this->token;
        $host = $this->host;
        $url_wf = $this->url_wf;

        $http = new Curl($url_wf . "api/events/" . $code . "/attachments");
        $http->setopt(CURLOPT_POST, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Host: ' . $host, 'Authorization: OAuth2 ' . $token, 'Content-Type: application/json', 'X-HTTP-Method-Override: POST', 'Content-Length: ' . strlen($attachment)));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $http->setopt(CURLOPT_POSTFIELDS, $attachment);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function addInvolved($code, $involved) {
        $token = $this->token;
        $host = $this->host;
        $url_wf = $this->url_wf;

        $http = new Curl($url_wf . "api/events/" . $code . "/involved");
        $http->setopt(CURLOPT_POST, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Host: ' . $host, 'Authorization: OAuth2 ' . $token, 'Content-Type: application/json', 'X-HTTP-Method-Override: POST', 'Content-Length: ' . strlen($attachment)));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $http->setopt(CURLOPT_POSTFIELDS, $involved);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function getPerimeters($filter = NULL, $page = NULL, $pageSize = NULL, $orderBy = NULL) {

        $token = $this->token;
        $url_rm = $this->url_rm;

        $filter = ( $filter ? '&$filter=' . urlencode($filter) : '');
        $page = ($page ? '&page=' . (int) $page : '');
        $pageSize = ($pageSize ? '&page_size=' . (int) $pageSize : '');
        $orderBy = ($orderBy ? '&$orderby=' . $orderBy : '');

        $parameters = $this->trataParameters($filter . $page . $pageSize . $orderBy);

        $http = new Curl($url_rm . "/api/organization/perimeters?" . $parameters);
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function getPerimeter($codigodoPerímetro) {

        $token = $this->token;
        $url_rm = $this->url_rm;

        $http = new Curl($url_rm . "api/organization/perimeters/" . $codigodoPerímetro);
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function getPerimetersChildren($codigodoPerímetro, $filter = NULL, $page = NULL, $pageSize = NULL, $orderBy = NULL) {

        $token = $this->token;
        $url_rm = $this->url_rm;

        $filter = ( $filter ? '&$filter=' . urlencode($filter) : '');
        $page = ($page ? '&page=' . (int) $page : '');
        $pageSize = ($pageSize ? '&page_size=' . (int) $pageSize : '');
        $orderBy = ($orderBy ? '&$orderby=' . $orderBy : '');

        $parameters = $this->trataParameters($filter . $page . $pageSize . $orderBy);
        
        $http = new Curl($url_rm . "/api/organization/perimeters/" . $codigodoPerímetro . "/children?" . $parameters);
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function createPerimeter($perimeter) {

        $perimeter = json_encode($perimeter);

        $token = $this->token;
        $url_rm = $this->url_rm;

        $http = new Curl($url_rm . "api/Organization/perimeters");
        $http->setopt(CURLOPT_POST, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token, 'Content-Type: application/json', 'X-HTTP-Method-Override: POST', 'Content-Length: ' . strlen($perimeter)));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $http->setopt(CURLOPT_POSTFIELDS, $perimeter);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function createAsset($asset) {

        $asset = json_encode($asset);

        $token = $this->token;
        $url_rm = $this->url_rm;

        $http = new Curl($url_rm . "api/Organization/assets");
        $http->setopt(CURLOPT_POST, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token, 'Content-Type: application/json', 'X-HTTP-Method-Override: POST', 'Content-Length: ' . strlen($asset)));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $http->setopt(CURLOPT_POSTFIELDS, $asset);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function getAssets($filter, $page = 1, $pageSize = 10, $orderBy = NULL) {

        $token = $this->token;
        $url_rm = $this->url_rm;


        $filter = ( $filter ? '&$filter=' . urlencode($filter) : '');
        $page = ($page ? '&page=' . (int) $page : '');
        $pageSize = ($pageSize ? '&page_size=' . (int) $pageSize : '');
        $orderBy = ($orderBy ? '&$orderby=' . $orderBy : '');

        $parameters = $this->trataParameters($filter . $page . $pageSize . $orderBy);

        $http = new Curl($url_rm . "api/organization/assets?" . $parameters);
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function getGroups($parameters, $page = 1, $page_size = 10) {

        $token = $this->token;
        $url_rm = $this->url_rm;

        $filter = urlencode($parameters);

        $http = new Curl($url_rm . "/api/Organization/groups?page=" . (int) $page . "&page_size=" . (int) $page_size . "&$" . "filter=" . $filter);
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function getPeople($peopleId) {

        $token = $this->token;
        $url_rm = $this->url_rm;

        $http = new Curl($url_rm . "/api/Organization/people/" . $peopleId);
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    /**
     *
     *
     *
     */
    public function getPeopleGroup($groupId, $page = 1, $pageSize = 10, $filter = NULL, $orderBy = NULL) {

        $token = $this->token;
        $url_rm = $this->url_rm;

        $filter = ( $filter ? '&$filter=' . urlencode($filter) : '');
        $page = ($page ? '&page=' . (int) $page : '');
        $pageSize = ($pageSize ? '&page_size=' . (int) $pageSize : '');
        $orderBy = ($orderBy ? '&$orderby=' . $orderBy : '');

        $parameters = $this->trataParameters($filter . $page . $pageSize . $orderBy);

        $http = new Curl($url_rm . "/api/Organization/groups/" . $groupId . "/members?" . $parameters);
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    private function tratarRequest($array) {
        return http_build_query($array, '', '&');
    }

    private function gerarFiltro($parameters) {
        $string = '';
        foreach ($parameters as $chave => $item) {
            if ($string != '') {
                $string .= '&$';
            }

            if ($chave == 'EventType') {
                $string .= 'substringof(\'' . $item . '\',EventType)';
            } else {
                $string .= urlencode($chave . ' eq ' . $item);
            }
        }
        return $string;
    }

    /**
     * 
     * @param type $parameters
     * @return type
     */
    public function trataParameters($parameters) {

        return substr($parameters, 1, (strlen($parameters) - 1));
    }

    /**
     * Recupera o perímetro pai apartir do perimetro filho
     * 
     * @param type $idPerimeterChildren
     * @return type
     */
    public function getPerimeterFather($idPerimeterChildren) {


        $perimeterChildren = json_decode($this->getPerimeter($idPerimeterChildren));

        //Verifica se perimetro existe
        if (is_object($perimeterChildren) && property_exists($perimeterChildren, 'Name')) {

            //Recupera o perimetro pai
            $namePerimetros = explode('>', $perimeterChildren->Path);

            $namePerimetroPai = "";
            foreach ($namePerimetros as $namePerimetro) {

                if (trim($namePerimetro) != $perimeterChildren->Name) {

                    $namePerimetroPai .= trim($namePerimetro) . ' > ';
                }
            }

            $namePerimetroPai = substr($namePerimetroPai, 0, (strlen($namePerimetroPai) - 3));
            $perimetersPai = json_decode($this->getPerimeters("Path eq '" . $namePerimetroPai . "'"));
            foreach ($perimetersPai as $perimeterPai) {

                $perimetersPai = $perimeterPai;
            }

            return $this->getPerimeter($perimeterPai->Id);
        }
    }

    /**
     * Recupera o perímetro pai apartir do ativo filho
     * 
     * @param type $idAssetChildren
     * @return type
     */
    public function getPerimeterFatherAsset($idAssetChildren) {

        $assetChildren = json_decode($this->getAssetByCode($idAssetChildren));

        //Verifica se perimetro existe
        if (is_object($assetChildren) && property_exists($assetChildren, 'Name')) {

            //Recupera o perimetro pai
            $namePerimetros = explode('>', $assetChildren->Path);

            $namePerimetroPai = "";
            foreach ($namePerimetros as $namePerimetro) {

                if (trim($namePerimetro) != trim($assetChildren->Name)) {

                    $namePerimetroPai .= trim($namePerimetro) . ' > ';
                }
            }

            $namePerimetroPai = substr($namePerimetroPai, 0, (strlen($namePerimetroPai) - 3));

            $perimetersPai = json_decode($this->getPerimeters("Path eq '" . trim($namePerimetroPai) . "'"));

            foreach ($perimetersPai as $perimeterPai) {

                $perimetersPai = $perimeterPai;
            }

            return $this->getPerimeter($perimeterPai->Id);
        }
    }

    /**
     * Detalhes dos atributos de eventos
     * 
     * @param type $nomeDaVariavelDoAtributo
     * @return type
     */
    public function getDetailsEventAttributes($nameAttribute) {

        $token = $this->token;
        $url_wf = $this->url_wf;

        $http = new Curl($url_wf . "/api/info/attributes/" . $nameAttribute);
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    /**
     * Detalhes dos atributos de eventos
     * 
     * @param type $nomeDaVariavelDoAtributo
     * @return type
     */
    public function getQueriesOrganization($nameConsulta) {

        $token = $this->token;
        $url_rm = $this->url_rm;

        $http = new Curl($url_rm . "api/Organization/queries/" . $nameConsulta);
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    /**
     * 
     * @param type $variable
     * @param type $filter
     * @param type $page
     * @param type $pageSize
     * @param type $orderBy
     * @return type
     */
    public function getObjects($variable, $filter = '', $page = 1, $pageSize = 10, $orderBy = NULL) {

        $filter = ( $filter ? '&$filter=' . urlencode($filter) : '');
        $page = ($page ? '&page=' . (int) $page : '');
        $pageSize = ($pageSize ? '&page_size=' . (int) $pageSize : '');
        $orderBy = ($orderBy ? '&$orderby=' . $orderBy : '');

        $parameters = $this->trataParameters($filter . $page . $pageSize . $orderBy);

        $token = $this->token;
        $url_rm = $this->url_rm;
        
        $http = new Curl($url_rm . "api/objects/" . $variable . "?" . $parameters);
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    /**
     * 
     * @param type $variable
     * @param type $Id
     * @return type
     */
    public function getObjectById($variable, $Id = NULL) {

        $token = $this->token;
        $url_rm = $this->url_rm;

        $http = new Curl($url_rm . "/api/objects/" . $variable . "/" . $Id);
        $http->setopt(CURLOPT_HTTPGET, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    /**
     * 
     * @param type $event
     * @return type
     */
    public function createObject($nameObject, $object) {

        $object = json_encode($object);

        $token = $this->token;
        $url_rm = $this->url_rm;

        $http = new Curl($url_rm . "/api/objects/" . $nameObject);
        $http->setopt(CURLOPT_POST, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token, 'Content-Type: application/json', 'X-HTTP-Method-Override: POST', 'Content-Length: ' . strlen($object)));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $http->setopt(CURLOPT_POSTFIELDS, $object);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    /**
     * 
     * @param type $nameObject
     * @param type $idObject
     * @param type $object
     * @return type
     */
    public function changeObject($nameObject, $idObject, $object) {

        $object = json_encode($object);

        $token = $this->token;
        $url_rm = $this->url_rm;

        $http = new Curl($url_rm . "api/objects/" . $nameObject . "/" . $idObject);
        $http->setopt(CURLOPT_POST, true);
        $http->setopt(CURLOPT_HTTPHEADER, array('Authorization: OAuth2 ' . $token, 'Content-Type: application/json', 'X-HTTP-Method-Override: PUT', 'Content-Length: ' . strlen($object)));
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $http->setopt(CURLOPT_POSTFIELDS, $object);
        $response = $http->exec();
        $http->close();
        return $response;
    }
}