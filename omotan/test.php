<?php
/*
  $upload_dir = "./profile_img/";
  try {
  if (is_uploaded_file($_FILES['profile']['tmp_name'])) {
  move_uploaded_file($_FILES['profile']['tmp_name'],
  $upload_dir . "$user_id+1" . ".jpg" );
  print_r($_FILES["profile"]["error"]);
  //                        print_r($_FILES['file']);
  }
  } catch (Exception $e) {
  echo '画像エラー:', $e->getMessage() . PHP_EOL;
  }
 */

//                $uploads_dir = './profile_img';
//$uploads_dir = '/Library/WebServer/Documents/php/omotan/';
$uploads_dir = '/Users/hosoyasatoshi/Desktop/old/';
        $tmp_name = $_FILES["profile"]["tmp_name"];
        // basename() で、ひとまずファイルシステムトラバーサル攻撃は防げるでしょう。
        // ファイル名についてのその他のバリデーションも、適切に行いましょう。
        $name = basename($_FILES["profile"]["name"]);
        move_uploaded_file($tmp_name, "$uploads_dir". "$name");

$tmp_name1 = $_FILES["profile"]["tmp_name"];
echo $tmp_name1."</br>";
?>




<form action="./test.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="profile">
    <input type="submit" value="ファイルをアップロードする">
</form>
