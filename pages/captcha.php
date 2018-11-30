<?php

/*Based on code found on www.plus2net.com   */

session_start();
header ("Content-type: image/png");

if(isset($_SESSION['captcha']))
{
unset($_SESSION['captcha']); // destroy the session if already there
}
$im = @ImageCreate (130, 30) // Width and hieght of the image. 
or die ("Cannot Initialize new GD image stream");
$background_color = ImageColorAllocate ($im, 204, 204, 204); // Assign background color
//////Part 1 Random string generation ////////
//$string1="abcdefghijklmnopqrstuvwxyz";
//$string2="1234567890";
$string1="abcdefghjkmnpqrstuvwxzABCDEFGHKMNPQRSTUVWXYZ";
$string2="23456789";
$string=$string1.$string2;
$text_color = ImageColorAllocate ($im, 51, 51, 255);      // text color is given 
$random_text='';

for($i=1;$i<=5;$i++){
    $src = @ImageCreate(20, 20);
    $background_color = ImageColorAllocate ($src, 204, 204, 204); // Assign background color

    $string= str_shuffle($string);
    $text = substr($string,0,1); // One char of the random chars
    ImageString($src,6,5,0,$text,$text_color); 
    $angle=rand(10,60);
    $src = imagerotate($src, $angle, 0);
    $x=$i*20;
    imagecopy($im, $src, $x, 5, 0, 0, 20, 20);
    $random_text .=$text;
    imagedestroy($src);
}

$_SESSION['captcha'] =$random_text; // Assign the random text to session variable

///// Create the image ////////
ImagePng ($im); // image displayed
imagedestroy($im); // Memory allocation for the image is removed. 