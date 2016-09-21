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
    } else {
        header("Location: omotan_top.php");
    }
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
    <head>
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

        <link rel="stylesheet" type="text/css" href="omotan.css">

    </head>

    <body>
        <!-- トップ画の上の部分 ここから-->            

    <input type="button" onclick="location.href = 'omotan_top.php'"value="TOP">
    <input type="button" onclick="location.href = ' '"value="通知">                            
    <input type="button" onclick="location.href = ' '"value="メッセージ">           









    <!-- トップ画の上の部分 ここまで-->            

    <!-- トップ画：ここから -->
    <div>
        <img src="omotan.png" align="middle">
    </div>
    <!-- トップ画：ここまで -->

    </br>
    <div id="news"><!-- ログインフォーム：ここから -->



        <?php
// --- ログインフォーム --------------------

        echo '<div>';
        echo $_SESSION["username"] . 'さん <br><br>';
        echo '<a href="omotan_top.php?mode=logout">ログアウト</a>';
        echo '</div>';
        ?>

    </div><!-- ログインフォーム：ここまで -->



    <div id="products">


        <?php
//つぶやき画面表示 ここから

        echo '<div>';
        echo '<form action="omotan_add.php" method="POST">';
        echo 'omotan <input type="text" name="tweet" placeholder="例:ピスタチオ"><br>';
        echo '<input type="hidden" name="add_check" value=1>';
        echo '<input type="submit" value="つぶやく">';
        echo '</form>';

        echo '<hr align="left">';
//つぶやき画面表示 ここまで                        
// 新着tweet：ここから
        $dbh = new PDO('mysql:host=localhost;dbname=omotan', $user
                , $password, array(PDO::ATTR_PERSISTENT => true));
        $sth = $dbh->prepare("SELECT "
                . "tweet_id, user_name,tweet, created_at, user_id, favorite "
                . "FROM "
                . "tweets "
                . "where user_id = " . $_SESSION["userid"]
                . " ORDER BY created_at DESC LIMIT 10");
        $sth->execute();

        for ($i = 1; $i <= 10; $i++) {
            $dataset = $sth->fetch(PDO::FETCH_ASSOC);
            $tweetid[$i - 1] = $dataset["tweet_id"];

            if (!empty($dataset)) {
                //tweet主のリンク先の作成

                echo '<form name=tweetuser' . $i . ' action="omotan_mypage.php" method="GET" style="display:inline;">';
                echo '<input type="hidden" name="username" value="' . $dataset["user_name"] . '">';
                echo '<a href="javascript:void(0)" onclick="document.tweetuser' . $i . '.submit();">' . $dataset["user_name"] . '</a>';
                echo '</form>';




                echo $dataset["created_at"] . '<br>';
                echo 'omotan ' . $dataset["tweet"] . "</br>";
                //つぶやき主のみ削除ボタンを表示
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
                    echo '<a href="javascript:void(0)" onclick="document.iine' . $i . '.submit(); return false;">いいねを取り消す</a>';
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
                    echo '<a href="javascript:void(0)" onclick="document.iine' . $i . '.submit(); return false;">いいね</a>';
                    echo '&nbsp;&nbsp;';
                    echo $dataset["favorite"];
                    echo '&nbsp;&nbsp;&nbsp;';
                    echo '</form>';
                }
                if ($_SESSION["userid"] == $dataset["user_id"]) {


                    //削除
                    echo '<form name=del' . $i . ' action="omotan_edit.php" method="POST" onSubmit="return check()"  style="display:inline;">';
                    echo '<a id=del' . $i . '></a>';
                    echo '<input type="hidden" name="userid" value=' . $_SESSION["userid"] . '>';
                    echo '<input type="hidden" name="tweetid" value=' . $tweetid[$i - 1] . '>';
                    echo '<input type="hidden" name="tweet_edit" value="del">';
                    echo '<input type="hidden" name="focus" value="del' . $i . '">'; //実験
                    echo '<a href="javascript:void(0)" onclick="document.del' . $i . '.submit();">削除する</a>';
                    echo '&nbsp;&nbsp;&nbsp;';
                    echo '</form>';
                }





                echo '<hr width="400" size="3" style="border-style:dotted" align="left">';
            } else {
                
            }
        }
//新着tweet：ここまで
        ?>

    </div>
</body>
</html>
