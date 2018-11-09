<?php

$url = file_get_contents('https://www.google.com.br/search?q=USD+to+BRL');

$dom = new DomDocument;

// We need to validate our document before refering to the id
$dom->validateOnParse = true;


//$dom->Load($url);

$dom->loadHTML($url);

//$doc->getElementById('php-basics')->tagName

//$doc->getElementById('knowledge-currency__tgt-amount')->tagName->item(0)->nodeValue
//
//echo $dom->getElementById('knowledge-currency__tgt-amount')->tagName;

$elem = $dom->getElementById('knowledge-currency__tgt-amount');
echo $elem->nodeValue;

//$result = $dom->getElementsByTagName('span')->item(0)->nodeValue;

//echo "The element whose id is 'php-basics' is: " . $doc->getElementById('php-basics')->tagName . "\n";