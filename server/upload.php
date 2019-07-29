<?php
// var_dump($_FILES['image']);
if ($_FILES['image'] && !$_FILES['image']['error']) {
  echo move_uploaded_file($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/server/upload/'.$_SERVER['REQUEST_TIME'].'.png');
} else echo 'empty';
