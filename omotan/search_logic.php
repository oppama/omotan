<?php

// セッション制御
session_start();
if (!empty($_SESSION["userid"]) && !empty($_POST["search_check"]) && !empty($_POST["search_word"])) {

//パス情報読み込み
    require 'secret/secret.php';

    // データベース「testdb」に接続(持続的な接続も確立しておく)
    try {
        $dbh = new PDO('mysql:host=localhost;dbname=omotan', $user
                , $password, array(PDO::ATTR_PERSISTENT => true));
    } catch (PDOException $e) {
        print "DB接続エラー!: " . $e->getMessage() . "<br/>";
        die();
    }


    //結果をセッション変数に入れるため、一度削除しておく 

    if (isset($_SESSION["search_result_user"])) {
        unset($_SESSION["search_result_user"]);
    }

    if (isset($_SESSION["search_result_tweet"])) {
        unset($_SESSION["search_result_tweet"]);
    }


//検索する件数を指定
    $s_num = 10;


    //検索条件に当てはまったユーザーを抽出

    $search_user = $dbh->prepare(
            "SELECT * FROM users"
            . ' WHERE user_name LIKE "%' . $_POST["search_word"] . '%"'
            . " and deleted_at is null"
            . " order by id "
            . " limit " . $s_num
            . ";"
    );

    $search_user->execute();

    //結果を配列に格納
    $search_result_user = Array();
    for ($i = 0; $i < $s_num; $i++) {
        unset($resule);
        $result = $search_user->fetch(PDO::FETCH_ASSOC);
        //検索結果のユーザー情報を格納
        if (isset($result["user_id"])) {
            $search_result_user[$i]["user_id"] = $result["user_id"];
            $search_result_user[$i]["user_name"] = $result["user_name"];
            $search_result_user[$i]["user_profile"] = $result["user_profile"];
        }
    }


    //結果をセッション変数に格納
    $_SESSION["search_result_user"] = $search_result_user;




    //検索条件に当てはまったtweetを抽出

    $search_tweet = $dbh->prepare(
            "SELECT * FROM tweets"
            . ' WHERE tweet LIKE "%' . $_POST["search_word"] . '%"'
            . " and deleted_at is null"
            . " order by tweet_id "
            . " limit " . $s_num
    );

    $search_tweet->execute();

    //結果を配列に格納
    $search_result_tweet = Array();
    for ($i = 0; $i < $s_num; $i++) {
        unset($resule);
        $result = $search_tweet->fetch(PDO::FETCH_ASSOC);
        //検索結果のユーザー情報を格納
        if (isset($result["user_id"])) {
            $search_result_tweet[$i]["user_id"] = $result["user_id"];
            $search_result_tweet[$i]["user_name"] = $result["user_name"];
            $search_result_tweet[$i]["tweet"] = $result["tweet"];
            $search_result_tweet[$i]["favorite"] = $result["favorite"];
        }
    }

    //結果をセッション変数に格納
    $_SESSION["search_result_tweet"] = $search_result_tweet;


//    header("Location: top.php");
    header("Location: test.php");
} else {
    header("Location: top.php");
}
?>
