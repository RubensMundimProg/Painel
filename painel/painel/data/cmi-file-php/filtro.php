<?php
//header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Token");

$arquivo = ($_GET['tipo'] == 'ocorrencia') ? 'risk-ocorrencias-tempo-detalhamento' : 'risk-tratamento-alerta-detalhamento';
$arquivo .= '.json';

$usuario = ($_GET['usuario']) ? $_GET['usuario'] : false;
$uf = ($_GET['uf']) ? $_GET['uf'] : false;
$municipio = ($_GET['municipio']) ? $_GET['municipio'] : false;

$dados = json_decode(file_get_contents($arquivo), true);
$tratado = [];
foreach($dados['data'] as $data){
    if($usuario){
        if(!preg_match('/'.$usuario.'/', $data['usuario'])) continue;
    }
    if($uf){
        if(!preg_match('/'.$uf.'/', $data['uf'])) continue;
    }
    if($municipio){
        if(!preg_match('/'.$municipio.'/', $data['municipio'])) continue;
    }
    $tratado[] = $data;
}
$dados['data'] = $tratado;
echo json_encode($dados);
die;

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