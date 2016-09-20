<?php

// セッション制御
session_start();
if (!empty($_SESSION["userid"]) && !empty($_POST["add_check"])) {
    $tweet = $_POST["tweet"];


//パス情報読み込み
    require 'secret/secret.php';

    // データベース「testdb」に接続(持続的な接続も確立しておく)
    try {
        $dbh = new PDO('mysql:host=localhost;dbname=omotan', $user
                , $password, array(PDO::ATTR_PERSISTENT => true));
//     print "接続成功!"."</br>";    
    } catch (PDOException $e) {
        print "DB接続エラー!: " . $e->getMessage() . "<br/>";
        die();
    }
    
//



    header("Location: omotan_top.php");
}
?>
