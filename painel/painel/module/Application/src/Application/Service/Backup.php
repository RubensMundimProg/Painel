<?php

namespace Application\Service;

use Estrutura\Service\Config;

class Backup
{

    public function backup()
    {
        $this->log('Iniciando rotina de backup...');
        echo 'Capturando dados para conexão...'.PHP_EOL;
        $dados = Config::getConfig('db');

        if(!$dados){
            $this->log('Erro ao capturar dados...');
            die;
        }
        echo 'Configurando comando...'.PHP_EOL;
        $return_var = NULL;
        $output = NULL;
        $time = date('Ymd@His');
        $filename = 'backup_'.$time.'.sql';
        $command = 'mysqldump -u{user} -p{password} --host={host} --result-file="{dir}{filename}" {database}';
        $command = str_replace('{user}',$dados['username'],$command);
        $command = str_replace('{password}',$dados['password'],$command);
        $command = str_replace('{host}',$dados['hostip'],$command);
        $command = str_replace('{dir}',$dados['bkp_dir'],$command);
        $command = str_replace('{filename}',$filename,$command);
        $command = str_replace('{database}',$dados['database'],$command);
        echo 'Executando comando...'.PHP_EOL;
        exec($command, $output, $return_var);

        if($return_var) {
            /* there was an error code: $return_var, see the $output */
            $this->log('Erro ao executar backup...'.PHP_EOL.json_encode($output),true);
        }else{
            $this->deleteOlder($dados['bkp_dir'],$dados['expire_days']);
        }
        $this->log('Terminado a rotina de backup.');
        die;
    }

    public function deleteOlder($dir,$expireDays = 3)
    {
        $files = scandir($dir,0);
        $tratatos = [];
        foreach ($files as $item) {
            if(in_array($item,['.','..'])) continue;
            $tratatos[] = $item;
        }
        if(count($tratatos) > $expireDays){
            $this->log('Excluindo arquivo '.$tratatos[0].' expirado a mais de '.$expireDays.' dias.');
            if(!unlink($dir.$tratatos[0])) $this->log('Erro ao excluir arquivo antigo '.$tratatos[0]);
        }
    }

    public function log($message,$saveLog=false)
    {
        $msgReady = '['.date('Y-m-d H:i:s').'] '.$message.PHP_EOL;
        echo $msgReady;
        if ($saveLog) {
            $logfile = fopen('./data/log/backup.txt', "a+");
            fwrite($logfile, $msgReady);
            fclose($logfile);
        }
    }

}