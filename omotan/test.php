<?php
/*$image_path = './profile_img/';

if (file_exists($image_path)) {
//header('Content-Type: image/jpeg');
//echo 'readfile(' . $image_path . $_SESSION["userid"] . '.jpg)';
echo 'aaa';

}
 */


$image_path = './profile_img/1.jpg';
if (file_exists($image_path)) {
    $fp   = fopen($image_path,'rb');
    $size = filesize($image_path);
    $img  = fread($fp, $size);
    fclose($fp);

    header('Content-Type: image/jpeg');
    echo $img;
}
?>
