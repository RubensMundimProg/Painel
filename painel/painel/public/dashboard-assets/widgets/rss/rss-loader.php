<?php
/**
 * Created by PhpStorm.
 * User: Bruno
 * Date: 6/24/14
 * Time: 10:20 AM
 */

//SET TIMEZONE
date_default_timezone_set("America/Sao_Paulo");


//LISTA DE PALAVRAS CHAVES
$needle = array (
    0 => "vazamento da prova",
    1 => "vasamento da prova",
    2 => "exame nacional de ensino",
    3 => "redação",
    4 => "redacao",
    5 => "vestibular",
    6 => "vazamento de questão",
    7 => "vazamento de questões",
    8 => "rua interditada",
    9 => "avenida interditada",
    10 => "rodovia interditada",
    11 => "escola destelhada",
    12 => "erro de português",
    13 => "gabarito",
    14 => "aplicador",
    15 => "cartão de resposta",
    16 => "cartao de resposta",
    17 => "folha de resposta",
    18 => "questao letra",
    19 => "questão letra",
    20 => "fiscal de prova",
    21 => "prova celular",
    22 => "InstaEliminado",
    23 => "erro ortográfico",
    24 => "resultado do enem",
    25 => "enem"
);


//FUNÇÃO QUE FAZ O FILTRO DAS PALAVRAS CHAVES
function substr_count_array( $haystack, $needle ) {
    $count = 0;
    foreach ($needle as $substring) {
        $count += substr_count( $haystack, $substring);
    }
    return $count;
}


//CARREGA TODOS OS RSS

copy("http://g1.globo.com/dynamo/brasil/rss2.xml","rss/globo-news.xml");
copy("http://g1.globo.com/dynamo/educacao/rss2.xml","rss/globo-educacao.xml");
copy("http://news.google.com.br/news?pz=1&cf=all&ned=pt-BR_br&hl=pt-BR&output=rss","rss/google-news.xml");
copy("http://www.climatempo.com.br/rss/destaque.xml","rss/climatempo-destaque.xml");
copy("http://www.climatempo.com.br/rss/brasil.xml","rss/climatempo-brasil.xml");
copy("http://www.climatempo.com.br/rss/regioes.xml","rss/climatempo-regioes.xml");
copy("http://www.climatempo.com.br/rss/capitais.xml","rss/climatempo-capitais.xml");

//SET ARRAY QUE GUARDARÁ E PASSARÁ AS NEWS VIA json_encode
$feeds = array();


//FUNÇÃO PEGA RSS DA GLOBO NEWS
function getGloboNews($feeds, $needle){
    $rss = new DOMDocument();
    $rss->load('rss/globo-news.xml');
    $globo = array();

    foreach ($rss->getElementsByTagName('item') as $node)
    {

        if (substr_count_array( $node->getElementsByTagName('title')->item(0)->nodeValue, $needle ) > 0) {
            $item = array (
                'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
                'date' => date('d/m/Y H:i', strtotime($node->getElementsByTagName('pubDate')->item(0)->nodeValue))
            );
            array_push($globo, $item);
        }
    }
    array_push($feeds, $globo);
    getGloboEducation($feeds, $needle);
}

//FUNÇÃO PEGA RSS DA GLOBO EDUCACAO
function getGloboEducation($feeds, $needle){
    $rss = new DOMDocument();
    $rss->load('rss/globo-educacao.xml');
    $globo = array();

    foreach ($rss->getElementsByTagName('item') as $node)
    {

        if (substr_count_array( $node->getElementsByTagName('title')->item(0)->nodeValue, $needle ) > 0) {
            $item = array (
                'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
                'date' => date('d/m/Y H:i', strtotime($node->getElementsByTagName('pubDate')->item(0)->nodeValue))
            );
            array_push($globo, $item);
        }
    }
    array_push($feeds, $globo);
    getGoogleNews($feeds, $needle);
}


//FUNÇÃO PEGA RSS DA GOOGLE
function getGoogleNews($feeds, $needle){
    $rss = new DOMDocument();
    $rss->load('rss/google-news.xml');
    $google = array();

    foreach ($rss->getElementsByTagName('item') as $node)
    {

        if (substr_count_array( $node->getElementsByTagName('title')->item(0)->nodeValue, $needle ) > 0) {
            $item = array (
                'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
                'date' => date('d/m/Y H:i', strtotime($node->getElementsByTagName('pubDate')->item(0)->nodeValue))
            );
            array_push($google, $item);
        }
    }
    array_push($feeds, $google);
    getClimaTempoDestaque($feeds, $needle);
}

//FUNÇÃO PEGA RSS DO CLIMA TEMPO - DESTAQUE
function getClimaTempoDestaque($feeds, $needle){
    $rss = new DOMDocument();
    $rss->load('rss/climatempo-destaque.xml');
    $climatempo = array();

    foreach ($rss->getElementsByTagName('item') as $node)
    {
        $item = array (
                'title' => trim($node->getElementsByTagName('title')->item(0)->nodeValue),
                'date' => trim(date('d/m/Y H:i', strtotime($node->getElementsByTagName('pubDate')->item(0)->nodeValue))),
                'description' => trim(str_replace("&nbsp;","",strip_tags($node->getElementsByTagName('description')->item(0)->nodeValue)))
            );
        array_push($climatempo, $item);
    }
    array_push($feeds, $climatempo);
    getClimaTempoBrasil($feeds, $needle);
}

//FUNÇÃO PEGA RSS DO CLIMA TEMPO - BRASIL
function getClimaTempoBrasil($feeds, $needle){
    $rss = new DOMDocument();
    $rss->load('rss/climatempo-brasil.xml');
    $climatempo = array();

    foreach ($rss->getElementsByTagName('item') as $node)
    {
        $item = array (
            'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
            'date' => date('d/m/Y H:i', strtotime($node->getElementsByTagName('pubDate')->item(0)->nodeValue)),
            'description' => str_replace("&nbsp;","",strip_tags($node->getElementsByTagName('description')->item(0)->nodeValue))
        );
        array_push($climatempo, $item);
    }
    array_push($feeds, $climatempo);
    getClimaTempoRegiao($feeds, $needle);
}

//FUNÇÃO PEGA RSS DO CLIMA TEMPO - REGIÕES DO BRASIL
function getClimaTempoRegiao($feeds, $needle){
    $rss = new DOMDocument();
    $rss->load('rss/climatempo-regioes.xml');
    $climatempo = array();

    foreach ($rss->getElementsByTagName('item') as $node)
    {
        $item = array (
            'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
            'date' => date('d/m/Y H:i', strtotime($node->getElementsByTagName('pubDate')->item(0)->nodeValue)),
            'description' => str_replace("&nbsp;","",strip_tags($node->getElementsByTagName('description')->item(0)->nodeValue))
        );
        array_push($climatempo, $item);
    }
    array_push($feeds, $climatempo);
    getClimaTempoCapitais($feeds, $needle);
}

//FUNÇÃO PEGA RSS DO CLIMA TEMPO - REGIÕES DO BRASIL
function getClimaTempoCapitais($feeds, $needle){
    $rss = new DOMDocument();
    $rss->load('rss/climatempo-capitais.xml');
    $climatempoa = array();
    $climatempob = array();

    $x = 0;

    foreach ($rss->getElementsByTagName('item') as $node)
    {
        if($x < 13){
            $item = array (
                'title' => strtoupper(str_replace(" - Previsão do Tempo","",$node->getElementsByTagName('title')->item(0)->nodeValue)),
                'description' => str_replace(array("<![CDATA[  ","]]>","<BR />"),"",strip_tags($node->getElementsByTagName('description')->item(0)->nodeValue))
            );
            array_push($climatempoa, $item);
        } else {
            $item = array (
                'title' => strtoupper(str_replace(" - Previsão do Tempo","",$node->getElementsByTagName('title')->item(0)->nodeValue)),
                'description' => str_replace(array("<![CDATA[  ","]]>","<BR />"),"",strip_tags($node->getElementsByTagName('description')->item(0)->nodeValue))
            );
            array_push($climatempob, $item);
        }


        $x++;
    }
    array_push($feeds, $climatempoa);
    array_push($feeds, $climatempob);
    //######COLOCAR NA ULTIMA FUNCAO
    echo json_encode($feeds);
}


//inicia a corrente do carregamento dos RSS
getGloboNews($feeds, $needle);