<?php
$modul="general";
$area="superadmin";
$menu_item="general";

require("inc/req.php");

validate("clear","int");
validate("msg","string");
validate("subject","string");

if($_VALID['msg']!=""&&$_VALID['subject']!="") {
    //Get all User

    //$test = 1;

    //if($test==1){

    //		$msg=$_VALID['msg'];
    //		$subject=$_VALID['subject'];
    //		send_mail($msg, $subject, "rbl@hecht-international.com", "Reinhold Blank");

    //}else{

    $membersql="SELECT firstname, lastname, email
									FROM user ";
    $memberresult = mysqli_query($con, $membersql);

    while($row=mysqli_fetch_row($memberresult)) {
        $msg=$_VALID['msg'];
        $subject=$_VALID['subject'];
        send_mail($msg, $subject, $row[2], $row[0]." ".$row[1]);
    }

    //}

}

$_VALID['clear']=0; // Auskommentieren wenn Funktion gebraucht wird...

if($_VALID['clear']==1) {

    $truncate[]="remark";
    $truncate[]="missing_matter";
    $truncate[]="document";
    $truncate[]="production_status";
    $truncate[]="article_log";
    $truncate[]="article";
    $truncate[]="project2user";
    $truncate[]="project";

    foreach($truncate as $table) {
        $sql="TRUNCATE ".$table;
        $result=mysqli_query($con, $sql);
        if(!$result) {
            $err.="ERROR in 00TRUN-".$table." ";
        }
    }

    if($err=="") {
        $err.=" All PSO Data cleared. ";
    }

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
    General
    </td></tr>
    <tr><td bgcolor="#FFFFFF"> 
<table class="table2" width="100%" cellpadding="6" cellspacing="1">
    <tr bgcolor="#FFFFFF">
        <td style="border-bottom:1px solid black;padding-bottom:10px;">
            <form name="clear_pso" method="get" action="superadmin_general.php">
                <p>(Don't forget to truncate Upload Folder)</p>
                <input type="button" onclick='if(confirm("Do you really want to clear PSO DATA?")){document.clear_pso.clear.value=1;document.clear_pso.submit();}' value='CLEAR PSO DATA' class="sub-button">
                <input type="hidden" name="clear" value="0">
            </form>
        </td>
    </tr>
    <tr >
        <td style="padding-top:10px;" bgcolor="#FFFFFF">
            <form name="msg2all" method="get" action="superadmin_general.php">
                <table>
                    <tr><td>	<input type="text" name="subject"> </td></tr>
                    <tr><td>	<textarea rows="10" cols="30" name="msg"></textarea> </td></tr>
                    <tr><td>	<input type="submit" value="Submit" class="sub-button"> </td></tr>
                </table>
            </form>
        </td>
    </tr>
</table>
</td></tr>
    
    </table></td></tr></table></td></tr></table>
<?php if($err!="") {
    echo $err;
} ?>

<?php
require("inc/footer.inc.php");
?>
<script language="javascript">
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



$("#loading-image")
		.bind("ajaxSend", function(){
		$(this).show();
		})
		.bind("ajaxComplete", function(){
		$(this).hide();
		});
</script>        



<style type="text/css" >
#loading-image {
	
	width: 65px;
	height: 55px;
	position: fixed;
	right: 180px;
	z-index: 1;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	margin-right:550px;
	top:200px;;
	-khtml-border-radius: 10px;
}
</style>