<?php
// セッション制御
session_start();



//パス情報読み込み
require 'secret/secret.php';

$dbh = new PDO('mysql:host=localhost;dbname=omotan', $user
        , $password, array(PDO::ATTR_PERSISTENT => true));

// ログアウト処理
if ($_GET["mode"] == "logout") {
    // ログアウト処理
    $_SESSION = array();
    session_destroy();
}
// ログイン中または未ログイン
else {

    // ユーザー名取得
    if (!empty($_SESSION["userid"])) {
        $sth = $dbh->prepare("SELECT user_name FROM users WHERE user_id = " . $_SESSION["userid"]);
        $sth->execute();

        $result = $sth->fetch(PDO::FETCH_ASSOC);
        $_SESSION["username"] = $result["user_name"];
    }
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
    <head>
        <!--	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> -->
        <meta http-equiv="Content-Style-Type" content="text/css">
        <title>omotan</title>



        <script type="text/javascript">
<!--                //確認ダイアログの作成 -->

            function check() {

                if (window.confirm('本当に実行しますか？')) { // 確認ダイアログを表示
                    return true; // 「OK」時は送信を実行
                } else { // 「キャンセル」時の処理
                    window.alert('キャンセルされました'); // 警告ダイアログを表示
                    return false; // 送信を中止
                }
            }

        </script>



        <script Language="text/javascript">
            <!--  //文字をformのsubmitリンクにする。
        function send()
            {
                test1.submit();
            }
                </script>

<style type="text/css">
<!--
a:link { color :black; }
a:visited { color :black; }
-->
</style>
        <link rel="stylesheet" type="text/css" href="omotan.css">
        <link rel="shortcut icon" href=”favicon.ico”>


    </head>

    <body>
        <!-- トップ画の上の部分 ここから-->            

        <!-- トップ画の上の部分 ここまで-->            

        <!-- トップ画：ここから -->
    <!--    <div><img src="omotan.png" align="middle"></div> -->
        <div class="top">
            <span id="heading05" align="center"><font size="7">omotan</font></span>
            <span>
                <a class="btn_i" type="button" onclick="location.href = 'omotan_top.php'"> TOP </a>
                <span style="margin-right: 10px;"></span>
                <a class="btn_i" type="button" onclick="location.href = 'error.php'"> 通知 </a>
                <span style="margin-right: 10px;"></span>
                <a class="btn_i" type="button" onclick="location.href = 'error.php'"> メッセージ </a>
            </span>
        </div>
        <!-- トップ画：ここまで -->
    <div id="margin"></div>

    <div id="login"><!-- ログインフォーム：ここから -->



        <?php
// --- ログインフォーム --------------------
        if (empty($_SESSION[userid])) {

            echo '<div>';
            echo '<form action="omotan_login.php" method="POST">';
            echo 'ログイン名 <input type="text" name="username" size="8"><br>';
            echo 'パスワード <input type="password" name="password" size="8"><br>';
            echo '<input type="hidden" name="mode" value="login">';
            echo '<input type="submit" value="ログイン">';
            echo '</form>';
            echo '</div>';
        } else {
            echo '<div>';
            echo "こんにちは</br>" . $_SESSION["username"] . 'さん <br><br>';
            //マイページ
//            echo '<a href="omotan_mypage.php">マイページ</a></br>';

            echo '<form name=mypage action="omotan_mypage.php" method="GET" style="display:inline;">';
            echo '<input type="hidden" name="username" value="' . $_SESSION["username"] . '">';
            echo '<a href="javascript:void(0)" onclick="document.mypage.submit();">マイページ</a>';
            echo '</form>';
            echo '</br>';

            //ログアウト
            echo '<a href="omotan_top.php?mode=logout">ログアウト</a>';
            echo '</div>';
        }
        ?>


    </div><!-- ログインフォーム：ここまで -->



    <div id="products">


        <?php
        //つぶやき画面表示 ここから

        if (empty($_SESSION[userid])) {
            
        } else {
            echo '<div class="tweet">';
            echo '<form action="omotan_add.php" method="POST">';
            echo '<input type="text" name="tweet" placeholder="例:ピスタチオ"><br>';
            echo '<input type="hidden" name="add_check" value=1>';
            echo '<input type="submit" value="omotan">';
            echo '</form>';
            echo '</div>';
            echo '<section id="line01"><hr /></section>';
        }

        //つぶやき画面表示 ここまで
//        echo '<div class="title">新着omotan</div>';


        // 新着tweet：ここから
        //表示件数用の変数
        $h = 10;
        $dbh = new PDO('mysql:host=localhost;dbname=omotan', $user
                , $password, array(PDO::ATTR_PERSISTENT => true));

        $sth = $dbh->prepare("SELECT "
                . "tweet_id, user_name,tweet, created_at, user_id, favorite "
                . "FROM "
                . "tweets "
                . "ORDER BY created_at DESC LIMIT " . $h . ";");
        $sth->execute();

        //削除・いいね用の配列を用意
        $tweetid = array($h);

        for ($i = 1; $i <= 10; $i++) {
            $dataset = $sth->fetch(PDO::FETCH_ASSOC);
            $tweetid[$i - 1] = $dataset["tweet_id"];

            if (!empty($dataset)) {
                //tweet主のリンク先の作成

                echo '<form name=tweetuser' . $i . ' action="omotan_mypage.php" method="GET" style="display:inline;">';
                echo '<input type="hidden" name="username" value="' . $dataset["user_name"] . '">';
                echo '<a href="javascript:void(0)" onclick="document.tweetuser' . $i . '.submit();" style="text-decoration:none;"><FONT size=2><B>' . $dataset["user_name"] . '</B></FONT></a>';
                echo '</form>';
                echo '<span style="margin-right: 6px;"></span>';
                echo '<span id="time">' . substr($dataset["created_at"],0,10) . "</span><br>";
                echo '<FONT size=4>' . $dataset["tweet"] . "</FONT></br>";

                //いいね！
                $iine = $dbh->prepare(
                        "SELECT count(*) AS fnum FROM favorite"
                        . " WHERE user_id = " . $_SESSION["userid"]
                        . " and favorite_tweet_id = " . $tweetid[$i - 1]
                        . " and deleted_at is null"
                        . ";"
                );
                $iine->execute();
                $result = $iine->fetch(PDO::FETCH_ASSOC);

                if ($result["fnum"] > 0) {
                    echo '<form name=iine' . $i . ' action="omotan_edit.php" method="POST"  style="display:inline;">';
                    echo '<a id=iine' . $i . '></a>';
                    echo '<input type="hidden" name="userid" value=' . $_SESSION["userid"] . '>';
                    echo '<input type="hidden" name="tweetid" value=' . $tweetid[$i - 1] . '>';
                    echo '<input type="hidden" name="tweet_edit" value="iine">';
                    echo '<input type="hidden" name="focus" value="iine' . $i . '">'; //実験
                    echo '<a href="javascript:void(0)" onclick="document.iine' . $i . '.submit(); return false;" style="text-decoration:none;">いいねを取り消す</a>';
                    echo '&nbsp;&nbsp;';
                    echo $dataset["favorite"];
                    echo '&nbsp;&nbsp;&nbsp;';
                    echo '</form>';
                } else {
                    echo '<form name=iine' . $i . ' action="omotan_edit.php" method="POST"  style="display:inline;">';
                    echo '<a id=iine' . $i . '></a>';
                    echo '<input type="hidden" name="userid" value=' . $_SESSION["userid"] . '>';
                    echo '<input type="hidden" name="tweetid" value=' . $tweetid[$i - 1] . '>';
                    echo '<input type="hidden" name="tweet_edit" value="iine">';
                    echo '<input type="hidden" name="focus" value="iine' . $i . '">'; //実験
                    echo '<a href="javascript:void(0)" onclick="document.iine' . $i . '.submit(); return false;" style="text-decoration:none;">いいね</a>';
                    echo '&nbsp;&nbsp;';
                    echo $dataset["favorite"];
                    echo '&nbsp;&nbsp;&nbsp;';
                    echo '</form>';
                }

//つぶやき主のみ削除ボタンを表示
                if ($_SESSION["userid"] == $dataset["user_id"]) {
                    echo '<form name=del' . $i . ' action="omotan_edit.php" method="POST" onSubmit="return check()"  style="display:inline;">';
                    echo '<a id=del' . $i . '></a>';
                    echo '<input type="hidden" name="userid" value=' . $_SESSION["userid"] . '>';
                    echo '<input type="hidden" name="tweetid" value=' . $tweetid[$i - 1] . '>';
                    echo '<input type="hidden" name="tweet_edit" value="del">';
                    echo '<input type="hidden" name="focus" value="del' . $i . '">'; //実験
                    echo '<a href="javascript:void(0)" onclick="document.del' . $i . '.submit();" style="text-decoration:none;">削除する</a>';
                    echo '&nbsp;&nbsp;&nbsp;';
                    echo '</form>';
                }

                echo '<hr size="3" align="left" color="#EEEEEE">';
            } else {
                
            }
        }

        //新着tweet：ここまで
        ?>

    </div>
</body>
</html>