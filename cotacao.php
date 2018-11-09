<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

function converterMoeda(string $de, string $para){

    $parametros = [
        'q' => 'select Rate from yahoo.finance.xchange where pair = "'.$de.$para.'"',
        'env' => 'store://datatables.org/alltableswithkeys',
        'format' => 'json'
    ];

    $parametros = http_build_query($parametros, '', '&', PHP_QUERY_RFC3986);

    $ch = curl_init('http://query.yahooapis.com/v1/public/yql?'.$parametros);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FAILONERROR => 1
    ]);

    if($resultado = curl_exec($ch)){

        $resultado = json_decode($resultado, true);

        return $resultado['query']['results']['rate']['Rate'];

    }

    return false;

}



$resultado = converterMoeda('USD', 'BRL');
echo $resultado;