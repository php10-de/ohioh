<?php
$modul="changepw";
$area="all";
$menu_item="password";

require("inc/req.php");

validate("pass_old","string");
validate("pass1","string");
validate("pass2","string");
$success = false;

if($_VALID['pass_old']!=""&&$_VALID['pass1']!=""&&$_VALID['pass2']!="") {

    if ($_VALID['pass1']==$_VALID['pass2']) {

        $membersql="UPDATE user
						SET password = '" . md5($_VALID['pass1'].SALT) . "'
						WHERE password = '" . md5($_VALID['pass_old'].SALT) . "'
						AND email = '" . $_SESSION['email'] . "'";
        $memberresult = mysqli_query($con, $membersql);
        $resultB = mysql_affected_rows();
        if (!$resultB) {
            $err.=" Wrong password. ";
        } else {
            $success = true;
        }

    } else {
        $err.=" Passwords do not match. ";
    }

} else if ($_GET['submitted']) {
    $err.=" Some fields are missing. ";
}

require("inc/header.inc.php");
if ($success) {
    echo "Password changed.";
} else {
    ?>

<table width="100%" cellpadding="6" cellspacing="1" class="">
    <tr>
        <td style="padding-top:10px;" bgcolor="#FFFFFF">
            <form name="changepw" method="get" action="changepw.php">
                <table class="table2" width="100%"  cellpadding="6" cellpadding="1">
                    <tr><td width="10%" align='left' valign='top' bgcolor='#FFFFFF'>Old:</td>
                    <td width="90%" align='left' valign='top' bgcolor='#FFFFFF'><input type="password" name="pass_old"> </td></tr>
                    <tr><td valign='top' bgcolor='#FFFFFF' align='left'>New:</td>
                    <td valign='top' bgcolor='#FFFFFF' align='left'><input type="password" name="pass1"> </td></tr>
                    <tr><td valign='top' bgcolor='#FFFFFF' align='left'>New (re-type):</td>
                    <td valign='top' bgcolor='#FFFFFF' align='left'><input type="password" name="pass2"> </td></tr>
                    <tr><td valign='top' bgcolor='#FFFFFF' align='center'></td><td valign='top' bgcolor='#FFFFFF' align='left'><input type="submit" name="submitted" class="sub-button" value="Submit"> </td></tr>
                </table>
            </form>
        </td>
    </tr>
</table>

    <?php if($err!="") {
        echo $err;
    } ?>

    <?php
}
require("inc/footer.inc.php");
?>