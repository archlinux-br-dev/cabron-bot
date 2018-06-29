<?php

require_once "config.php";

$hoje = date('Y-m-d');
$json_feriados = file_get_contents('json/feriados.json');
$array_feriados = json_decode($json_feriados);
$host = "https://cabron-bot.herokuapp.com";

function emoji($emoji) {
  switch ($emoji) {
    case 'rindo': return "\xF0\x9F\x98\x81"; break;
    case 'feliz': return "\xF0\x9F\x98\x83"; break;
    case 'chorando': return "\xF0\x9F\x98\xA2"; break;
    case 'triste': return "\xF0\x9F\x98\x94"; break;
    case 'teste': return "\u{1F600}"; break;
  }
}

function feriados($array_feriados, $hoje) {
      //foreach($array_feriados as $day) {
      foreach($array_feriados as $key => $value){
          $interval[] = abs(strtotime($hoje) - strtotime(key[$array_feriados[$key]]));
      }
      asort($interval);
      $closest = key($interval);
      return $array_feriados[$closest];
}

//feriados($array_feriados, "2013-02-18 05:14:55");

function pegaAdmins($chat_id) {
  $adm = apiRequestJson("getChatAdministrators", ["chat_id" => $chat_id]);
  $adm = json_decode($adm);

  foreach ($adm as $chave => $valor) {
      $l .= $valor['user']['username'] . "\n\n";
  } 

  return $l; 
}

function cotacoes() {
  if(!$fp=fopen("https://www.infomoney.com.br/mercados/cambio" , "r" )) {
      return "Erro ao abrir a página de cotação" ;
      exit;
  }

  while(!feof($fp)) { 
      $conteudo .= fgets($fp,1024);
  }
  fclose($fp);

  $valorCompraHTML = explode('class="numbers">', $conteudo); 
  $valorCompra = trim(strip_tags($valorCompraHTML[5]));

  return $valorCompra;
}

function palavras($string) {
    $string = preg_replace('/\s+/', ' ', trim($string));
    $words = explode(" ", $string);
    return count($words);
}

function respostas($resp) {
  $r1 = array(
      'ola' => array('E aí beleza?','Bão!?'),
      'bot' => array('Que bot?','Bot!? Onde?','Sou Bot não pô!'),
      'cabron' => array("Em que posso ser útil? " . emoji('rindo'),"Falou comigo? " . emoji('feliz'),"Pois não..","Ao seu dispor!","Locked & Loaded!","hmmmmm...","Não sei o que dizer..."),
      'serjao' => array(
        "https://youtu.be/vj51ekCZtB4",
        "https://pbs.twimg.com/media/DMRvB3FWAAANKnv.jpg",
        "https://cabron-bot.herokuapp.com/img/serjao.jpg",
        "https://www.youtube.com/watch?v=AcekWw-swgo"
      )
  );

  $r = $r1[$resp][array_rand($r1[$resp])];
  return $r;
}

function sons($som) {
  $ss = array(
      'rock' => array(
        "https://cabron-bot.herokuapp.com/snd/Joan Jett - I Love Rock And Roll.mp3",
        "https://cabron-bot.herokuapp.com/snd/Metallica - The Unforgiven.mp3"
      )
  );

  $s = $ss[$som][array_rand($ss[$som])];
  return $s;
}


function ddg($consulta) {

  $url = 'https://duckduckgo.com/?q=' . urlencode($consulta);
  $pagina = @file_get_contents('https://duckduckgo.com/?q=' . urlencode($consulta));
  $erro = "Desculpe, não fui capaz de encontrar esta página " . emoji('triste');

  if (!$pagina) return $erro;
  
  $matches = array();
  if (preg_match('/<title>(.*?)<\/title>/', $pagina, $matches)) {
    return trim($matches[1]) . ": " . $url;
  } else {
    return $erro;
  }

}

function man($consulta,$secao = null) {
  if (null === $secao) $secao=1;
  $url = 'https://linux.die.net/man/' . $secao . '/' . $consulta;
  $pagina = @file_get_contents('https://linux.die.net/man/' . $secao . '/' . $consulta);
  $erro = "Desculpe, não fui capaz de encontrar esta página " . emoji('triste');

  if (!$pagina) return $erro;
  
  $matches = array();
  if (preg_match('/<title>(.*?)<\/title>/', $pagina, $matches)) {
    return "*" . trim($matches[1]) . "*\n\n" . $url;
  } else {
    return $erro;
  }

}

function wikipedia($consulta) {

  $url = 'https://en.wikipedia.org/w/index.php?search=' . $consulta . '&title=Special:Search&go=Go';
  $pagina = @file_get_contents($url);
  $erro = "Desculpe, não fui capaz de encontrar esta página " . emoji('triste');

  if (!$pagina) return $erro;
    $matches = array();
    if (preg_match('/<title>(.*?)<\/title>/', $pagina, $matches)) {
      return trim($matches[1]) . ": " . $url;
    } else {
      return $erro;
    }

}

function archwiki($consulta) {

  $url = 'https://wiki.archlinux.org/index.php/' . $consulta;
  $pagina = @file_get_contents('https://wiki.archlinux.org/index.php/' . $consulta);
  $erro = "Desculpe, não fui capaz de encontrar esta página " . emoji('triste');

  if (!$pagina) return $erro;
    $matches = array();
    if (preg_match('/<title>(.*?)<\/title>/', $pagina, $matches)) {
      return trim($matches[1]) . ": " . $url;
    } else {
      return $erro;
    }

}

function pesquisaShell($comando,$nome) {
  $encontrado = false;
  $fh = fopen('txt/comandos2.txt','r');
 
  while ($line = fgets($fh)) {
    $comp = explode(' ',trim($line));
    if (preg_match("~\b$comando\b~",$comp[0])) {
      $encontrado = true;
      return $nome . " encontrei o seguinte sobre " . $comando . ":\n\n" . $line;
      break;
    }
  }

  if(!$encontrado) { //if there's a dot in our soruce text do

    rewind($fh);

    while ($line = fgets($fh)) {
      $comp = explode(' ',trim($line));
      $position = strpos($comp[0], $comando);
      if($position !== false) { //if there's a dot in our soruce text do
        $encontrado = true;
        //$offset = $position + 1; //prepare offset
        //$position2 = strpos ($line, $comando, $offset); //find second dot using offset
        //$first_two = substr($line, 0, $position2); //put two first sentences under $first_two
        //return $line;
        return $nome . " encontrei um comando parecido! Talvez seja isso:\n\n" . $line;
        break;
      }
    }
  }

  fclose($fh);

  if (!$encontrado) {
    return $nome . " infelizmente eu não encontrei nada sobre o comando: " . $comando . " " . emoji('triste');
  }
}


function executa_curl($handle) {
  $response = curl_exec($handle);

  if ($response === false) {
    $errno = curl_errno($handle);
    $error = curl_error($handle);
    error_log("Curl returned error $errno: $error\n");
    curl_close($handle);
    return false;
  }

  $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
  curl_close($handle);

  if ($http_code >= 500) {
    // do not wat to DDOS server if something goes wrong
    sleep(10);
    return false;
  } else if ($http_code != 200) {
    $response = json_decode($response, true);
    error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
    if ($http_code == 401) {
      throw new Exception('Invalid access token provided');
    }
    return false;
  } else {
    $response = json_decode($response, true);
    if (isset($response['description'])) {
      error_log("Request was successfull: {$response['description']}\n");
    }
    $response = $response['result'];
  }
  return $response;
}

function requisicao($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }

  foreach ($parameters as $key => &$val) {
    // encoding to JSON array parameters, for example reply_markup
    if (!is_numeric($val) && !is_string($val)) {
      $val = json_encode($val);
    }
  }
  $url = API_URL.$method.'?'.http_build_query($parameters);

  $handle = curl_init($url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);

  return executa_curl($handle);
}

function processaCallbackQuery($callback){
  $callback_id = $callback['id'];
  $chat_id = $callback['message']['chat']['id'];
  $data =  $callback['data'];
  if ($data == 'site') {
    requisicao("sendMessage", array('chat_id' => $chat_id, 'disable_web_page_preview' => true, "text" => 'https://archlinux-br-dev.github.io/cabron')); 
  } else if ($data == 'github') {
    requisicao("sendMessage", array('chat_id' => $chat_id, 'disable_web_page_preview' => true, "text" => 'https://github.com/archlinux-br-dev/cabron')); 
  } else if ($data == 'faq') {
    requisicao("sendMessage", array('chat_id' => $chat_id, "text" => 'FAQ')); 
  } else if ($data == 'duvidas') {
    requisicao("sendMessage", array('chat_id' => $chat_id, "text" => 'Em breve...')); 
  }

  requisicao("answerCallbackQuery", array('callback_query_id' => $callback_id));
}

function apiRequestJson($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }

  $parameters["method"] = $method;

  $handle = curl_init(API_URL);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);
  curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
  curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

  return executa_curl($handle);
}

function processaMensagem($message) {
  // process incoming message
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  $usuario = $message['reply_to_message']['from']['first_name'];
  $quem = "@" . $message['from']['username'];

  if (isset($message['text'])) {
    $text = $message['text'];
    $comm = substr(strstr($text," "), 1);

    if (strpos($text, "/info") === 0) {

      apiRequestJson("sendMessage", array(
        'chat_id' => $chat_id, "text" => 'Informações sobre o Projeto Cabron', 'reply_markup' => array(
          'inline_keyboard' => array(
            array(
              array('text' => 'Site', 'callback_data' => 'site')
            ),
            array(
              array('text' => 'GitHub', 'callback_data' => 'github'),
              array('text' => 'FAQ', 'callback_data' => 'faq'),
              array('text' => 'Dúvidas', 'callback_data' => 'duvidas')
            )
          )
        )
      )
      );

    } else if ($text === "cbr" || strpos(strtolower($text),"http injector") !== false) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendSticker", array('chat_id' => $chat_id, "sticker" => 'CAADAQADJQIAAnTnKwJe6r7nI9DetQI')); 

    } else if (strpos($text, "!feriados") === 0) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "text" => feriados($array_feriados, $hoje)));  

    } else if (strpos($text, "teste") === 0) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "text" => emoji('teste')));      

    } else if (strpos($text, "/admins") === 0) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "text" => pegaAdmins($chat_id)));      
    
    } else if (strpos(strtolower($text),"cotação dolar") !== false || strpos(strtolower($text),"cotação do dolar") !== false || strpos(strtolower($text),"cotacao dolar") !== false || strpos(strtolower($text),"cotacao do dolar") !== false) {
        requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
        requisicao("sendMessage", array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'parse_mode' => 'markdown', "text" => "A cotação do dolar hoje é *" . cotacoes() . "*"));
    
    } else if (substr(strtolower($text), 0, 5) === "!man " || substr(strtolower($text), 0, 4) === "man ") {
      $n = palavras($comm);
      if ($n == 2) {
        $manual = explode(' ', $comm);
        requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
        requisicao("sendMessage", array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'parse_mode' => 'markdown', 'disable_web_page_preview' => true, "text" => man($manual[1],$manual[0])));
      } else if ($n == 1) {
        requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
        requisicao("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, 'parse_mode' => 'markdown', 'disable_web_page_preview' => true, "text" => man($comm)));
      }
    
    } else if (substr(strtolower($text), 0, 5) === "!ddg " || substr(strtolower($text), 0, 4) === "ddg ") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "text" => ddg($comm)));
    
    } else if ($text === "rdj" || strpos(strtolower($text),"http injector") !== false) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendSticker", array('chat_id' => $chat_id, "sticker" => 'CAADAQADfAYAAkEGgg6U-GRry3w3TAI')); 

    } else if (substr(strtolower($text), 0, 5) === "!wiki") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "text" => archwiki($comm)));

    } else if (substr(strtolower($text), 0, 5) === "!wikipedia") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "text" => wikipedia($comm)));      
    
    } else if (substr(strtolower($text), 0, 6) === "!shell") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      if (isset($comm) && $comm != "") {
        requisicao("sendMessage", array('chat_id' => $chat_id, "text" => pesquisaShell($comm,$quem)));
      } else {
        requisicao("sendMessage", array('chat_id' => $chat_id, "text" => $quem . " pesquisa inválida, uso: !shell comando"));
      }
    
    } else if (strpos(strtolower($text),"!archlinux") !== false) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "text" => 'Tutorial de instalação do Arch Linux: http://telegra.ph/Instala%C3%A7%C3%A3o-Arch-Linux-03-24'));
    
    } else if (strpos($text,"Cabron") !== false) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "text" => respostas('cabron')));
    
    } else if (strpos(strtolower($text),"vou dormir") !== false) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendVideo", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "video" => 'https://cabron-bot.herokuapp.com/vid/dormir.mp4'));
    
#    } else if (strpos(strtolower($text),"morgareth") !== false || strtolower($text) === "nojo" || strpos(strtolower($text),"windows") !== false) {
#      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
#      requisicao("sendVideo", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "video" => 'https://cabron-bot.herokuapp.com/vid/nojo.mp4'));
    
    } else if (strtolower($text) === "grupo parado") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendAudio", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "audio" => 'https://cabron-bot.herokuapp.com/snd/entalado.mp3'));      
    
    } else if ($text == "!serjao") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "text" => respostas('serjao')));
    
    //} else if (strpos(strtolower($text),"olá") !== false || $text === "Oi") {
      //requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      //requisicao("sendMessage", array('chat_id' => $chat_id, "text" => respostas('ola')));
    
    } else if ($text === "que num vomitou") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "text" => 'A é! Peraí...'));
      requisicao("sendVideo", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "video" => 'https://cabron-bot.herokuapp.com/vid/nojo.mp4'));
    
    } else if (strpos($text, 'BOT') !== false) {
      $resposta = $respostas['bot'][array_rand($respostas['bot'])];
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "text" => respostas('bot')));
    
    } else if (strpos(strtolower($text),"\xF0\x9F\x98\x88") !== false || strpos(strtolower($text),"\xF0\x9F\xA4\x98") !== false) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      //requisicao("sendMessage", array('chat_id' => $chat_id, "text" => 'https://www.youtube.com/watch?v=yrjZWi49KOk'));
      requisicao("sendAudio", array('chat_id' => $chat_id, "audio" => sons('rock')));
    
    } else if ($text === "O @Galdino0800 é o que?") {      
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "text" => 'Hummmmmmmm, danada! ;)'));
    
    } else {
      if ($usuario == "Cabron") {
        requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
        requisicao("sendMessage", array('chat_id' => $chat_id, "text" => respostas('cabron')));
      }
    }
  } 
}

define('WEBHOOK_URL', 'https://cabron-bot.herokuapp.com');

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
  exit;
} else if (isset($update["message"])) {
  processaMensagem($update["message"]);
} else if (isset($update["callback_query"])) {
  processaCallbackQuery($update["callback_query"]);
}
