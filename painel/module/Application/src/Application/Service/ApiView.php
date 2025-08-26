<?php

namespace Application\Service;

use Zend\View\Model\JsonModel;

class ApiView
{
    public function successReturn($data, $message){
        $return = [];
        $return['data'] = $data;
        $return['message'] = $message;
        $return['error'] = false;
        $return['datetime'] = date('Y-m-d H:i:s');

        return new JsonModel($return);
    }

    public function errorReturn($message){
        $return = [];
        $return['data'] = [];
        $return['message'] = $message;
        $return['error'] = true;
        $return['datetime'] = date('Y-m-d H:i:s');

        return new JsonModel($return);
    }
}