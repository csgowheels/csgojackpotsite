<?php session_start(); ?>
<?php

include("connect.php");
if(isset($_POST["itemprice"]) && isset($_POST["ID"])){

    
    mysql_query("update skinprice set avg_price_7_days ='".$_POST["itemprice"]."' where id='".$_POST["ID"]."'");
    
    header('Location: items.php');
}
?>

