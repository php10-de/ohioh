<?php
$modul="help";
$area="all";
$menu_item="help";

require("inc/req.php");
require_once("inc/sqlInjection.php");
require_once("inc/dateConv.php");

validate("helpsave","int");
validate("helptext","string");

//Standard User Ansicht
//$su=1;


if($_VALID['helptext']&&$_VALID['helpsave']) {
    $sql ="INSERT INTO help ( helptext, user_id, dbupdate )
				 						VALUES('".sql_injection($_VALID['helptext'])."', ".$_SESSION['user_id'].", '".$timenow."')";

    $result = mysqli_query($con, $sql);
    if(!$result) {
        $err.="ERROR in save";
    }
}

$sql ="SELECT helptext, u.email, h.dbupdate
			 FROM help h
			 INNER JOIN user u ON u.user_id = h.user_id
			 ORDER BY dbupdate desc
			 LIMIT 0,1";
$result=mysqli_query($con, $sql);
$helptext=mysqli_result($result,0,0);
$email=mysqli_result($result,0,1);
$update=mysqli_result($result,0,2);

if($su==1) {
    $_SESSION['is_admin']=0;
    $_SESSION['is_superadmin']=0;
}

require("inc/header.inc.php");

?><div id="loading-image" style="position: absolute; top: 140px; left: 450px;"><img alt="Loading..." src="images/ajax-loader.gif" style=""></div>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_top_padding"> 
  <tr>
    <td bgcolor="#f7f7f7">
	<table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr><td>
	<table width="100%" border="0" cellpadding="6" cellspacing="0" class="table2">
  <tr>
    <td class="row_head">
    Help
    </td>
            </tr>
    <tr><td bgcolor="#FFFFFF"> 
	<?php
	echo "<table cellpadding='6' cellspacing='1' class='table2' > 
						<tr><td valign='top' bgcolor='#FFFFFF' align='center'>Editor:</td><td valign='top' bgcolor='#FFFFFF' align='left'>".$email."</td></tr> 
						<tr><td valign='top' bgcolor='#FFFFFF' align='center'>Last Update:</td><td valign='top' bgcolor='#FFFFFF' align='left'>".dateConv($update)."</td></tr>
						<tr><td valign='top' bgcolor='#FFFFFF' align='center'>Help:</td><td valign='top' bgcolor='#FFFFFF' align='left'>";
if($_SESSION['is_admin']||$_SESSION['is_superadmin']) {
    echo "<form>";
    echo "<textarea rows='20' cols='80' name='helptext'>".$helptext."</textarea>";
    echo "</td></tr><tr><td  bgcolor='#FFFFFF'></td><td  bgcolor='#FFFFFF' align='left'><input type='hidden' name='helpsave' value='1'><input type='submit' value='speichern' class='sub-button'></td></tr>";
    echo "</form>";
}else {
    echo nl2br($helptext);
}
echo "</td></tr>
					</table>";
    ?></td></tr>
    
    </table></td></tr></table></td></tr></table>
<?php
/*echo "
				<fieldset>
				<legend><img src='img/icon/info.png' alt='Help'><span class='box_head'>Help</span></legend>	
					<table> 
						<tr><td>Editor:</td><td>".$email."</td></tr> 
						<tr><td>Last Update:</td><td>".dateConv($update)."</td></tr>
						<tr><td valign='top'>Help:</td><td>";
if($_SESSION['is_admin']||$_SESSION['is_superadmin']) {
    echo "<form>";
    echo "<textarea rows='20' cols='80' name='helptext'>".$helptext."</textarea>";
    echo "<br /><input type='hidden' name='helpsave' value='1'><input type='submit' value='speichern'>";
    echo "</form>";
}else {
    echo nl2br($helptext);
}
echo "</td></tr>
					</table>
				</fieldset>	
			";*/

if($su==1) {
    $_SESSION['is_admin']=1;
    $_SESSION['is_superadmin']=1;
}

require("inc/footer.inc.php");
?>

<script language="javascript" type="text/javascript">
jQuery(window).load(function() {	
		$("#loading-image")
		.bind("ajaxSend", function(){
		$(this).show();
		})
		.bind("ajaxComplete", function(){
		$(this).hide();
		});
			jQuery('#loading-image').hide();
		});

</script>
<style type="text/css" >
#loading-image {
	
	width: 65px;
	height: 55px;
	position: fixed;
	right: 160px;
	z-index: 1;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	/*	background-color: #333;
	border-radius: 10px; */
	margin-right:550px;
	top:300px;;
	-khtml-border-radius: 10px;
}
</style>