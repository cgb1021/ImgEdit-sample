<?php
// var_dump($_FILES['image']);
error_reporting(0);
header('Access-Control-Allow-Origin: *');
if ($_FILES['image'] && !$_FILES['image']['error']) {
  // sleep(5);
  $arr = explode('.', $_FILES['image']['name']);
  $ext = $arr[count($arr) - 1];
  $name = $_SERVER['REQUEST_TIME'] . '.' . ($ext ? $ext : 'jpg');
  if (move_uploaded_file($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/server/upload/' . $name)) {
    echo '{"result": 0, "msg": "' . $_FILES['image']['name'] . ',' . $name . '"}';
  } else {
    echo '{"result": 1, "msg": "' . $_FILES['image']['name'] . '"}';
  }
} else echo '{"result": 1, "msg": "empty"}';
