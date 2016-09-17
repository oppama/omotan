<?php
// セッションを開始
session_start();


//パス情報読み込み
require 'secret/secret.php';

$db = new PDO('mysql:host=localhost;dbname=omotan', $user
        , $password, array(PDO::ATTR_PERSISTENT => true));
//rowcountを取得するためにこの行を追加
$db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

$username = $_POST["username"];
$query = $db->query("SELECT * FROM users WHERE user_name = '$username'");
$record = $query->fetch(PDO::FETCH_ASSOC);

// 認証OK?
if ($_POST["mode"] == "login" && $query->rowCount() > 0 && $_POST["password"] == $record["user_password"]) {
    $_SESSION["userid"] = $record["user_id"];
    header("Location: omotan_top.php");
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>ログイン処理</title>
    </head>
    <body>
        <?php
        // ■■　ログイン処理状態
        if ($_POST["mode"] == "login") {
            // 新規登録：クエリ実行結果が0行＝既存ユーザー名と一致しない
            if ($query->rowCount() == 0) {
                echo '新規ユーザー登録<br>';
                echo '<form action="omotan_login.php" method="POST" enctype="multipart/form-data">';
                echo 'ユーザー名 <input type="text" name="username" value="' . $_POST["username"] . '"><br>';
                echo 'パスワード <input type="password" name="password"><br>';
                echo 'パスワード(確認) <input type="password" name="confirm"><br>';
                echo 'ひみつの質問 <input type="text" name="question"><br>';
                echo 'その答え <input type="text" name="answer"><br>';
                echo 'プロフィール画像 <input type="file" name="profile"><br>';
                echo '<input type="hidden" name="mode" value="register">';
                echo '<input type="submit" value="登録">';
                echo '</form>';
                echo '<br>';
                echo '<a href="javascript:history.go(-1);">戻る</a>';
            }
            // パスワードミス：クエリ実行結果のパスワードと入力されたパスワードが一致しない
            elseif ($_POST["password"] != $record["user_password"]) {
                echo 'パスワードが違います。<br>';
                echo 'ひみつの質問に答えると、パスワードを変更できます。<br>';
                echo '<form action="omotan_login.php" method="POST">';
                echo 'ひみつの質問 ' . $record["user_question"] . '<br>';
                echo 'その答え <input type="text" name="answer"><br>';
                echo '<input type="hidden" name="username" value="' . $username . '">';
                echo '<input type="hidden" name="mode" value="resetpassword">';
                echo '<input type="submit" value="送信">';
                echo '</form>';
                echo '<br>';
                echo '<a href="javascript:history.go(-1);">戻る</a>';
            }
        }
        // ■■　パスワード再設定状態
        elseif ($_POST["mode"] == "resetpassword") {
            if ($record["user_answer"] == $_POST["answer"]) {
                echo 'パスワード再設定<br>';
                echo '<form action="omotan_login.php" method="POST">';
                echo 'パスワード <input type="password" name="password"><br>';
                echo 'パスワード(確認) <input type="password" name="confirm"><br>';
                echo '<input type="hidden" name="username" value="' . $username . '">';
                echo '<input type="hidden" name="mode" value="modifypassword">';
                echo '<input type="submit" value="登録">';
                echo '</form>';
            } else {
                echo "ひみつの答えが違います";
                echo "<a href='javascript:history.go(-1);'>戻る</a>";
            }
        }
        // ■■　新規ユーザー登録状態
        elseif ($_POST["mode"] == "register") {
            if ($_POST["password"] == $_POST["confirm"]) {
                //ユーザーidの取得
                $sql = "select * from users;";
                $user_id_query = $db->query($sql);
                $user_id = $user_id_query->rowCount();


                //画像ファイルのエラーチェック&サーバーアップロード
                $upload_dir = "./profile_img/";
                try {
                    if (is_uploaded_file($_FILES['profile']['tmp_name'])) {
                        move_uploaded_file($_FILES['profile']['tmp_name'], $upload_dir . "$user_id+1" . ".jpg" );
                        print_r($_FILES['profile']['tmp_name']);
//                        print_r($_FILES['file']);
                    }
                } catch (Exception $e) {
                    echo '画像エラー:', $e->getMessage() . PHP_EOL;
                }
               

        //登録クエリの実行

        $sql = "INSERT INTO users (id,user_id,user_name, user_password, user_question, user_answer,user_profile) VALUES ("
        . "$user_id+1"
        . ',' . "$user_id+1"
        . ',"' . $_POST["username"] . '"'
        . ',"' . $_POST["password"] . '"'
        . ',"' . $_POST["question"] . '"'
        . ',"' . $_POST["answer"] . '"' 
        . ',"' . "$user_id+1" . 'jpg"'                         
        . ')';
        $db->query($sql);

        echo '登録しました。</br>';
        echo '<a href="omotan_top.php">トップページへ</a>';
    } else {
    echo 'パスワードを再確認してください。';
    echo '<a href="javascript:history.go(-1);">戻る</a>';
    }
    }
    // ■■　パスワード変更状態
    elseif ($_POST["mode"] == "modifypassword") {
    if ($_POST["password"] == $_POST["confirm"]) {

    $sql = "UPDATE users "
    . " SET user_password = '" . $_POST["password"] . "'"
    . " WHERE user_name = '" . $_POST["username"] . "'";
    $db->query($sql);

    echo '登録しました。</br>';
    echo '<a href="omotan_top.php">トップページへ</a>';
    } else {
    echo 'パスワードを再確認してください。';
    echo '<a href="javascript:history.go(-1);">戻る</a>';
    }
    }
    ?>
</body>
</html>
