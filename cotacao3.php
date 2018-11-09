<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

//$url = file_get_contents('https://www.google.com.br/search?q=USD+to+BRL');
$url = file_get_contents('https://duckduckgo.com/?q=USD+in+BRL&t=h_&ia=currency');

$dom = new DomDocument;

// We need to validate our document before refering to the id
$dom->validateOnParse = true;


//$dom->Load($url);

$dom->loadHTML($url);

//$doc->getElementById('php-basics')->tagName

//$doc->getElementById('knowledge-currency__tgt-amount')->tagName->item(0)->nodeValue
//
//echo $dom->getElementById('knowledge-currency__tgt-amount')->tagName;

//$elem = $dom->getElementById('knowledge-currency__tgt-amount');
//
$elem = $dom->getElementById('zci--currency-amount-right');
echo $elem[0]->nodeValue;

//$result = $dom->getElementsByTagName('span')->item(0)->nodeValue;

//echo "The element whose id is 'php-basics' is: " . $doc->getElementById('php-basics')->tagName . "\n";