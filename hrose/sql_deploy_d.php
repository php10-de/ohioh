<?php 
    
$modul="sql_deploy";

require("inc/req.php");

// For Administrators only
GRGR(3);
//Form Hook After Group
/*** Validation ***/

// Sql_deploy_id
validate('sql_deploy_id', 'int nullable' );
$_SESSION[$modul]['sql_deploy_id'] = $_VALID[' sql_deploy_id'];

// Filename
validate('filename', 'string' );
$_SESSION[$modul]['filename'] = $_VALID[' filename'];

// Hroses
validate('hroses', 'string' );
$_SESSION[$modul]['hroses'] = $_VALID[' hroses'];

// Status
validate('status', 'string nullable' );
$_SESSION[$modul]['status'] = $_VALID[' status'];

// Deployed_date
validate('deployed_date', 'string nullable' );
$_SESSION[$modul]['deployed_date'] = $_VALID[' deployed_date'];
if (isset($_REQUEST['submitted']) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}

if (isset($_REQUEST['submitted']) AND !$error) {
    $checkSql = "SELECT 1 FROM sql_deploy WHERE sql_deploy_id = " . (int) $_REQUEST['sql_deploy_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	$sql = "UPDATE sql_deploy SET filename = "
    .$_VALIDDB['filename']
     . ",hroses = " . $_VALIDDB['hroses']
     . ",status = " . $_VALIDDB['status']
     . ",deployed_date = " . $_VALIDDB['deployed_date']
    . " WHERE sql_deploy_id = " . (int) $_REQUEST['sql_deploy_id'];
    /*** after sql_deploy update ***/
    mysqli_query($con, $sql) or die('DB Update Error');
    
    } else {
    
	$sql = "INSERT INTO sql_deploy(sql_deploy_id, filename, hroses, status, deployed_date) VALUES("
    .$_VALIDDB['sql_deploy_id']
    . "," . $_VALIDDB['filename']
    . "," . $_VALIDDB['hroses']
    . "," . $_VALIDDB['status']
    . "," . $_VALIDDB['deployed_date']
    . ") ";
    mysqli_query($con, $sql) or die('DB Insert Error');
    /*** after sql_deploy insert ***/
    }
    header('Location: sql_deploy.php?ok=Done');
    exit;
}

if ($_REQUEST['sql_deploy_id']) {
	$sql = "SELECT * FROM sql_deploy WHERE sql_deploy_id = " . (int) $_REQUEST['sql_deploy_id'];
	$data = mysqli_fetch_assoc(mysqli_query($con, $sql));
    foreach ($data as $key => $value) {
        $_VALID[$key] = $value;
    }
}
// manuelle Eingabe überschreibt DB-Werte
if (isset($_REQUEST['submitted'])) {
    foreach ($_VALID as $key => $value) {
        $_VALID[$key] = $value;
    }
}
$n4a['sql_deploy.php'] = ss('Back to List');
require("inc/header.inc.php");

if ($error) {
	$headerError = implode('<br>', $error);
}
?>
<a href="javascript:void(0)" onClick="window.location.href = 'sql_deploy.php'"><img alt="<?php sss('Back to List')?>" title="<?php sss('Back to List')?>" src="css/icon/align_just_icon&16.png" class="listmenuicon"></a><br><br>

<div class="contentheadline"><?php echo ss('Sql_deploy')?></div>
<br>
<div class="contenttext">
<form id="formsql_deploy" name="formsql_deploy" method="post" class="formLayout">
<?php if($id) {
  echo '<input type="hidden" name="id" value="'.$id.'">';
}?>

<label for="filename"><?php echo ss('Filename')?></label>
<input type="text" name="filename" id="filename" value="<?php echo ss($_VALID['filename'])?>" required="required" />
<?php if ($error['filename']) echo $error['filename'] . ''?>
<br>
<label for="hroses"><?php echo ss('Hroses')?></label>
<input type="text" name="hroses" id="hroses" value="<?php echo ss($_VALID['hroses'])?>" required="required" />
<?php if ($error['hroses']) echo $error['hroses'] . ''?>
<br>
<label for="status"><?php echo ss('Status')?></label>
<input type="text" name="status" id="status" value="<?php echo ss($_VALID['status'])?>" />
<?php if ($error['status']) echo $error['status'] . ''?>
<br>
<label for="deployed_date"><?php echo ss('Deployed_date')?></label>
<input type="text" name="deployed_date" id="deployed_date" value="<?php echo ss($_VALID['deployed_date'])?>" />
<?php if ($error['deployed_date']) echo $error['deployed_date'] . ''?>
<br>
<input type="hidden" name="submitted" value="submitted">
<input type="submit" id="submit" value="<?php echo ss('Submit')?>">
<!-- After submit -->
</form>
<!-- after sql_deploy detail form -->
</div>
<?php
require("inc/footer.inc.php"); 
    ?>