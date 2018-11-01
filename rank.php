<?php

//irei refatorar com algum framewok mvc
ini_set('display_errors', 'On');
error_reporting(E_ALL);

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

    $saida = '';

    foreach($result as $row) {
      $saida .= "ID: " . $row['id'] . "\n<br />";
      $saida .= "Votos: " . $row['votos'] . "\n<br />";
      $saida .= "Nick: @" . $row['nick'] . "\n<br />";
      $saida .= "Motivo: " . $row['motivo'] . "\n<br />";
      $saida .= "\n<br /><br />";
    }

    //return $saida;
    echo $saida;

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

    //$insert = "INSERT OR REPLACE INTO rank (votos, nick, motivo)
    //VALUES (:votos, (SELECT nick FROM rank WHERE nick = :nick), :motivo)";

    $insert = "INSERT OR REPLACE INTO rank (id,votos,nick,motivo)
    VALUES (:id, COALESCE((SELECT votos FROM rank WHERE nick = :nick)+1, 1), :nick, :motivo)";
    
    // VALUES (COALESCE((SELECT votos + 1 FROM rank WHERE nick = :nick), 1), :nick, :motivo);";

//((select ID from Book where Name = "SearchName"), "SearchName", ...);
  
  // VALUES (COALESCE((SELECT votos + 1 FROM rank WHERE nick = :nick), 1), :nick, :motivo);";

//          );

//    $result = $db->prepare('SELECT * FROM data WHERE NodeID = ?');

    //$stmt = $file_db->prepare($insert);
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

votar('Lucas2','Seiiii lá');
echo top();
