<?php
$dom = new DomDocument;

// We need to validate our document before refering to the id
//$doc->validateOnParse = true;
//$doc->Load('book.xml');

$dom->loadHTML('https://www.google.com.br/search?q=USD+to+BRL');

//$doc->getElementById('php-basics')->tagName

//$doc->getElementById('knowledge-currency__tgt-amount')->tagName->item(0)->nodeValue
//
echo $dom->getElementById('knowledge-currency__tgt-amount')->item(0)->nodeValue;

//$result = $dom->getElementsByTagName('span')->item(0)->nodeValue;

//echo "The element whose id is 'php-basics' is: " . $doc->getElementById('php-basics')->tagName . "\n";