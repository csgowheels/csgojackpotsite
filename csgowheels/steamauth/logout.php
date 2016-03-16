<?php
include("settings.php");
session_start();
unset($_SESSION['steamid']);
unset($_SESSION['steam_uptodate']);
unset($_SESSION['steam_steamid']);
session_destroy();

header("Location: ".$steamauth['logoutpage']);
?>