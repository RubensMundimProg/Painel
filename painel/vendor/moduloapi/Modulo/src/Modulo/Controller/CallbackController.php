<?php

namespace Modulo\Controller;

use Modulo\Service\ApiSession;
use Modulo\Service\OAuth;
use Modulo\Service\RiskManager;
use Modulo\Service\UsuarioApi;
use RiskManager\Organization\Service\People;
use Zend\Mvc\Controller\AbstractActionController;

class CallbackController extends AbstractActionController {

    public function indexAction() {
        
        $oauth = new OAuth();

        try {
            $code = $_GET['code'];
            
            $error = isset($_GET['error']) ? $_GET['error'] : null;

            if ($error) {

                throw new \Exception($error);
            }
            
            $token = json_decode($oauth->getToken($code));        

            $access_token = $token->access_token;
            $local_wf = $oauth->urlWF();
            $local_rm = $oauth->urlRM();
            $local_dm = $oauth->urlDM();
            $host = explode("//", $local_dm);

            $apiSession = new ApiSession($access_token, $host, $local_rm, $local_wf, $host);

            $api = new RiskManager();
            $details = json_decode($api->getMeDetails());

            $people = new People();
            $people->setAnonimo();

            $people->setId($details->Id);
            $grupos = $people->getGroups();

            $tratadoGrupo = [];
            foreach($grupos as $grupo){
                $tratadoGrupo[] = $grupo->Name;
            }
            $details->Perfis = $tratadoGrupo;

            $usuarioApi = new UsuarioApi();
            $usuarioApi->logar($details);

            if (!$apiSession) {
                throw new \Exception('Erro ao salvar dados na sessão');
            }

            return $this->redirect()->toRoute('home');
        } catch (\Exception $e) {
            debug($e->getMessage());
        }
    }

}
