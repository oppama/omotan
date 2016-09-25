<?php
// セッション制御
session_start();


//エラー詳細表示
//ini_set("display_errors", 1);
//error_reporting(E_ALL);
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
        header("Location: top.php");
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

        
<!--タブで分けている箇所  -->
    <style type="text/css"><!--
        /* ▼(A)表示領域全体 */
        div.tabbox { margin: 0px; padding: 0px; width: 400px; }

        /* ▼(B)タブ部分 */
        p.tabs { margin: 0px; padding: 0px; }
        p.tabs a {
            /* ▼(B-2)リンクをタブのように見せる */
            display: block; width: 5em; float: left;
            margin: 0px 1px 0px 0px; padding: 3px;
            text-align: center;
            border-radius: 12px 12px 0px 0px; /* 角を丸くする */
            position:relative;
            left: 15px;
        }
        /* ▼(B-3)各タブの配色 */
        p.tabs a.tab1 { background-color: #008800;  color: white; }
        p.tabs a.tab2 { background-color: #008800; color:white;}
        p.tabs a:hover { color: yellow; }

        /* ▼(C)タブ中身のボックス */
        div.tab {
            /* ▼(C-2)ボックス共通の装飾 */
            height:auto; overflow: auto; clear: left;border-radius: 20px;
        }
        /* ▼(C-3)各ボックスの配色 */
        div#tab1 { border: 2px solid black;}
        div#tab2 { border: 2px solid black;}
        div.tab p { margin: 0.5em; }
        --></style>
 
    <script type="text/javascript"><!--
    function ChangeTab(tabname) {
            // 全部消す
            document.getElementById('tab1').style.display = 'none';
            document.getElementById('tab2').style.display = 'none';
            // 指定箇所のみ表示
            document.getElementById(tabname).style.display = 'block';
        }
        // --></script>



        
        
        
        
    </head>

    <body>

        <!-- トップ画：ここから -->
        <!-- 画像表示 -->
        <div class="top">
            <span id="heading05" align="center"><font size="7">omotan</font></span>

            <!-- トップへのリンク表示 -->
            <span>
                <a class="btn_i" type="button" onclick="location.href = 'top.php'"> TOP </a>
            </span>
            <span style="margin-right: 500px;"></span>

            <!-- 検索窓表示 -->
            <span>
                <form action="search_logic.php" method="POST" style="display:inline" >
                    <input type="text" name="search_word" placeholder="検索ワード">
                    <input type="hidden" name="search_check" value=1>
                    <input type="submit" value="検索">
                </form>
            </span>


        </div>
        <!-- トップ画：ここまで -->
        <div id="margin"></div>

        <div id="login"><!-- ログインフォーム：ここから -->



            <?php
// --- ログインフォーム --------------------

            echo '<div>';
            echo '<img src="./profile_img/' . $_SESSION["userid"] . '.jpg"'
            . ' style="width:10%;height:auto;"></img>';
            echo '<span style="margin-right: 15px;"></span>';

            echo $_SESSION["username"] . '<br><br>';
            //マイページ

            echo '<form name=mypage action="mypage.php" method="GET" style="display:inline;">';
            echo '<input type="hidden" name="username" value="' . $_SESSION["username"] . '">';
            echo '<a href="javascript:void(0)" onclick="document.mypage.submit();">マイページ</a>';
            echo '</form>';
            echo '</br>';

            //ログアウト
            echo '<a href="top.php?mode=logout">ログアウト</a>';
            echo '</div>';
            ?>


        </div><!-- ログインフォーム：ここまで -->




    <div id="search_result">
        <div class="tabbox">
            <p class="tabs">
                <a href="#tab1" class="tab1" onclick="ChangeTab('tab1'); return false;">omotan</a>
                <a href="#tab2" class="tab2" onclick="ChangeTab('tab2'); return false;">アカウント</a>
            </p>               
            <div id="tab1" class="tab">
                <?php
                //タブ1の中身
                echo "<p>";

// 新着tweet：ここから
//表示用に検索結果のtweet_idを抽出する
                $ids = "";
                $n=1;
                foreach ($_SESSION["search_result_tweet"] as $value) {
                    $ids .= $value["tweet_id"] . ",";
                    $n++;
                }

                $sth = $dbh->prepare("SELECT "
                        . "tweet_id, user_name,tweet, created_at, user_id, favorite "
                        . "FROM "
                        . "tweets "
                        . "WHERE tweet_id in(" . $ids
                        . "0) ORDER BY created_at DESC;");
                $sth->execute();
                //                   $dataset = $sth->fetch(PDO::FETCH_ASSOC);
//                    print_r($dataset);
//削除・いいね用の配列を用意
                $tweetid = array();
                for($i= 1;$i<=$n;$i++) {
                    $dataset = $sth->fetch(PDO::FETCH_ASSOC);
                    $tweetid[$i - 1] = $dataset["tweet_id"];

                    if (!empty($dataset)) {
                        //tweet主のリンク先の作成
                        //画像のリンク                     

                        echo '<form name=img_tweetuser' . $i . ' action="mypage.php" method="GET" style="display:inline">';
                        echo '<input type="hidden" name="username" value="' . $dataset["user_name"] . '">';
                        echo '<a href="mypage.php" >';
                        echo '<img src="./profile_img/' . $dataset["user_id"] . '.jpg"'
                        . ' style="width:5%;height:15%;position:relative; left:5px;">';
                        echo '<span style="margin-right: 15px;"></span>';
                        echo '</a>';
                        echo '</form>';

                        //ユーザー名のリンク                        
                        echo '<form name=name_tweetuser' . $i . ' action="mypage.php" method="GET" style="display:inline;">';
                        echo '<input type="hidden" name="username" value="' . $dataset["user_name"] . '">';
                        echo '<a href="javascript:void(0)" onclick="document.name_tweetuser' . $i . '.submit();" style="text-decoration:none;"><FONT size=2><B>' . $dataset["user_name"] . '</B></FONT></a>';
                        echo '</form>';
                        echo '<span style="margin-right: 6px;"></span>';

                        //投稿日の登録
                        echo '<span id="time">' . substr($dataset["created_at"], 0, 10) . "</span><br>";
                        echo '<FONT size=4>' . $dataset["tweet"] . "</FONT></br>";

                        if (!empty($_SESSION["userid"])) {//ログアウトしているユーザーには出さないようにする。
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
                                echo '<form name=iine' . $i . ' action="edit.php" method="POST"  style="display:inline;">';
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
                                echo '<form name=iine' . $i . ' action="edit.php" method="POST"  style="display:inline;">';
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
                                echo '<form name=del' . $i . ' action="edit.php" method="POST" onSubmit="return check()"  style="display:inline;">';
                                echo '<a id=del' . $i . '></a>';
                                echo '<input type="hidden" name="userid" value=' . $_SESSION["userid"] . '>';
                                echo '<input type="hidden" name="tweetid" value=' . $tweetid[$i - 1] . '>';
                                echo '<input type="hidden" name="tweet_edit" value="del">';
                                echo '<input type="hidden" name="focus" value="del' . $i . '">'; //実験
                                echo '<a href="javascript:void(0)" onclick="document.del' . $i . '.submit();" style="text-decoration:none;">削除する</a>';
                                echo '&nbsp;&nbsp;&nbsp;';
                                echo '</form>';
                            }
                        }
                        echo '<hr size="3" align="left" color="#EEEEEE">';
                    } else {
                        
                    }
                }

//新着tweet：ここまで
                //タブ1ここまで
                echo "</p>";
                ?>
            </div>
            <div id="tab2" class="tab">
                    <?php
                //タブ2の中身
                echo "<p>";

// 新着tweet：ここから
//表示用に検索結果のtweet_idを抽出する
                $ids = "";
                $n=1;
                foreach ($_SESSION["search_result_tweet"] as $value) {
                    $ids .= $value["tweet_id"] . ",";
                    $n++;
                }

                $sth = $dbh->prepare("SELECT "
                        . "tweet_id, user_name,tweet, created_at, user_id, favorite "
                        . "FROM "
                        . "tweets "
                        . "WHERE tweet_id in(" . $ids
                        . "0) ORDER BY created_at DESC;");
                $sth->execute();
                //                   $dataset = $sth->fetch(PDO::FETCH_ASSOC);
//                    print_r($dataset);
//削除・いいね用の配列を用意
                $tweetid = array();
                for($i= 1;$i<=$n;$i++) {
                    $dataset = $sth->fetch(PDO::FETCH_ASSOC);
                    $tweetid[$i - 1] = $dataset["tweet_id"];

                    if (!empty($dataset)) {
                        //tweet主のリンク先の作成
                        //画像のリンク                     

                        echo '<form name=img_tweetuser' . $i . ' action="mypage.php" method="GET" style="display:inline">';
                        echo '<input type="hidden" name="username" value="' . $dataset["user_name"] . '">';
                        echo '<a href="mypage.php" >';
                        echo '<img src="./profile_img/' . $dataset["user_id"] . '.jpg"'
                        . ' style="width:5%;height:15%;position:relative; left:5px;">';
                        echo '<span style="margin-right: 15px;"></span>';
                        echo '</a>';
                        echo '</form>';

                        //ユーザー名のリンク                        
                        echo '<form name=name_tweetuser' . $i . ' action="mypage.php" method="GET" style="display:inline;">';
                        echo '<input type="hidden" name="username" value="' . $dataset["user_name"] . '">';
                        echo '<a href="javascript:void(0)" onclick="document.name_tweetuser' . $i . '.submit();" style="text-decoration:none;"><FONT size=2><B>' . $dataset["user_name"] . '</B></FONT></a>';
                        echo '</form>';
                        echo '<span style="margin-right: 6px;"></span>';

                      } else {
                        
                    }
                }

//新着tweet：ここまで
                //タブ2ここまで
                echo "</p>";
                ?>
            </div>
        </div>
    </div>
    <script type="text/javascript"><!--
       // デフォルトのタブを選択
        ChangeTab('tab1');
        // --></script>

</body>
</html>
