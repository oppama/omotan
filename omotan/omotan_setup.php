<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>テーブルを準備する</title>
    </head>

    <body>
        ボタンを押すとテーブルを準備します。<br>
    既存のデータがある場合、データは削除されるので注意して下さい。<br><br>

    <form method="POST" action="omotan_setup.php">
        <input type="submit" name="setup" value="セットアップ">
    </form>

    <?php
    // セットアップボタンが押されたかどうかのチェック
    if (empty($_POST["setup"]))
        exit;

    // 「omotan」データベースファイルをオープン
//パス情報読み込み
require 'secret/secret.php';

    $db = new PDO('mysql:host=localhost;dbname=omotan', $user
            , $password, array(PDO::ATTR_PERSISTENT => true));


    
      // 既存のテーブルを削除する

      $db->query("DROP TABLE tweets");

      // 「tweet」テーブル作成用SQLを準備
      $sql  = "CREATE TABLE tweets (".
      "tweet_id integer, ".
      "user_id integer, ".
      "user_name text, ".
      "tweet text, ".
      "favorite integer, ".
      "created_at timestamp not null default current_timestamp, ".
      "updated_at timestamp not null default current_timestamp on update current_timestamp, ".
      "deleted_at timestamp NULL, ".
      "primary key (tweet_id) )";

      // 「tweets」テーブルを作成

      $db->query($sql);


      // 既存のusersテーブルを削除する
      $db->query("DROP TABLE users");


      // 「users」テーブル作成用SQLを準備
      $sql  = "CREATE TABLE users (".
      "id integer, ".
      "user_id integer, ".
      "user_name text, ".
      "user_password text, ".
      "user_question text, ".
      "user_answer text, ".
      "user_profile text, ".              
      "user_lastlogin timestamp, ".
      "created_at timestamp not null default current_timestamp, ".
      "updated_at timestamp not null default current_timestamp on update current_timestamp, ".
      "deleted_at timestamp NULL, ".
      "primary key (user_id) )";

      // 「users」テーブルを作成
      $db->query($sql);

      // 既存のfriendsテーブルを削除する
      $db->query("DROP TABLE friends");


      // 「friends」テーブル作成用SQLを準備
      $sql  = "CREATE TABLE friends(".
      "id integer, ".
      "user_id integer, ".
      "friend_user_id integer, ".
      "created_at timestamp not null default current_timestamp, ".
      "updated_at timestamp not null default current_timestamp on update current_timestamp, ".
      "deleted_at timestamp NULL, ".
      "primary key (id) )";


      // テーブルを作成
      $db->query($sql);
     


    // 既存のfavoriteテーブルを削除する
    $db->query("DROP TABLE favorite");


    // 「favorite」テーブル作成用SQLを準備
    $sql = "CREATE TABLE favorite(" .
            "id integer, " .
            "user_id integer, " .
            "favorite_tweet_id integer, " .
            "created_at timestamp not null default current_timestamp, " .
            "updated_at timestamp not null default current_timestamp on update current_timestamp, " .
            "deleted_at timestamp NULL, " .
            "primary key (id) )";

// テーブルを作成
    $db->query($sql);
    ?>

    テーブルを準備しました。

</body>
</html>
