<?php

// Mostra erros
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('default_socket_timeout', 300);

$debug = true;
$result = null;
$error = null;
$exception = null;
$xml = null;


// Definição da localização do arquivo WSDL
$wsdl = 'https://www3.bcb.gov.br/sgspub/JSP/sgsgeral/FachadaWSSGS.wsdl';

// Soapclient PHP 5.0 ou Superior ( SOAP 1.1 e SOAP 1.2 )
$client = new Soapclient($wsdl, array('trace' => 1, 'exceptions' => true));



$xml =' <?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://publico.ws.casosdeuso.sgs.pec.bcb.gov.br" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
<SOAP-ENV:Body>
<ns1:getUltimoValorVO>
<in xsi:type="xsd:long">'. '206'.'</in>
</ns1:getUltimoValorVO>
</SOAP-ENV:Body>
</SOAP-ENV:Envelope>';
// Chamada do método SOAP
try {
    //$serie = 206;
    $result = $client->getUltimoValorVO($xml, $serie);

    if (is_object($result)) {
        if (!$result->getUltimoValorVOResult) {
            $error = $result->getUltimoValorVOError;
        }
    }

    if (is_soap_fault($result)) {
        trigger_error("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring})", E_USER_ERROR);
    }

} catch (Exception $e) {
    $exception = $e->getMessage();
}

if ($debug) {
    echo '<h2>Request Headers</h2>';
    echo '<pre>' . htmlspecialchars($client->__getLastRequestHeaders()) . '</pre>';

    echo '<h2>Response Headers</h2>';
    echo '<pre>' . htmlspecialchars($client->__getLastResponseHeaders()) . '</pre>';

    echo '<h2>Request</h2>';
    echo '<pre>' . htmlspecialchars($client->__getLastRequest()) . '</pre>';

    echo '<h2>Response</h2>';
    echo '<pre>' . htmlspecialchars($client->__getLastResponse()) . '</pre>';

    echo '<h2>Debug</h2>';
    echo '<pre>' . var_dump($result) . '</pre>';

    echo '<h2>Exception</h2>';
    echo '<pre>' . var_dump($exception) . '</pre>';

    echo '<h2>Error</h2>';
    echo '<pre>' . $error . '</pre>';
}

?>