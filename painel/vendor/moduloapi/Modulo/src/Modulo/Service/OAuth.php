<?php

namespace Modulo\Service;

use Estrutura\Service\Config;

class OAuth {

    protected $ambiente;

    public function __construct() {
        // O construtor volta a ser simples. O ambiente será carregado sob demanda.
    }

    public function setEnvironment($context = 'default') {
        $config = Config::getConfig('API'); // Carrega a configuração base sempre

        if ($context === 'mobile') {
            $mobileConfig = Config::getConfig('API_MOBILE');
            $id = $mobileConfig['client_id'];
            $secret = $mobileConfig['client_secret'];
            $callbackUri = $mobileConfig['redirect_uri']; // URI completa
            $baseSis = $config['baseSis']; // Mantém a base do sistema original
        } else {
            // Lógica original para o fluxo padrão
            $id = $config['idRM'];
            $secret = $config['secretRM'];
            $baseSis = $config['baseSis'];
            $callbackUri = $baseSis . 'callback'; // URI construída
        }

        $baseRm = $config['baseRM'];
        $patchRm = $config['patchRM'];
        $workFlow = $config['workFlowRM'];

        $ambiente = ['DOMAIN_RM' => $baseRm,
            'DOMAIN_CLIENT_RM' => $baseSis,
            'CALLBACK_URI' => $callbackUri, // Chave unificada para a URL de callback
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

    public function login($context = 'default') {
        $this->setEnvironment($context);
        $url = $this->ambiente->ORGANIZATION_RM . $this->ambiente->AUTHORIZATION_ENDPOINT . "?client_id=" . $this->ambiente->CLIENT_ID_RM . "&redirect_uri=" . $this->ambiente->CALLBACK_URI . "&response_type=code";
        return $url;
    }

    public function getToken($auth, $context = 'default') {
        $this->setEnvironment($context);
        $http = new Curl($this->ambiente->ORGANIZATION_RM . $this->ambiente->ACCESS_TOKEN_ENDPOINT);

        $post = array(
            'code' => $auth,
            'client_id' => $this->ambiente->CLIENT_ID_RM,
            'client_secret' => $this->ambiente->CLIENT_SECRET_RM,
            'redirect_uri' => $this->ambiente->CALLBACK_URI,
            'grant_type' => 'authorization_code'
        );

        $http->setopt(CURLOPT_POST, true);
        $http->setopt(CURLOPT_POSTFIELDS, $post);
        $http->setopt(CURLOPT_RETURNTRANSFER, true);
        $http->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $http->setopt(CURLOPT_SSL_VERIFYHOST, false);
        $response = $http->exec();
        $http->close();
        return $response;
    }

    // Métodos originais restaurados
    public function urlRM() {
        if (!$this->ambiente) $this->setEnvironment();
        return $this->ambiente->ORGANIZATION_RM;
    }

    public function urlWF() {
        if (!$this->ambiente) $this->setEnvironment();
        return $this->ambiente->WORKFLOW_RM;
    }

    public function urlDM() {
        if (!$this->ambiente) $this->setEnvironment();
        return $this->ambiente->DOMAIN_RM;
    }
}