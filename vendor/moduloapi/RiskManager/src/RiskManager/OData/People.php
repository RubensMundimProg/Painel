<?php

namespace RiskManager\OData;


class People {
    protected $data;

    public function setPerson($people){
        $this->data[] = ['Name'=>$people,'Type'=>'Person'];
    }

    public function setGroup($group){
        $this->data[] = ['Name'=>$group,'Type'=>'Group'];
    }

    public function getData(){
        if(count($this->data) == 1){
            return (object) end($this->data);
        }

        return $this->data;
    }
} 