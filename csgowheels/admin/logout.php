<?php
include "connect.php";
//session_destroy();
  setcookie('username', $_POST['username'], false);
  setcookie('password', md5($_POST['password']), false);
header("Location: index.php");
exit;
?>