<?php
require("../inc/req.php");

$userEmail    = $_SESSION['email'];
$userName     = $_SESSION['firstname'].'&nbsp;'.$_SESSION['lastname'];
define(CONFIG_SERVER,"DEVELOPMENT");
if($_GET['action']=='subscribe')
{
	//if(CONFIG_SERVER == 'DEVELOPMENT'){	
	$element 	= filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
	$str 		= explode("_", $element);
	 $SQL		= "INSERT IGNORE INTO featurebug_users(featurebug_id,user_id,email,name) VALUES ('".$str[0]."','".$str[1]."','".$userEmail."','".$userName."')";
	$res = mysqli_query($con, $SQL);
	
	//}
	//else if(CONFIG_SERVER == 'LIVE'){
/*	$db = dbconnect_php10();	
	$element 	= filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
	$str 		= explode("_", $element);
	 $SQL		= "INSERT IGNORE INTO featurebug_users(featurebug_id,user_id,email,name) VALUES ('".$str[0]."','".$str[1]."','".$userEmail."','".$userName."')";
	$res = mysqli_query($con, $SQL);
	mysqli_close($con, $db);
	dbconnect_php();*/
	
	//}
	
} 
else if($_GET['action']=='unsubscribe') {
	//if(CONFIG_SERVER == 'DEVELOPMENT'){	
	$element 	= filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
	$str 		= explode("_", $element);
	$SQL 		= "DELETE FROM featurebug_users WHERE featurebug_id = '".$str[0]."' AND user_id = '".$str[1]."'";
	$res = mysqli_query($con, $SQL);
	
	//}
	//else if(CONFIG_SERVER == 'LIVE'){
/*	$db = dbconnect_php10();	
	$element 	= filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
	$str 		= explode("_", $element);
	$SQL 		= "DELETE FROM featurebug_users WHERE featurebug_id = '".$str[0]."' AND user_id = '".$str[1]."'";
	$res = mysqli_query($con, $SQL,$db);
	mysqli_close($con, $db);
	dbconnect_php();*/
	
	//}
}
else if($_GET['action']=='delete'){
	$element 	= filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
	$str 		= explode("-", $element);
	$SQL 		= "DELETE FROM changelog WHERE feature_id = ".$str[1]."";
	$res 		= mysqli_query($con, $SQL);
	$SQL 		= "DELETE FROM featurebug_users WHERE featurebug_id = ".$str[1]."";
	$res		= mysqli_query($con, $SQL);
/*	$db 		= dbconnect_php10();	
	$element 	= filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
	$str 		= explode("_", $element);
	$SQL 		= "DELETE FROM changelog WHERE feature_id = ".$str[1]."";
	$res 		= mysqli_query($con, $SQL,$db);
	$SQL 		= "DELETE FROM featurebug_users WHERE featurebug_id = ".$str[1]."";
	$res 		= mysqli_query($con, $SQL);
	mysqli_close($con, $db);
	dbconnect_php();*/
	
}
else if($_GET['action']=='change_date')
{
	 	$element 	= filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
	 	$str 		= explode("-", $element);
	 	$date_err 	= explode(".",$_REQUEST['estimatedate']);
	 	$date 		=	$date_err[2].'-'.$date_err[1].'-'.$date_err[0] ;
	 	  $SQL 		= "UPDATE changelog SET date_of_completion='".$date."',modified='".date("Y-m-d H:i:s")."'  WHERE feature_id = ".$str[1]."";
		 $res 		= mysqli_query($con, $SQL) or die(mysqli_error());
	
		/*$db = dbconnect_php10();
		$element 	= filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
		$str 		= explode("-", $element);
		$date_err = explode(".",$_REQUEST['estimatedate']);
		$date =$date_err[2].'-'.$date_err[1].'-'.$date_err[0] ;
		$SQL 		= "UPDATE changelog SET date_of_completion='".$date."',modified='".date("Y-m-d H:i:s")."'  WHERE feature_id = ".$str[1]."";
		$res = mysqli_query($con, $SQL) or die(mysqli_error());
		mysqli_close($con, $db);
		dbconnect_php();*/
	
}
else if($_GET['action']=='movetotask'){

		$version	=	$_GET['version'];
		$milestone  =   $_GET['milestone'];
		$element	= filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
	    $str 		= explode("-", $element);
		
		$SQLupdate	= "UPDATE changelog SET movetotask = 1,version='".$version."',milestone='".$milestone."',approved='A' WHERE id = ".$str[1]."";
		$res 		= mysqli_query($con, $SQLupdate) ;
	/*	$db 		= dbconnect_php10();
		$SQLupdate	= "UPDATE changelog SET movetotask = 1,version='".$version."',milestone='".$milestone."',approved='A' WHERE id = ".$str[1]."";
		$res 		= mysqli_query($con, $SQLupdate) ;
		mysqli_close($con, $db);
		dbconnect_php();*/
		/*$SQL 		= "SELECT * FROM changelog WHERE id = ".$str[1]."";
		$result     = mysqli_query($con, $SQL);
		$row = mysqli_fetch_assoc($result);
		
		if($row['bug_feature_type']=="B")
		{
		$type = "bug";
		}
		elseif($row['bug_feature_type']=="N")
		{
		$type = "feature";
		}

		if($row['priority_status']=="C")
		{
		$priority_status = "Critical";
		}
		elseif($row['priority_status']=="H")
		{
		$priority_status = "High";
		}
		elseif($row['priority_status']=="L")
		{
		$priority_status = "Low";
		}
		elseif($row['priority_status']=="M")
		{
		$priority_status = "Normal";
		}
		 $springlooop_text = "[a:me m:".$milestone." l:'".$row['feature_title'].",".$row['id'].",".$row['area'].",".$type."' p:".$priority_status."]".$row['description']."(Author: [".$_SESSION['email']."])";
		 $subject = "PSO::New ticket-".$row['feature_title'];*/
		  //send_mail($springlooop_text, $subject, SPRINGLOOPS_TICKET_RECEIPIENTEMAIL,SPRINGLOOPS_TICKET_RECEIPIENTEMAIL,true, true, true);	
		  //send_mail($springlooop_text, $subject, "sajeena.ke@hitechito.com", true, true, true);	
			//exit(0);
		
		
	/*}*/
}

?>