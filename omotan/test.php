<?php
//エラー詳細表示
//ini_set("display_errors", 1);
//error_reporting(E_ALL);

$uploads_dir = './profile_img';

$tmp_name = $_FILES["profile"]["tmp_name"];

// basename() で、ひとまずファイルシステムトラバーサル攻撃は防げるでしょう。
// ファイル名についてのその他のバリデーションも、適切に行いましょう。
$name = basename($_FILES["profile"]["name"]);

//uploadする先のディレクトリに、外部からの書き込み権限を付与しておく(sudo chmod 777 ****)
if (move_uploaded_file($tmp_name, $uploads_dir . "/test1ww1.jpg")) {
    
} else {
    echo $uploads_dir . ":失敗";
    print_r($_FILES);
}
?>




<form action="./test.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="profile">
    <input type="submit" value="ファイルをアップロードする">
</form>
