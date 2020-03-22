<?php
$modul="dict_edit";
$area="all";
header('Content-Type: text/html; charset=utf-8');

require("../inc/req.php");

$sql = "SET NAMES 'utf8'";
mysqli_query($con, $sql);

if($_GET['action']=='edit') {
    validate("current","int");
	  $element = filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
	  $value = filter_input(INPUT_POST, 'value', FILTER_SANITIZE_STRING);
	   $current = filter_input(INPUT_POST, 'current', FILTER_SANITIZE_STRING);
	    mysqli_query($con, "UPDATE changelog SET ".$element." = '".$value."',modified=NOW() WHERE id=".$current);
		 "UPDATE changelog SET ".$element." = '".$value."',modified=NOW() WHERE id=".$current;
     exit(0);//validate("value","string");
   
   
}



?>