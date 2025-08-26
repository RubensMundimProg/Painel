<?php

namespace Classes\Service;

use \Classes\Entity\UltimaMilha as Entity;
use Estrutura\Service\Conexao;

class UltimaMilha extends Entity{
    public function contador(){
        $sql = 'SELECT UF as uf, sum(1) total, status FROM ultima_milha group by UF, STATUS';
        $lista = Conexao::listarSql($sql);
        $valores = [];
        foreach($lista as $item){
            if(!isset($valores['BR'][$item->status])){
                $valores['BR'][$item->status] = $item->total;
            }else{
                $valores['BR'][$item->status] += $item->total;
            }

            if(!isset($valores[$item->uf][$item->status])){
                $valores[$item->uf][$item->status] = $item->total;
            }else{
                $valores[$item->uf][$item->status] += $item->total;
            }
        }

        $tratado = [];
        foreach($valores as $uf => $dados){
            $entregues = (isset($dados['ENTREGUE'])) ? $dados['ENTREGUE'] : 0;
            $naoEntregues = (isset($dados['NAO ENTREGUE'])) ? $dados['NAO ENTREGUE'] : 0;

            $total = $entregues + $naoEntregues;
            $percentualEntregue = round($entregues * 100 / $total);
            $percentualNao = round($naoEntregues * 100 / $total);

            $tratado[] = ['uf'=>$uf, 'entregue'=>$percentualEntregue,'nao'=>$percentualNao];
        }

        return $tratado;
    }
}