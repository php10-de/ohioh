<?php
$modul="dict_edit";
$area="all";
header('Content-Type: text/html; charset=utf-8');

require("../inc/req.php");

$sql = "SET NAMES 'utf8'";
mysqli_query($con, $sql);

if($_GET['action']=='edit') {
    validate("dict_id","int");
    //validate("value","string");
    if($_POST['elem'] == 'de' || $_POST['elem'] == 'cn' || $_POST['elem'] == 'en') {
	
        $result = mysqli_query($con, "SELECT ".$_POST['elem']." FROM dict WHERE dict_id = ".$_VALID['dict_id']."");
        $row = mysqli_fetch_row($result);
        if($row[0] == $_POST['value'])
            exit(0);
       echo $sql = "UPDATE dict SET ".$_POST['elem']." = '".mysqli_real_escape_string($con, $_POST['value'])."', user_id = " . (int) $_SESSION['user_id'] . " WHERE dict_id = ".$_VALID['dict_id']."";
        $res = mysqli_query($con, $sql);
        $sql = "INSERT INTO dict_changelog (dict_id, field, old_value, new_value, user_id, dbupdate) VALUES (".$_VALID['dict_id'].", '".$_POST['elem']."', '".$row[0]."', '".mysqli_real_escape_string($con, $_POST['value'])."', " . (int) $_SESSION['user_id'] . ", now() )";
        $res = mysqli_query($con, $sql);
    }
    exit(0);
}

validate("dict_id","int");
validate("de","int");
validate("de","string");
validate("cn","string");
validate("en","string");

if (!$_VALID['dict_id']) $_VALID['dict_id'] = 'NULL';


/***** Add article Start ****/
if ($_POST['dict_save']) {
    $sql = "REPLACE INTO dict (dict_id, de, cn, en, user_id, dbupdate) VALUES (" . $_VALID['dict_id'] . ",'" . mysqli_real_escape_string($con, $_REQUEST['de']) . "', '" . mysqli_real_escape_string($con, $_REQUEST['cn']) . "', '" . mysqli_real_escape_string($con, $_REQUEST['en']) . "', " . (int) $_SESSION['user_id'] . ", now())";
    $res = mysqli_query($con, $sql);
    if ($res) {

        echo '<script type="text/javascript">parent.$.fancybox.close();parent.window.location.reload();</script>';

    } else {
        $error = 'The entry could not be saved.';
    }
}

//define('DICT_ADMIN', ($_SESSION['crt_dict']==1||$_SESSION['is_admin']==1||$_SESSION['is_superadmin']==1));
define('DICT_ADMIN', 1);

require("../inc/miniheader.inc.php");

/***** Fehlermeldung *****/
if ($error) {
    echo '<span class="error">' . $error . '</span>';
}

/****** Formular  *****/
echo '<form id="dict_form" method=post accept-charset="utf-8">
	  <input type="hidden" name="dict_id" value="'.$_VALID['dict_id'].'">';
echo '<table class="pso_table" style="width:auto" cellpadding="0" cellspacing="0">>
	  <tr>
	  <th>German</th>
	  <th>Chinese</th>
	  <th>English</th>';

echo '
	  <th>Edit</th>
	  </tr>
	 <tr>';

echo '<td><input name="de" value="' . $_VALID['de'] . '"></td>
	<td><input name="cn" value="' . $_VALID['cn'] . '"></td>
	<td><input name="en" value="' . $_VALID['en'] . '"></td>
	<td colspan=2><input type="submit" name="dict_save" value="Save"></td></tr>';
echo '</table></form>';		


?>