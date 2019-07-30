<?php
// var_dump($_FILES['image']);
header('Access-Control-Allow-Origin: *');
$arr = explode('.', $_FILES['image']['name']);
$ext = $arr[count($arr) - 1];
if ($_FILES['image'] && !$_FILES['image']['error']) {
  echo move_uploaded_file($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/server/upload/'.$_SERVER['REQUEST_TIME'].'.'.($ext ? $ext : 'jpg'));
} else echo 'empty';
