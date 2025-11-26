<?php

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://painel-inep.local/api/categorias");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$json = curl_exec($ch);
curl_close($ch);

echo '<pre>';
$data = json_decode($json, true);
print_r($data);
die;