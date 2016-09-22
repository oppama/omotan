<?php

// セッション制御
session_start();

//パス情報読み込み
require 'secret/secret.php';

// データベースに接続(持続的な接続も確立しておく)
try {
    $dbh = new PDO('mysql:host=localhost;dbname=omotan', $user
            , $password, array(PDO::ATTR_PERSISTENT => true));
} catch (PDOException $e) {
    print "DB接続エラー!: " . $e->getMessage() . "<br/>";
    die();
}




//削除時処理
if (!empty($_SESSION["userid"]) && !empty($_POST["tweetid"]) && !empty($_POST["tweet_edit"] == "del")) {
    try {
        date_default_timezone_set('Asia/Tokyo');
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->beginTransaction();
        $dbh->exec(
                "delete from tweets where "
                . "tweet_id = " . $_POST["tweetid"] . " and "
                . "user_id =" . $_SESSION["userid"] . ";"
        );
        $dbh->commit();

        //検索後の画面から削除した際の対応
        unset($_SESSION["search_result_tweet"][$_POST["focus_id"]-1]);        
        } catch (Exception $e) {
        $dbh->rollBack();
    }


    $uri = $_SERVER['HTTP_REFERER'];
//    header("Location: " . $uri . '#' . $_POST["focus"], true, 303);
    header("Location: " . $uri , true, 303);    
    
}


//いいね時処理
if (!empty($_SESSION["userid"]) && !empty($_POST["tweetid"]) && !empty($_POST["tweet_edit"] == "iine")) {

    date_default_timezone_set('Asia/Tokyo');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $iine = $dbh->prepare(
            "SELECT id,deleted_at FROM favorite WHERE user_id = " . $_SESSION["userid"]
            . " and favorite_tweet_id = " . $_POST["tweetid"]
            . ";"
    );
    $iine->execute();
    $result = $iine->fetch(PDO::FETCH_ASSOC);


    //まだ一度もいいねしたことがない場合

    if (is_null($result["id"])) {
        //実行
        try {
            date_default_timezone_set('Asia/Tokyo');
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->beginTransaction();
            //tweetテーブルのいいねの更新
            $dbh->exec(
                    "update tweets "
                    . "set favorite = favorite + 1 "
                    . "where tweet_id =" . $_POST["tweetid"] . ";"
            );


            //favoriteテーブルへのinsert
            $dbh->exec(
                    "insert favorite values("
                    . "(select id from(select ifnull(max(id),0)+1 as id from favorite) as favo)" . ", "
                    . $_SESSION["userid"] . ", "
                    . $_POST["tweetid"] . ", "
                    . " current_time" . ", "
                    . " current_time" . ", "
                    . " null )"
                    . ";"
            );

            $dbh->commit();
        } catch (Exception $e) {
            $dbh->rollBack();
        }

        $uri = $_SERVER['HTTP_REFERER'];
        header("Location: " . $uri , true, 303);
    }


    //いいね削除時
    if (is_null($result["deleted_at"]) && !is_null($result["id"])) {
        //実行
        try {
            date_default_timezone_set('Asia/Tokyo');
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->beginTransaction();
            //tweetテーブルのいいねの更新
            $dbh->exec(
                    "update tweets "
                    . "set favorite = favorite - 1 "
                    . "where tweet_id =" . $_POST["tweetid"] . ";"
            );
            //favoriteテーブルへのinsert            

            $dbh->exec(
                    "update favorite "
                    . "set deleted_at = current_time "
                    . "where favorite_tweet_id =" . $_POST["tweetid"]
                    . " and user_id =" . $_SESSION["userid"]
                    . ";"
            );

            $dbh->commit();
        } catch (Exception $e) {
            $dbh->rollBack();
        }


        $uri = $_SERVER['HTTP_REFERER'];
        header("Location: " . $uri . '#' . $_POST["focus"], true, 303);
    }


    //過去にいいねしたことがある時の対応
    elseif (!is_null($result["deleted_at"]) && !is_null($result["id"])) {
        //実行
        try {
            date_default_timezone_set('Asia/Tokyo');
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->beginTransaction();
            //tweetテーブルのいいねの更新
            $dbh->exec(
                    "update tweets "
                    . "set favorite = favorite + 1 "
                    . "where tweet_id =" . $_POST["tweetid"] . ";"
            );
            //favoriteテーブルへのinsert            

            $dbh->exec(
                    "update favorite "
                    . "set deleted_at = null "
                    . "where favorite_tweet_id =" . $_POST["tweetid"]
                    . " and user_id =" . $_SESSION["userid"]
                    . ";"
            );

            $dbh->commit();
        } catch (Exception $e) {
            $dbh->rollBack();
        }

        $uri = $_SERVER['HTTP_REFERER'];
        header("Location: " . $uri , true, 303);
    } else {
        echo '処理漏れ' . "</br>";
        echo $result["id"] . "</br>";
        echo $_POST["tweetid"] . "</br>";
        echo is_null($result["deleted_at"]) . "</br>";
    }
}
?>
