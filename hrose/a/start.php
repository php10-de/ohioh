<?php
//require_once('/home/path/to/subdomain/m/mobilize.php');
header('Content-Type: text/html; charset=UTF-8');
die('hea');
$modul="start";
$area="no_customer";
$menu_item="start";

require("inc/req.php");
header('Content-Type: text/html; charset=utf-8');
require("inc/header.inc.php");

/*
if($_SESSION['is_quality']){
header('Location:article.php');
}
else{
header('Location:project.php?view=order&closed=0');
}*/
?>
        <div class="contentheadline"></div><div class="contenttext"><!--<img src="images/start_de.gif">--><br></div>
<?php require 'inc/footer.inc.php';?>
