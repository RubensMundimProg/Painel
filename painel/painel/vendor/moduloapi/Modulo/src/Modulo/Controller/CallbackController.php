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
            if (!isset($_GET['code'])) {
                throw new \Exception("Código de autorização não encontrado.");
            }
            $code = $_GET['code'];
            
            $error = isset($_GET['error']) ? $_GET['error'] : null;
            if ($error) {
                throw new \Exception($error);
            }
            
            $token = json_decode($oauth->getToken($code, 'default')); // Contexto default
            if (!isset($token->access_token)) {
                throw new \Exception("Falha ao obter o token de acesso.");
            }
            
            $access_token = $token->access_token;
            // Lógica original restaurada
            $local_wf = $oauth->urlWF();
            $local_rm = $oauth->urlRM();
            $local_dm = $oauth->urlDM();
            $host = explode("//", $local_dm);
            $apiSession = new ApiSession($access_token, $host, $local_rm, $local_wf, $host);

            $api = new RiskManager();
            $details = json_decode($api->getMeDetails());
            if (!isset($details->Id)) {
                throw new \Exception("Falha ao obter detalhes do usuário.");
            }

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

            return $this->redirect()->toRoute('home');

        } catch (\Exception $e) {
            die("Erro no callback: " . $e->getMessage());
        }
    }

    public function callmobilebackAction()
    {
        $oauth = new OAuth();

        try {
            if (!isset($_GET['code'])) {
                throw new \Exception("Código de autorização não encontrado (mobile).");
            }
            $code = $_GET['code'];

            $error = isset($_GET['error']) ? $_GET['error'] : null;
            if ($error) {
                throw new \Exception($error);
            }

            $token = json_decode($oauth->getToken($code, 'mobile')); // Contexto mobile
            if (!isset($token->access_token)) {
                $debug_info = isset($token->error_description) ? $token->error_description : json_encode($token);
                throw new \Exception("Falha ao obter o token de acesso (mobile). Resposta do servidor: " . $debug_info);
            }
            
            $access_token = $token->access_token;
            // Lógica original restaurada
            $local_wf = $oauth->urlWF();
            $local_rm = $oauth->urlRM();
            $local_dm = $oauth->urlDM();
            $host = explode("//", $local_dm);
            $apiSession = new ApiSession($access_token, $host, $local_rm, $local_wf, $host);

            $api = new RiskManager();
            $details = json_decode($api->getMeDetails());
            if (!isset($details->Id)) {
                throw new \Exception("Falha ao obter detalhes do usuário (mobile).");
            }

            $people = new People();
            $people->setAnonimo();
            $people->setId($details->Id);
            $grupos = $people->getGroups();
            $tratadoGrupo = [];
            foreach ($grupos as $grupo) {
                $tratadoGrupo[] = $grupo->Name;
            }
            $details->Perfis = $tratadoGrupo;

            $usuarioApi = new UsuarioApi();
            $usuarioApi->logar($details);

            return $this->redirect()->toUrl('/mobile');

        } catch (\Exception $e) {
            die("Erro no callback do mobile: " . $e->getMessage());
        }
    }
}