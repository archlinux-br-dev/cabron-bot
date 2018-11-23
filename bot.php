<?php

// Setar o WebHook: https://api.telegram.org/bot[TOKEN]/setWebhook?url=https://site.com/pasta/script.php

// Comente esta linha...
require_once "config.php";
// Descomente as sequintes linhas e insira seu token.
//define('CABRON_URL', 'https://site.com/pasta/');
//define('BOT_TOKEN', 'TOKEN');
//define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

$hoje = date('Y-m-d');
$json_feriados = file_get_contents('json/holidays.json');
$array_feriados = json_decode($json_feriados);

function top() {
  try {
    $file_db = new PDO('sqlite:rank.sqlite3');
    $file_db->setAttribute(PDO::ATTR_ERRMODE,
                            PDO::ERRMODE_EXCEPTION);

    $file_db->exec("CREATE TABLE IF NOT EXISTS rank (
                    id INTEGER PRIMARY KEY,
                    votos INTEGER,
                    nick TEXT,
                    motivo TEXT)");

    $result = $file_db->query('SELECT * FROM rank ORDER BY votos DESC;');

    foreach($result as $row) {
      $saida .= "ID: " . $row['id'] . "\n";
      $saida .= "Votos: " . $row['votos'] . "\n";
      $saida .= "Nick: @" . $row['nick'] . "\n";
      $saida .= "Motivo: " . $row['motivo'] . "\n";
      $saida .= "\n";
    }

    return $saida;

    $file_db = null;
  } catch(PDOException $e) {
    echo $e->getMessage();
  }
}

function votar($usuario,$motivo) {
  try {
    $file_db = new PDO('sqlite:rank.sqlite3');
    $file_db->setAttribute(PDO::ATTR_ERRMODE,
                            PDO::ERRMODE_EXCEPTION);

    $file_db->exec("CREATE TABLE IF NOT EXISTS rank (
                    id INTEGER PRIMARY KEY,
                    votos INTEGER,
                    nick TEXT,
                    motivo TEXT)");

    $insert = "INSERT OR REPLACE INTO rank (votos, nick, motivo)
    VALUES (:votos, (SELECT nick FROM rank WHERE nick = $usuario), :motivo)";

    $stmt = $file_db->prepare($insert);

    $stmt->bindParam(':nick', $usuario);
    $stmt->bindParam(':motivo', $motivo);

    $stmt->execute();
    $file_db = null;
  }
  catch(PDOException $e) {
    echo $e->getMessage();
  }

}

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

function pegaAdmins($chat_id) {
  $adm = apiRequestJson("getChatAdministrators", ["chat_id" => $chat_id]);
  $adm = json_decode($adm);

  foreach ($adm as $chave => $valor) {
      //$l .= $adm[$chave]['user']['username'] . "\n\n";
      $l .= $valor . "\n\n";
  }

  //return $l;
  return var_dump($adm);
}

function cotacoes2() {
  //if(!$fp=fopen("https://www.infomoney.com.br/mercados/cambio" , "r" )) {
  if(!$fp=fopen("https://economia.uol.com.br/cotacoes/" , "r" )) {
      return "Erro ao abrir a página de cotação" ;
      exit;
  } else {
    while(!feof($fp)) {
        $conteudo .= fgets($fp,1024);
    }
    fclose($fp);

    //$valorCompraHTML = explode('class="numbers">', $conteudo);
    $valorCompraHTML = explode('class="pg-color4">', $conteudo);
    $valorCompra = trim(strip_tags($valorCompraHTML[1]));

    return $valorCompra;
  }
}


function cotacoes($moeda = 'BRL') {
  $XMLContent=file("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
  foreach($XMLContent as $line){
    if(preg_match("/currency='([[:alpha:]]+)'/",$line,$currencyCode)){
      if(preg_match("/rate='([[:graph:]]+)'/",$line,$rate)){
        if ($currencyCode[1] == $moeda) {
          return $rate[1];
        }
      }
    }
  }
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
      'cabron' => array("Em que posso ser útil? " . emoji('rindo'),"Falou comigo? " . emoji('feliz'),"Pois não..","Tô afim de falar não...","Opa!","Ao seu dispor!","Lock & Load!","hmmmmm...","Não sei o que dizer..."),
      'serjao' => array(
        "https://youtu.be/vj51ekCZtB4",
        "https://pbs.twimg.com/media/DMRvB3FWAAANKnv.jpg",
        CABRON_URL . "img/serjao.jpg",
        "https://www.youtube.com/watch?v=AcekWw-swgo"
      ),
      'js' => array(
        CABRON_URL . "vid/fogo.mp4"
      )
  );

  $r = $r1[$resp][array_rand($r1[$resp])];
  return $r;
}

function sons($som) {
  $ss = array(
      'rock' => array(
        CABRON_URL . "snd/rock.mp3",
        CABRON_URL . "snd/unforgiven.mp3"
      ),
      'love' => array(
        CABRON_URL . "snd/whisper.mp3"
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

function aur($consulta) {
  $url = 'https://aur.archlinux.org/packages/' . urlencode($consulta);
  $pagina = @file_get_contents('https://aur.archlinux.org/packages/' . urlencode($consulta));
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
  } else if ($data == 'arch') {
    requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
    requisicao("sendMessage", array('chat_id' => $chat_id, 'disable_web_page_preview' => true, "text" => 'Tutorial de instalação do Arch Linux: http://bit.ly/instalacao-arch'));
  } else if ($data == 'archuefi') {
    requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
    requisicao("sendMessage", array('chat_id' => $chat_id, 'disable_web_page_preview' => true, "text" => 'Tutorial de instalação do Arch Linux + UEFI: http://bit.ly/arch-uefi'));
  } else if ($data == 'archlvm') {
    requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
    requisicao("sendMessage", array('chat_id' => $chat_id, 'disable_web_page_preview' => true, "text" => 'Tutorial de instalação do Arch Linux + LVM: http://bit.ly/arch-lvm'));
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
  if (isset($message['reply_to_message']['from']['first_name'])) {
	$usuario = $message['reply_to_message']['from']['first_name'];
  } else {
	$usuario = '';
  }
  $quem = "@" . $message['from']['username'];
  $usuario_alvo = $message['reply_to_message']['from']['username'];

  if (isset($message['text'])) {
    $text = $message['text'];
    $comm = substr(strstr($text," "), 1);

    if (strpos($text, "/cabron") === 0) {

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

    } else if (strpos($text, "/arch") === 0) {

      apiRequestJson("sendMessage", array(
        'chat_id' => $chat_id, "text" => 'Informações sobre a instalação do Arch Linux', 'reply_markup' => array(
          'inline_keyboard' => array(
            array(
              array('text' => 'Instalação', 'callback_data' => 'arch'),
              array('text' => 'Instalação UEFI', 'callback_data' => 'archuefi'),
              array('text' => 'Instalação LVM', 'callback_data' => 'archlvm')
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
    } else if (strpos($text, "+1") === 0) {

      if (str_word_count($text) > 2) {
        if (isset($usuario_alvo) && $usuario_alvo != '') {
          votar($usuario,$motivo);
          requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
          requisicao("sendMessage", array('chat_id' => $chat_id, "text" => top()));
        } else {
          requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
          requisicao("sendMessage", array('chat_id' => $chat_id, "text" => 'Usuário não encontrado.'));
        }
      } else {
        requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
        requisicao("sendMessage", array('chat_id' => $chat_id, "text" => 'Número errado de parâmetros.'));
      }

      requisicao("sendMessage", array('chat_id' => $chat_id, "text" => top()));
    } else if (strpos($text, "/admins") === 0) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "text" => pegaAdmins($chat_id)));

    } else if (strpos(strtolower($text),"cotação dólar") !== false || strpos(strtolower($text),"cotação dolar") !== false || strpos(strtolower($text),"cotação do dolar") !== false || strpos(strtolower($text),"cotacao dolar") !== false || strpos(strtolower($text),"cotacao do dolar") !== false) {
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

    } else if (substr(strtolower($text), 0, 5) === "!aur " || substr(strtolower($text), 0, 4) === "aur ") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "text" => aur($comm)));


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

    } else if (strpos(strtolower($text),"!archlinux") !== false || strpos(strtolower($text),"como instalar o arch") !== false  || strpos(strtolower($text),"como instalar arch") !== false) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, 'disable_web_page_preview' => true, "text" => 'Tutorial de instalação do Arch Linux: http://bit.ly/instalacao-arch'));

    } else if (strpos($text,"Cabron") !== false) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "text" => respostas('cabron')));

    } else if (strpos(strtolower($text),"vou dormir") !== false) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendVideo", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "video" => CABRON_URL . 'vid/dormir.mp4'));

    } else if (strpos(strtolower($text),"aí sim heim") !== false) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendVideo", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "video" => CABRON_URL . 'vid/olha.mp4'));

    } else if ($text === "JavaScript") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendVideo", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "video" => CABRON_URL . 'vid/fogo.mp4'));

    } else if (strtolower($text) === "lol") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendSticker", array('chat_id' => $chat_id, "sticker" => 'CAADAgADyikAAktqAwABQX0_7H5oJSoC'));

    } else if (strtolower($text) === "!mp" || strtolower($text) === "\xF0\x9F\x98\x92") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendSticker", array('chat_id' => $chat_id, "sticker" => 'CAADAQADYAgAAkJRYwGaKd1yBN3AhwI'));

    } else if (strtolower($text) === "grupo parado") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendAudio", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "audio" => CABRON_URL . 'snd/entalado.mp3'));

    } else if (strtolower($text) === "\xF0\x9F\x90\xB6") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendAudio", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "audio" => CABRON_URL . 'snd/toma.mp3'));

    } else if (strtolower($text) === "!krl") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendAudio", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "audio" => CABRON_URL . 'snd/caraio.mp3'));

    } else if (strtolower($text) === "cri cri") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendAudio", array('chat_id' => $chat_id, "audio" => CABRON_URL . 'snd/cricri.mp3'));

    } else if ($text === "!os") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendAudio", array('chat_id' => $chat_id, "audio" => CABRON_URL . 'snd/oldspice.mp3'));

    } else if (strpos(strtolower($text),"brincadeira sadia") !== false) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendAudio", array('chat_id' => $chat_id, "audio" => CABRON_URL . 'snd/oldspice.mp3'));

    } else if ($text === "!osf") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendAudio", array('chat_id' => $chat_id, "audio" => CABRON_URL . 'snd/oldspice-full.mp3'));

    } else if ($text == "!serjao") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "text" => respostas('serjao')));

    } else if ($text === "que num vomitou") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "text" => 'A é! Peraí...'));
      requisicao("sendVideo", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "video" => CABRON_URL . 'vid/nojo.mp4'));

    // } else if (strpos($text, 'BOT') !== false) {
    //   $resposta = $respostas['bot'][array_rand($respostas['bot'])];
    //   requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
    //   requisicao("sendMessage", array('chat_id' => $chat_id, "text" => respostas('bot')));

    } else if (strpos(strtolower($text),"\xF0\x9F\x98\x88") !== false || strpos(strtolower($text),"\xF0\x9F\xA4\x98") !== false) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendAudio", array('chat_id' => $chat_id, "audio" => sons('rock')));

    } else if (strpos($text, "\xE2\x9D\xA4") !== false || strpos($text, '!love') !== false) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendAudio", array('chat_id' => $chat_id, "audio" => sons('love')));

    } else if ($text === "O @Galdino0800 é o que?") {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "text" => 'Hummmmmmmm, danada! ;)'));

    } else if (strpos(strtolower($text),"tosvaldo") !== false) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "text" => 'https://www.youtube.com/watch?v=LHxFGPrlJBQ'));

    } else if (strpos(strtolower($text),"partiu pubg") !== false) {
      requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
      requisicao("sendPhoto", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "photo" => CABRON_URL . 'img/pubg.jpg'));

    } else {
      if ($usuario == "Cabron") {
        requisicao("sendChatAction", array('chat_id' => $chat_id, 'action' => 'typing'));
        requisicao("sendMessage", array('chat_id' => $chat_id, "text" => respostas('cabron')));
      }
    }
  }
}

define('WEBHOOK_URL', 'https://lucasbrum.net/cabron/bot.php');

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
  exit;
} else if (isset($update["message"])) {
  processaMensagem($update["message"]);
} else if (isset($update["callback_query"])) {
  processaCallbackQuery($update["callback_query"]);
}
