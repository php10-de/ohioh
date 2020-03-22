<?php 

require("../inc/req.php");

if($_GET['action'] == 'edit') {

    $element = filter_input(INPUT_POST, 'elem', FILTER_SANITIZE_STRING);
    $value = filter_input(INPUT_POST, 'value', FILTER_SANITIZE_STRING);

    $str = explode("-", $element);

    mysqli_query($con, "UPDATE changelog SET ".$str[0]." = '".$value."',modified=NOW() WHERE id=".$str[1]);
	if($str[0]=="status"){
		  $mailSendUsers = mysqli_query($con, "SELECT * FROM featurebug_users WHERE featurebug_id=".$str[1]);
		if($value=="1")
		{
		$status = "Open";
		}
		if($value=="2")
		{
		$status = "In Progress";
		}
		if($value=="3")
		{
		$status = "Done";
		}
		if($value=="4")
		{
		$status = "Tested";
		}
		if($value=="5")
		{
		$status = "Live";
		}
		  while($userRow = mysqli_fetch_row($mailSendUsers)){
		
			  $msg = 'Hello '.$userRow[3].' <br> The status of the Feature/Bug with the ID ='.$str[1].'  which you have subscribe is '.$status;
			  $subject= "Feature/Bug Status Done";
			  send_mail($msg, $subject, $userRow[2],$userRow[3],false,true); 
		  }
	}
}

if($_GET['action'] == 'move-up') {
    $current = filter_input(INPUT_POST, 'current', FILTER_SANITIZE_STRING);
    $old = filter_input(INPUT_POST, 'old', FILTER_SANITIZE_STRING);

    $cur_elem = explode("-", $current);
    $old_elem = explode("-", $old);

    $result_old = mysqli_query($con, "SELECT priority FROM changelog WHERE id=".$cur_elem[1]);
    $row_old = mysqli_fetch_row($result_old);

    //$result_cur = mysqli_query($con, "SELECT priority FROM changelog WHERE id=".$cur_elem[1]);
    //$row_cur = mysqli_fetch_row($result_cur);

    mysqli_query($con, "UPDATE changelog SET priority = ".($row_old[0]+1).",modified=NOW() WHERE id=".$cur_elem[1]);
}

if($_GET['action'] == 'move-down') {
    $current = filter_input(INPUT_POST, 'current', FILTER_SANITIZE_STRING);
    $old = filter_input(INPUT_POST, 'old', FILTER_SANITIZE_STRING);

    $cur_elem = explode("-", $current);
    $old_elem = explode("-", $old);
	if(count($cur_elem)>1)
	{
	$id	=$cur_elem[1];
	}
	else
	{
	$id	= $current;
	}
    $result_old = mysqli_query($con, "SELECT priority FROM changelog WHERE id=".$cur_elem[1]);
    $row_old = mysqli_fetch_row($result_old);

    //$result_cur = mysqli_query($con, "SELECT priority FROM changelog WHERE id=".$cur_elem[1]);
    //$row_cur = mysqli_fetch_row($result_cur);
	 "UPDATE changelog SET priority = ".($row_old[0]-1).",modified=NOW() WHERE id=".$id;
    mysqli_query($con, "UPDATE changelog SET priority = ".($row_old[0]-1).",modified=NOW() WHERE id=".$id);
}

if($_GET['action'] == 'change-version') {
    $current = filter_input(INPUT_POST, 'current', FILTER_SANITIZE_STRING);
     $version = filter_input(INPUT_POST, 'version', FILTER_SANITIZE_STRING);
    $position = filter_input(INPUT_POST, 'position', FILTER_SANITIZE_STRING);

    $cur_elem = explode("-", $current);
		if(count($cur_elem)>1)
	{
	$id	=$cur_elem[1];
	}
	else
	{
	$id	= $current;
	}
    if($position == 'bottom'){
    	$result = mysqli_query($con, "SELECT min(priority) FROM changelog WHERE version='".$version."'");
    	$pos = mysqli_fetch_row($result);
    	$pos[0]--;
    } else {
    	$result = mysqli_query($con, "SELECT max(priority) FROM changelog WHERE version='".$version."'");
    	$pos = mysqli_fetch_row($result);
    	$pos[0]++;
    }
	if(!$pos[0]) $pos[0] = 0;
	 "UPDATE changelog SET priority = ".$pos[0].", version = '".$version."',modified=NOW() WHERE id=".$id;
    mysqli_query($con, "UPDATE changelog SET priority = ".$pos[0].", version = '".$version."',modified=NOW() WHERE id=".$id);
}

if($_GET['action'] == 'delete') {
    $current = filter_input(INPUT_POST, 'current', FILTER_SANITIZE_STRING);

    $cur_elem = explode("-", $current);
	if(count($cur_elem)>1)
	{
	$id	=$cur_elem[1];
	}
	else
	{
	$id	= $current;
	}
    //$result_cur = mysqli_query($con, "SELECT priority FROM changelog WHERE id=".$cur_elem[1]);
    //$row_cur = mysqli_fetch_row($result_cur);

    mysqli_query($con, "DELETE FROM changelog WHERE id=".$id);
}
if($_GET['action'] == 'task')
{
    $current = filter_input(INPUT_POST, 'current', FILTER_SANITIZE_STRING);
	$cur_elem = explode("-", $current);
	$result = mysqli_query($con, "SELECT max(version ) FROM changelog ");
    $pos = mysqli_fetch_row($result);
    $new_version	= $pos[0] + 0.1;
    //$result_cur = mysqli_query($con, "SELECT priority FROM changelog WHERE id=".$cur_elem[1]);
    //$row_cur = mysqli_fetch_row($result_cur);
    mysqli_query($con, "UPDATE changelog SET approved='A',version='".$new_version."',modified=NOW() WHERE id=".$cur_elem[1]);
}
if($_GET['action'] == 'next-version')
{
     $current = filter_input(INPUT_POST, 'current', FILTER_SANITIZE_STRING);
	$cur_elem = explode("-", $current);
	if(count($cur_elem)>1)
	{
	$id	=$cur_elem[1];
	}
	else
	{
	$id	= $current;
	}
	$result = mysqli_query($con, "SELECT max(version ) FROM changelog ");
    $pos = mysqli_fetch_row($result);
	$pos_exp	= explode(".",$pos[0]);
	if($pos_exp[1]>=9)
	{
	$new_version	= $pos_exp[0] + 1;
	}
	else
	{
    $new_version	= $pos[0] + 0.1;
	}
     $result_cur =" UPDATE changelog SET version='".$new_version."',modified=NOW() WHERE id=".$id;
    //$row_cur = mysqli_fetch_row($result_cur);
    mysqli_query($con, "UPDATE changelog SET version='".$new_version."',modified=NOW() WHERE id=".$id);
}
?>