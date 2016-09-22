<?php
        // セッション制御
        session_start();
        if ( !empty($_SESSION["userid"]) && !empty($_POST["add_check"]) ) {
                $tweet	= $_POST["tweet"];
                
//パス情報読み込み
require 'secret/secret.php';

    // データベース「testdb」に接続(持続的な接続も確立しておく)
    try {
     $dbh = new PDO('mysql:host=localhost;dbname=omotan', $user
             , $password ,array(PDO::ATTR_PERSISTENT => true));
//     print "接続成功!"."</br>";    
     } catch (PDOException $e) {
     print "DB接続エラー!: " . $e->getMessage() . "<br/>";
     die();
      }


// SQLの追加クエリを作成する      
            try {
                date_default_timezone_set('Asia/Tokyo');
          $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $dbh->beginTransaction();
         $dbh->exec(
"INSERT INTO tweets VALUES ("
                  . "(select id from(select ifnull(max(tweet_id),0)+1 as id from tweets) as tweet)" . ", "
                  .$_SESSION["userid"] . ",'"
                 .$_SESSION["username"] . "','"
                  .$tweet. "',"
                  . "0,"
                  ."current_time, "
                  ."current_time, null);"
                  );

         $dbh->commit();

      } catch (Exception $e) {
          $dbh->rollBack();
      }
header("Location: top.php");
      
      
      
      }

?>
