<?php

header('Content-type: text/html; charset=UTF-8');
$ParametroPesquisa = "getUltimoValorXML";
$Moeda = "1";
// vamos evitar que o arquivo WSDL seja colocado no cache
ini_set("soap.wsdl_cache_enabled", "0");
$WsSOAP = new SoapClient("https://www3.bcb.gov.br/sgspub/JSP/sgsgeral/FachadaWSSGS.wsdl");
$DadosParaPesquisa = array("in0" => "$Moeda"); try {
$ResultadoPesquisaWS = $WsSOAP -> $ParametroPesquisa($DadosParaPesquisa); 
 
if (<a href="http://charlescorrea.com.br/blog/tag/isset">isset</a>($ResultadoPesquisaWS)) { $CotacaoMoedaWS = simplexml_load_string($ResultadoPesquisaWS);
//print_r($CotacaoMoedaWS); $NomeMoeda = $CotacaoMoedaWS->SERIE->NOME;
$ValorMoeda = $CotacaoMoedaWS->SERIE->VALOR;
$DiaCotacaoMoeda = $CotacaoMoedaWS->SERIE->DATA->DIA;
$MesCotacaoMoeda = $CotacaoMoedaWS->SERIE->DATA->MES;
$AnoCotacaoMoeda = $CotacaoMoedaWS->SERIE->DATA->ANO;
echo " $NomeMoeda
<hr />
Valor: $1 equivale à R$ $ValorMoeda <br /> Dia da Cotação: $DiaCotacaoMoeda/$MesCotacaoMoeda/$AnoCotacaoMoeda ";
} else {
exit('Falha ao abrir XML do BCB.');
}
} catch (Exception $Exception) {
echo "ERRO AO REALIZAR A CAPTURA DE DADOS DO WEBSERVICE: " . $Exception -> getMessage();
}