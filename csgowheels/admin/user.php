<?php
include("connect.php");

if(isset($_POST["username"]) && isset($_POST["steamid"]) &&
  isset($_POST["tradeurl"]) && isset($_POST["profileurl"]) && isset($_POST["avatar"]))

{
$q="INSERT INTO `csgowhee_csgo`.`user` (`id`, `usteamid`, `communityvisibilitystate`, `profilestate`, `personname`, `lastlogoff`, `profileurl`, `avatar`, `avatarmedium`, `avatarfull`, `personastate`, `realname`, `primaryclanid`, `timecreated`, `profiletradeurl`, `check_tos`) VALUES (NULL,".$_POST["steamid"]." , '3', '1', '".$_POST["username"]."', '', '".$_POST["profileurl"]."', '".$_POST["avatar"]."', '', '', '', '', '', '', '".$_POST["tradeurl"]."', '1')";
    
    $result=mysql_query($q);
    
    

}

header('Location: add-user.php');











