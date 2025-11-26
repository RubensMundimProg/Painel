<?php

header("Content-type:application/json; charset=utf-8");
//header('Access-Control-Allow-Origin: *');

////risk-alertas-origem
if($_GET['file'] == 'risk-alertas-origem'){
    $alertas = json_decode(file_get_contents('cache_alertas.json'), true);

    $result = [];
    foreach($alertas as $alerta) {
        $local = getLocalAlerta($alerta);
        if (isset($_GET['uf'])) if ($_GET['uf'] != $local[0]) continue;
        if (isset($_GET['municipio'])){
            if(count($local) != 2) continue;
            if ($_GET['municipio'] != $local[1]) continue;
        }
        if(!isset( $alerta['customAttributes']['dia_da_aplicacao'])){ continue;
            $dia = 'Sem dia';
        }else{
            $dia = preg_match('/1/',$alerta['customAttributes']['dia_da_aplicacao']) ? '1° dia' : '2° dia';
        }

        if($alerta['status'] != "Aberto") continue;

        if($alerta['customAttributes']['origem_informacao'] == 'RM'){
            $alerta['customAttributes']['origem_informacao'] = 'Sistema de Monitoramento';
        }

        $origem = $alerta['customAttributes']['origem_informacao'];
        $dia = $alerta['customAttributes']['dia_da_aplicacao'];

        if (!isset($result[$dia][$origem])) $result[$dia][$origem] = 0;
        $result[$dia][$origem]++;
    }
    successReturn($result);
}

////risk-alertas-origem-uf
if($_GET['file'] == 'risk-alertas-origem-uf'){
    $alertas = json_decode(file_get_contents('cache_alertas.json'), true);

    $result = [];
    foreach($alertas as $alerta) {
        $local = getLocalAlerta($alerta);
        //if(!isset( $alerta['customAttributes']['dia_da_aplicacao'])) continue;
        if(!isset( $alerta['customAttributes']['dia_da_aplicacao'])){ continue;
            $dia = 'Sem dia';
        }else{
            $dia = $alerta['customAttributes']['dia_da_aplicacao'];
        }
        if($alerta['status'] != "Aberto") continue;
        if(!isset($result[$dia][$local[0]])) $result[$dia][$local[0]] = 0;
        $result[$dia][$local[0]]++;
    }

    $tratado = [];
    foreach($result as $dia => $valores){
        foreach($valores as $key => $value){
            $tratado[$dia][] = [
                'descricao' => $key,
                'valor'=>$value
            ];
        }
    }
    $result = $tratado;

    successReturn($result);
}

////geral-rm-alertas-categoria
if($_GET['file'] == 'geral-rm-alertas-categoria'){
    $alertas = json_decode(file_get_contents('cache_alertas.json'), true);

    $result = [];
    foreach($alertas as $alerta) {
        $local = getLocalAlerta($alerta);
        if (isset($_GET['uf'])) if ($_GET['uf'] != $local[0]) continue;
        if (isset($_GET['municipio'])){
            if(count($local) != 2) continue;
            if ($_GET['municipio'] != $local[1]) continue;
        }

        if($alerta['status'] != "Aberto") continue;

        if (!isset($alerta['customAttributes']['dia_da_aplicacao'])) continue;

        if(!$alerta['customAttributes']['categoria_evento']) continue;
        $categoria = explode(' - ',$alerta['customAttributes']['categoria_evento'])[0];
        $dia = $alerta['customAttributes']['dia_da_aplicacao'];

        if (!isset($result[$dia][$categoria])) $result[$dia][$categoria] = 0;
        $result[$dia][$categoria]++;
    }
    successReturn($result);
}

////risk-tratamento-alerta-detalhamento
if($_GET['file'] == 'risk-tratamento-alerta-detalhamento'){
    $alertas = json_decode(file_get_contents('cache_alertas.json'), true);

    $result = [];
    $group = 1;

    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $pageSize = isset($_GET['pageSize']) ? $_GET['pageSize'] : 10;

    if(isset($_GET['order'])){
        $tratado = [];
        foreach($alertas as $alerta){
            if($_GET['order'] == 'categoria'){
                $tratado[$alerta['customAttributes']['categoria_evento']][] = $alerta;
            }
            if($_GET['order'] == 'situacao'){
                $tratado[$alerta['status']][] = $alerta;
            }
            if($_GET['order'] == 'criacao'){
                $tratado[epocToDate($alerta['customAttributes']['Created'],'dmyHis')][] = $alerta;
            }
        }
        if(isset($_GET['orderType'])){
            if($_GET['orderType'] == 'asc'){
                ksort($tratado);
            }else{
                krsort($tratado);
            }
        }else{
            ksort($tratado);
        }

        $alertas = [];
        foreach($tratado as $item){
            foreach($item as $registro){
                $alertas[] = $registro;
            }
        }
    }

    foreach($alertas as $alerta) {
        $local = getLocalAlerta($alerta);
        if (isset($_GET['uf'])) if ($_GET['uf'] != $local[0]) continue;
        if (isset($_GET['municipio'])){
            if(count($local) != 2) continue;
            if ($_GET['municipio'] != $local[1]) continue;
        }
        $categoria = explode(' - ',$alerta['customAttributes']['categoria_evento']);
        if(isset($_GET['categoria'])) if($_GET['categoria'] != $categoria[0]) continue;
        if(isset($_GET['status'])) if($_GET['status'] != $alerta['status']) continue;
        if(isset($_GET['situacao'])) if($_GET['situacao'] != $alerta['status']) continue;

        if(isset($_GET['startDate'])){
            $start = \DateTime::createFromFormat('Y-m-d',$_GET['startDate']);
            if(!$start) continue;
            $data = \DateTime::createFromFormat('d/m/Y', epocToDate($alerta['customAttributes']['Created']));
            if($start > $data) continue;
        }

        if(isset($_GET['endDate'])){
            $end = \DateTime::createFromFormat('Y-m-d',$_GET['endDate']);
            if(!$end) continue;
            $data = \DateTime::createFromFormat('d/m/Y', epocToDate($alerta['customAttributes']['Created']));
            if($end < $data) continue;
        }

        $custom = $alerta['customAttributes'];

        if(!isset($result[$group])) $result[$group] = [];
        if(count($result[$group]) >= $pageSize) $group++;

        $result[$group][] = [
            'nome_usuario'=>$custom['usuario_cadastro'],
            'categoria'=>isset($categoria[0]) ? $categoria[0] : 'Sem Categoria',
            'subcategoria'=>isset($categoria[1]) ? $categoria[1] : 'Sem Subcategoria',
            'impacto_aplicacao'=>$custom['impacto_aplicacao'],
            'origem_informacao'=>$custom['origem_informacao'],
            'data'=>epocToDate($alerta['created'],'d/m/Y H:i:s'),
            'descricao'=>$alerta['description'],
            'uf'=>$local[0],
            'classificacao'=>$custom['nivel_do_alerta_sgir'],
            'coordenacao'=>$custom['coordenacao'],
            'situacao'=>$alerta['status'],
            'municipio'=>$local[1],
            'id'=>$custom['EventID']
        ];

    }

    $retorno = isset($result[$page]) ? $result[$page] : [];
    successReturn(['registros'=> $retorno,'totalPaginas'=>count($result)]);
}

////risk-total-registros
if($_GET['file'] == 'risk-total-registros'){
    $alertas = json_decode(file_get_contents('cache_alertas.json'), true);
    $ocorrencias = json_decode(file_get_contents('cache_ocorrencias.json'), true);

    $result = [];
    foreach($alertas as $alerta) {
        $local = getLocalAlerta($alerta);
        if (isset($_GET['uf'])) if ($_GET['uf'] != $local[0]) continue;
        if (isset($_GET['municipio'])){
            if(count($local) != 2) continue;
            if ($_GET['municipio'] != $local[1]) continue;
        }
        //if(!isset( $alerta['customAttributes']['dia_da_aplicacao'])) continue;
        if(!isset( $alerta['customAttributes']['dia_da_aplicacao'])){ continue;
            $dia = 'Sem dia';
        }else{
            $dia = preg_match('/1/',$alerta['customAttributes']['dia_da_aplicacao']) ? '1° dia' : '2° dia';
        }

        if(!isset($result[$dia]['Alertas '.$alerta['status']])) $result[$dia]['Alertas '.$alerta['status']] = 0;
        $result[$dia]['Alertas '.$alerta['status']]++;
    }

    foreach($ocorrencias as $ocorrencia) {
        $local = getLocalOcorrencia($ocorrencia);
        if (isset($_GET['uf'])) if ($_GET['uf'] != $local[0]) continue;
        if (isset($_GET['municipio'])){
            if(count($local) != 2) continue;
            if ($_GET['municipio'] != $local[1]) continue;
        }
        if($ocorrencia['Status'] == 2) continue;

        //if(!isset($ocorrencia['DiaAplicacao'])) continue;
        if(!isset($ocorrencia['DiaAplicacao'])){ continue;
            $dia = 'Sem dia';
        }else{
            $dia = preg_match('/1/',$ocorrencia['DiaAplicacao']) ? '1° dia' : '2° dia';
        }

        $status = ['Ocorrências Arquivadas','Ocorrências em Triagem','Ocorrências Aceitas'];

        if(!isset($status[$ocorrencia['Status']])) continue;

        if(!isset($result[$dia][$status[$ocorrencia['Status']]])) $result[$dia][$status[$ocorrencia['Status']]] = 0;
        $result[$dia][$status[$ocorrencia['Status']]]++;
    }


    $tratado = [];
    foreach($result as $dia => $item){
        foreach($item as $key => $value){
            $tratado[$dia][] = [
                'descricao'=>$key,
                'valor'=>$value
            ];
        }
    }
    $result = $tratado;
    successReturn($result);
}

///rm-entrega-malotes-uf
if($_GET['file'] == 'rm-entrega-malotes-uf'){
    $data = json_decode(file_get_contents('cache_milha.json'), true);
    $result = [];
    foreach($data[0] as $key => $item){
        if($key == 'ID') continue;
        $exp = explode('|',$item);
        $result['ultima_milha'][] = [
            'descricao'=>$key,
            'valor'=>$exp[0]
        ];

        $result['abstencoes'][] = [
            'descricao'=>$key,
            'valor'=>$exp[1]
        ];
    }

    successReturn($result);
}



/// Função Debug
function debug( $mixExpression , $boolExit = TRUE , $boolFinish = NULL )
{
    static $arrMessages;
    if( ! $arrMessages )
    {
        $arrMessages = array();
    }

    if( $boolFinish )
    {
        return( implode( " <br/> " , $arrMessages ) );
    }

    $arrBacktrace = debug_backtrace();
    $strMessage = "";
    $strMessage .= "<fieldset><legend><font color=\"#007000\">debug</font></legend><pre>" ;
    foreach( $arrBacktrace[0] as $strAttribute => $mixValue )
    {
        if($strAttribute=='args') continue;
        $strMessage .= "<b>" . $strAttribute . "</b> ". $mixValue ."\n";
    }
    $strMessage .= "<hr />";

    # Abre o buffer, impedindo que seja impresso na tela alguma coisa
    ob_start();
    var_dump( $mixExpression );
    # Pega todo o buffer
    $strMessage .= ob_get_clean();

    $strMessage .= "</pre></fieldset>";


    foreach( $arrMessages as $messages )
    {
        print $messages;
        ob_flush();
        flush();
    }
    print $strMessage;
    print "<br /><font color=\"#700000\" size=\"4\"><b>D I E</b></font>";

    if( $boolExit )
    {
        exit();
    }
}

function successReturn($data, $message='Solicitação do Serviço Executada com Sucesso'){
    $return = [];
    $return['data'] = $data;
    $return['message'] = $message;
    $return['error'] = false;
    $return['datetime'] = date('Y-m-d H:i:s');

    echo json_encode($return);
    die;
}

function errorReturn($message){
    $return = [];
    $return['data'] = [];
    $return['message'] = $message;
    $return['error'] = true;
    $return['datetime'] = date('Y-m-d H:i:s');

    echo json_encode($return);
    die;
}

function epocToDate($dataRM, $formato='d/m/Y'){
    $dataRM = str_replace('-0200','-0300', $dataRM);
    $onlyDate = false;
    if(!preg_match('/-0300/',$dataRM)){
        $dataRM = str_replace(')/','-0300)/',$dataRM);
        $onlyDate = true;
    }
    $data=substr(preg_replace('/\/Date\((.*)-[0-9]*\)\//i','$1',$dataRM),0,10);
    $GMT3=substr(preg_replace('/\/Date\((.*)\)\//i','$1',$dataRM),15,-2);
    $GMT3  = -$GMT3;
    $data = gmdate('d/m/Y H:i:s',$data + 3600*($GMT3+date("I")));
    $dateTime = \DateTime::createFromFormat('d/m/Y H:i:s', $data);
    if($onlyDate) $dateTime->add(new \DateInterval('P1D'));
    return $dateTime->format($formato);
}

function getLocalAlerta($alerta){
    $local = explode(' - ', $alerta['customAttributes']['municipio_de_aplicacao']);
    if(!isset($local[1])){
        $ex = explode(' - ',$alerta['title']);
        if(!isset($ex[1])){
            return ['SEM UF','SEM MUNICIPIO'];
        }
        return [$ex[0],$ex[1]];
    }
    return $local;
}

function getLocalOcorrencia($ocorrencia){
    return explode(' - ',$ocorrencia['Municipio']);
}