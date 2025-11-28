<?php

namespace RiskManager\OData;

use Base\Service\ApiSession;
use Base\Service\Request;
use Estrutura\Service\Config;
use RiskManager\Organization\Service\People;

/**
 * Exibi os detalhes do token enviado
 * Class TokenDetails
 * @package RiskManager\OData
 */
class TokenDetails {

    protected $dados;

    /**
     * Retorna um resumo do usuário do token
     * @param $token
     */
    public function __construct($token){
        $return = [
            'dados'=>[],
            'grupos'=>[]
        ];
        $config = Config::getConfig('API');

        $request = new Request();
        $request->setType('GET');
        $request->setHost($config['baseRM']);
        $request->setToken($token);
        $request->setUrl($config['baseRM'].$config['patchRM'].'/api/info/me');
        $dados = $request->send();

        $return['dados'] = $dados;

        $request = new Request();
        $request->setType('GET');
        $request->setHost($config['baseRM']);
        $request->setToken($token);
        $request->setUrl($config['baseRM'].$config['patchRM'].'/api/Organization/people/'.$dados->Id);
        $result = $request->send();
        if(isset($result->Groups)){
            $return['grupos'] = $result->Groups;
        }

        $this->dados = $return;
    }

    public function getDados(){
        return $this->dados;
    }
} 