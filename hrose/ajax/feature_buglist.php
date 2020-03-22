<?php
require("../inc/req.php");

$userEmail    = $_SESSION['email'];
$userName     = $_SESSION['firstname'].' '.$_SESSION['lastname'];
define(CONFIG_SERVER,"DEVELOPMENT");
if($_GET['action']=='subscribe')
{
	if(CONFIG_SERVER == 'DEVELOPMENT'){	
	$element 	= filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
	$str 		= explode("_", $element);
	$SQL		= "INSERT IGNORE INTO featurebug_users(featurebug_id,user_id,email,name) VALUES ('".$str[0]."','".$str[1]."','".$userEmail."','".$userName."')";
	$res = mysqli_query($con, $SQL);
	exit(0);
	}
	else if(CONFIG_SERVER == 'LIVE'){
	$db = dbconnect_php10();	
	$element 	= filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
	$str 		= explode("_", $element);
	$SQL		= "INSERT IGNORE INTO featurebug_users(featurebug_id,user_id,email,name) VALUES ('".$str[0]."','".$str[1]."','".$userEmail."','".$userName."')";
	$res = mysqli_query($con, $SQL);
	mysqli_close($con, $db);
	exit(0);
	}
	
} 
else if($_GET['action']=='unsubscribe') {
	if(CONFIG_SERVER == 'DEVELOPMENT'){	
	$element 	= filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
	$str 		= explode("_", $element);
	$SQL 		= "DELETE FROM featurebug_users WHERE featurebug_id = '".$str[0]."' AND user_id = '".$str[1]."'";
	$res = mysqli_query($con, $SQL);
	exit(0);
	}
	else if(CONFIG_SERVER == 'LIVE'){
	$db = dbconnect_php10();	
	$element 	= filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
	$str 		= explode("_", $element);
	$SQL 		= "DELETE FROM featurebug_users WHERE featurebug_id = '".$str[0]."' AND user_id = '".$str[1]."'";
	$res = mysqli_query($con, $SQL,$db);
	mysqli_close($con, $db);
	exit(0);
	}
}
else if($_GET['action']=='delete'){
	$element 	= filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
	$str 		= explode("-", $element);
	$SQL 		= "DELETE FROM changelog WHERE id = ".$str[1]."";
	$res = mysqli_query($con, $SQL);
	exit(0);
}
else if($_GET['action']=='movetotask'){

	$element 	= filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
	$str 		= explode("-", $element);
	$SQL		= "UPDATE changelog SET movetotask = 1 WHERE id = ".$str[1]."";
	$res = mysqli_query($con, $SQL);
	
	exit(0);
	
}

?>