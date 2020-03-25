<?php 
    
$modul="fall";

require("inc/req.php");

// Generally for people with the right to edit fall
$groupID = 26;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'fall/fall.php')) {
    require MODULE_ROOT.'fall/fall.php';
}
//Form Hook After Group

validate('i', 'int nullable');

/*** Validation ***/

// Fall_id
validate('fall_id', 'int nullable' );

// Transfer_date
validate('transfer_date', 'string' );
if (isset($_REQUEST['submitted']) AND is_array($_MISSING) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}

if (isset($_REQUEST['submitted']) AND !$error) {
    $checkSql = "SELECT 1 FROM fall WHERE fall_id = " . (int) $_REQUEST['fall_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	$sql = "UPDATE fall SET transfer_date = "
    .$_VALIDDB['transfer_date']
    . " WHERE fall_id = " . (int) $_REQUEST['fall_id'];
    /*** after fall update ***/
    mysqli_query($con, $sql) or die('DB Update Error');
    
    } else {
    
	$sql = "INSERT INTO fall(fall_id, transfer_date) VALUES("
    .$_VALIDDB['fall_id']
    . ",
	" . $_VALIDDB['transfer_date']
    . ") ";
    mysqli_query($con, $sql) or die('DB Insert Error');
    $_VALID['fall_id'] = mysqli_insert_id($con);
    /*** after fall insert ***/
    }
    header('Location: fall.php?ok=Done');
    exit;
}

if ($_REQUEST['fall_id']) {
	$sql = "SELECT * FROM fall WHERE fall_id = " . (int) $_REQUEST['fall_id'];
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
$n4a['fall.php'] = ss('Back to List');
require("inc/header.inc.php");

if ($error) {
	$headerError = implode('<br>', $error);
}
?>
<a href="javascript:void(0)" onClick="window.location.href = 'fall.php'"><img alt="<?php sss('Back to List')?>" title="<?php sss('Back to List')?>" src="css/icon/align_just_icon&16.png" class="listmenuicon"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['fall_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;fall_id='.$prevEntry[$modul.'_id'].'"><img src="css/icon/br_prev_icon&16.png" title="' . ss('Previous') . '"></a>';
    } else {
        echo '&nbsp;<span style="margin:8px">&nbsp;</span>';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;fall_id='.$nextEntry[$modul.'_id'].'"><img src="css/icon/br_next_icon&16.png" title="' . ss('Next') . '"></a>';
    }
}?>
<br><br><div class="contentheadline"><?php echo ss('Fall')?></div>
<br>
<div class="contenttext">
<form id="formfall" name="formfall" method="post" class="formLayout" >
<?php if($id) {
  echo '<input type="hidden" name="id" value="'.$id.'">';
}?>

<label for="transfer_date"><?php echo ss('Transfer_date')?></label>
<input type="text" name="transfer_date" id="transfer_date" value="<?php echo ss($_VALID['transfer_date'])?>" required="required" />
<?php if (isset($error['transfer_date'])) echo $error['transfer_date'] . ''?>
<br>
<input type="hidden" name="submitted" value="submitted">
<input type="submit" id="submit" value="<?php echo ss('Submit')?>">
<!-- After submit -->
</form>
<!-- after fall detail form -->
</div>
<?php
require("inc/footer.inc.php"); 
    ?>