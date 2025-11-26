<?php

namespace Base\Service;

use Zend\Session\Container;

class ApiSession{
    /**
     * @var \Zend\Session\Container
     */
    protected $session;

    public function __construct($token=false, $url=false, $localRm=false,$localWf=false,$host=false, $details=false){
        $this->session = new Container('Api');
        if($token){
            $this->init($token, $url, $localRm,$localWf,$host);
        }
    }

    public function init($token, $url, $localRm,$localWf,$host){
        try{
            $this->session->offsetSet("authorized", TRUE);

            $this->session->offsetSet("token", $token);
            $this->session->offsetSet('url',$url);
            $this->session->offsetSet("url_rm", $localRm);
            $this->session->offsetSet("url_wf", $localWf);
            $this->session->offsetSet("host", $host);
            /*$this->session->offsetSet("id_user", $details->Id);
            $this->session->offsetSet("login_user", $details->Login);
            $this->session->offsetSet("name_user", $details->Name);
            $this->session->offsetSet("email_user", $details->Email);*/

            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public function get($campo){
        return $this->session->offsetGet($campo);
    }

    public function destroy()
    {
        $this->session->getManager()->destroy();
    }
} 