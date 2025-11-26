<?php

namespace Application\Service;
use \Application\Entity\UltimaMilha as Entity;

class UltimaMilha extends Entity
{
    public function colors()
    {
                //vermelho,laranja,amarelo e verde
        return ['5a1400FA','501478FF','5a14F0FA','5a14B400'];
    }

    public function getColorByPercentage($percentage)
    {
        $return = '';

        if($percentage < 34){
            $return = '5a1400FA'; //vermelho
        }
        if($percentage > 33 && $percentage < 67){
            $return = '501478FF'; //laranja
        }
        if($percentage > 66 && $percentage < 100){
            $return = '5a14F0FA'; //amarelo
        }
        if($percentage > 99){
            $return = '5a14B400'; //verde
        }

        return $return;
    }

    public function getAbstencoes()
    {
        $return = [];
        foreach($this->toArray() as $key => $item){
            if($key == 'Id') continue;
            $dados = explode('|',$item);
            $presentes = 100 - $dados[1];
            $return[strtolower($key)] = [$presentes,$dados[1]];
        }

        $tratado = [];
        foreach($return as $uf => $dados){
            $tratado[] = ['descricao'=>strtoupper($uf),'valores'=>$dados];
        }
        return $tratado;
    }

    public function getMilha()
    {
        $return = [];
        foreach($this->toArray() as $key => $item){
            if($key == 'Id') continue;
            $dados = explode('|',$item);
            $presentes = 100 - $dados[0];
            $return[strtolower($key)] = [$presentes,$dados[0]];
        }

        $tratado = [];
        foreach($return as $uf => $dados){
            $tratado[] = ['descricao'=>strtoupper($uf),'valores'=>$dados];
        }
        return $tratado;
    }

    public function load(){
        $this->setId(1);
        return parent::load();
    }

    public function getMilhaGeral()
    {
        $total = 2700;
        $totalEntregue = 0;
        foreach($this->toArray() as $key => $item){
            if($key == 'Id') continue;
            $dados = explode('|',$item);
            $totalEntregue += $dados[0];
        }

        $calculo = 100 / $total * $totalEntregue;
        $percentual = round($calculo);
        $retorno = [
            [
                'descricao'=>'Entregue',
                'valor'=>$percentual
            ],
            [
                'descricao'=>'Não Entregue',
                'valor'=>100 - $percentual
            ]
        ];

        return $retorno;

    }

    public function getAbstencaoGeral()
    {
        $total = 2700;
        $totalEntregue = 0;
        foreach($this->toArray() as $key => $item){
            if($key == 'Id') continue;
            $dados = explode('|',$item);
            $totalEntregue += $dados[1];
        }

        $calculo = 100 / $total * $totalEntregue;
        $percentual = round($calculo);
        $retorno = [
            [
                'descricao'=>'Abstenção',
                'valor'=>$percentual
            ],
            [
                'descricao'=>'Presentes',
                'valor'=>100 - $percentual
            ]
        ];
        return $retorno;
    }
}