<?php
  //Read eurofxref-daily.xml file in memory
  //For this command you will need the config
  //option allow_url_fopen=On (default)
  $XMLContent=file("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
  foreach($XMLContent as $line){
    if(preg_match("/currency='([[:alpha:]]+)'/",$line,$currencyCode)){
      if(preg_match("/rate='([[:graph:]]+)'/",$line,$rate)){
        if ($currencyCode[1] == "BRL") {
          echo $rate[1];
        }
      }
    }
  }
?>