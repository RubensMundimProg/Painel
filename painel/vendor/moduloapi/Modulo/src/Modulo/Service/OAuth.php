<?php

namespace Modulo\Service;

use Estrutura\Service\Config;

class OAuth {

    public function __construct() {
        $this->setEnvironment();
    }

    protected $ambiente;

    public function setEnvironment() {
        $config = Config::getConfig('API');
        $baseSis = $config['baseSis'];
        $baseRm = $config['baseRM'];
        $id = $config['idRM'];
        $secret = $config['secretRM'];
        $patchRm = $config['patchRM'];
        $workFlow = $config['workFlowRM'];

        $ambiente = ['DOMAIN_RM' => $baseRm,
            'DOMAIN_CLIENT_RM' => $baseSis,
            'CALLBACK_URI' => 'callback',
            'WORKFLOW_RM' => $baseRm . $workFlow . '/',
            'ORGANIZATION_RM' => $baseRm . $patchRm . '/',
            'CLIENT_ID_RM' => $id,
            'CLIENT_SECRET_RM' => $secret,
            'AUTHORIZATION_ENDPOINT' => 'APIIntegration/AuthorizeFeatures',
            'ACCESS_TOKEN_ENDPOINT' => 'APIIntegration/Token'
        ];

        $this->ambiente = new \stdClass();
        foreach ($ambiente as $chave => $item) {
            $this->ambiente->{$chave} = $item;
        }
    }

    public function login() {
        $this->setEnvironment();
        $url = $this->ambiente->ORGANIZATION_RM . $this->ambiente->AUTHORIZATION_ENDPOINT . "?client_id=" . $this->ambiente->CLIENT_ID_RM . "&redirect_uri=" . $this->ambiente->DOMAIN_CLIENT_RM . $this->ambiente->CALLBACK_URI . "&response_type=code";
        return $url;
    }

    public function getToken($auth) {

        $http = new Curl($this->ambiente->ORGANIZATION_RM . $this->ambiente->ACCESS_TOKEN_ENDPOINT);

        $post = array(
            'code' => $auth, 
            'client_id' => $this->ambiente->CLIENT_ID_RM, 
            'client_secret' => $this->ambiente->CLIENT_SECRET_RM, 
            'redirect_uri' => $this->ambiente->DOMAIN_CLIENT_RM . $this->ambiente->CALLBACK_URI, 
            'grant_type' => 'authorization_code'
        );

        //$http->setopt(CURLOPT_HTTPHEADER, $head);
        $http->setopt(CURLOPT_POST, true);
        $http->setopt(CURLOPT_POSTFIELDS, $post);
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    public function urlRM() {
        return $this->ambiente->ORGANIZATION_RM;
    }

    public function urlWF() {
        return $this->ambiente->WORKFLOW_RM;
    }

    public function urlDM() {
        return $this->ambiente->DOMAIN_RM;
    }

}
