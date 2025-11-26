<?php

namespace Application\Service;

use Modulo\Service\UsuarioApi;

class Relatorios
{
    public $ufs = [
        'AC'=>'Acre',
        'AL'=>'Alagoas',
        'AP'=>'Amapá',
        'AM'=>'Amazonas',
        'BA'=>'Bahia',
        'CE'=>'Ceará',
        'DF'=>'Distrito Federal',
        'ES'=>'Espírito Santo',
        'GO'=>'Goiás',
        'MA'=>'Maranhão',
        'MT'=>'Mato Grosso',
        'MS'=>'Mato Grosso do Sul',
        'MG'=>'Minas Gerais',
        'PA'=>'Pará',
        'PB'=>'Paraíba',
        'PR'=>'Paraná',
        'PE'=>'Pernambuco',
        'PI'=>'Piauí',
        'RJ'=>'Rio de Janeiro',
        'RN'=>'Rio Grande do Norte',
        'RS'=>'Rio Grande do Sul',
        'RO'=>'Rondônia',
        'RR'=>'Roraima',
        'SC'=>'Santa Catarina',
        'SP'=>'São Paulo',
        'SE'=>'Sergipe',
        'TO'=>'Tocantins'];

    public function getPermissionUfs()
    {
        $usuarioApi = new UsuarioApi();
        $grupos = $usuarioApi->get('perfis');

        $cime = [];
        foreach($grupos as $grupo){
            $grupo = strtolower($grupo);
            if(preg_match('/cime/', $grupo)){
                $cime[] = str_replace('cime ','',$grupo);
            }
        }

        $tratado = [];
        if(count($cime)){
            foreach ($cime as $item){
                $item = strtoupper($item);
                $tratado[$item] = $this->getUf($item);
            }
        }else{
            $tratado = array_merge([''=>'Todas'],$this->ufs);
        }

        return $tratado;
    }

    public function getUf($uf)
    {
        return $this->ufs[$uf];
    }

}