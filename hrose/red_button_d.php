<?php 
    
$modul="red_button";

require("inc/req.php");

// Generally for people with the right to edit red_button
GRGR(3);
//Form Hook After Group

validate('i', 'int nullable');

/*** Validation ***/

// Red_button_id
validate('red_button_id', 'int nullable' );

// Tablename
validate('tablename', 'string' );

// Replace_from
validate('replace_from', 'string' );

// Replace_to
validate('replace_to', 'string' );

// Is_config
validate('is_config', 'ckb' );

// Is_active
validate('is_active', 'ckb nullable' );

// Error
validate('error', 'string nullable' );
if (isset($_REQUEST['submitted']) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}

if (isset($_REQUEST['submitted']) AND !$error) {
    $checkSql = "SELECT 1 FROM red_button WHERE red_button_id = " . (int) $_REQUEST['red_button_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	$sql = "UPDATE red_button SET tablename = "
    .$_VALIDDB['tablename']
     . ",replace_from = " . $_VALIDDB['replace_from']
     . ",replace_to = " . $_VALIDDB['replace_to']
     . ",is_config = " . $_VALIDDB['is_config']
     . ",is_active = " . $_VALIDDB['is_active']
     . ",error = " . $_VALIDDB['error']
    . " WHERE red_button_id = " . (int) $_REQUEST['red_button_id'];
    /*** after red_button update ***/
    mysqli_query($con, $sql) or die('DB Update Error');
    
    } else {
    
	$sql = "INSERT INTO red_button(red_button_id, tablename, replace_from, replace_to, is_config, is_active, error) VALUES("
    .$_VALIDDB['red_button_id']
    . ",
" . $_VALIDDB['tablename']
    . ",
" . $_VALIDDB['replace_from']
    . ",
" . $_VALIDDB['replace_to']
    . ",
" . $_VALIDDB['is_config']
    . ",
" . $_VALIDDB['is_active']
    . ",
" . $_VALIDDB['error']
    . ") ";
    mysqli_query($con, $sql) or die('DB Insert Error');
    /*** after red_button insert ***/
    }
    header('Location: red_button.php?ok=Done');
    exit;
}

if ($_REQUEST['red_button_id']) {
	$sql = "SELECT * FROM red_button WHERE red_button_id = " . (int) $_REQUEST['red_button_id'];
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
$n4a['red_button.php'] = ss('Back to List');
require("inc/header.inc.php");

if ($error) {
	$headerError = implode('<br>', $error);
}
?>
<a href="javascript:void(0)" onClick="window.location.href = 'red_button.php'"><img alt="<?php sss('Back to List')?>" title="<?php sss('Back to List')?>" src="css/icon/align_just_icon&16.png" class="listmenuicon"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['red_button_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;red_button_id='.$prevEntry[$modul.'_id'].'"><img src="css/icon/br_prev_icon&16.png" title="' . ss('Previous') . '"></a>';
    } else {
        echo '&nbsp;<span style="margin:8px">&nbsp;</span>';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;red_button_id='.$nextEntry[$modul.'_id'].'"><img src="css/icon/br_next_icon&16.png" title="' . ss('Next') . '"></a>';
    }
}?>
<br><br><div class="contentheadline"><?php echo ss('Red_button')?></div>
<br>
<div class="contenttext">
<form id="formred_button" name="formred_button" method="post" class="formLayout">
<?php if($id) {
  echo '<input type="hidden" name="id" value="'.$id.'">';
}?>

<label for="tablename"><?php echo ss('Tablename')?></label>
<input type="text" name="tablename" id="tablename" value="<?php echo ss($_VALID['tablename'])?>" required="required" />
<?php if ($error['tablename']) echo $error['tablename'] . ''?>
<br>
<label for="replace_from"><?php echo ss('Replace_from')?></label>
<textarea name="replace_from" id="replace_from"><?php echo ss($_VALID['replace_from'])?></textarea>
<?php if ($error['replace_from']) echo $error['replace_from'] . ''?>
<br>
<label for="replace_to"><?php echo ss('Replace_to')?></label>
<textarea name="replace_to" id="replace_to"><?php echo ss($_VALID['replace_to'])?></textarea>
<?php if ($error['replace_to']) echo $error['replace_to'] . ''?>
<br>
<label for="is_config"><?php echo ss('Is_config')?></label>
<input type="checkbox" name="is_config" id="is_config" value="1" <?php echo ($_VALID['is_config'])?'checked="checked"':''?> />
<?php if ($error['is_config']) echo $error['is_config'] . ''?>
<br>
<label for="is_active"><?php echo ss('Is_active')?></label>
<input type="checkbox" name="is_active" id="is_active" value="1" <?php echo ($_VALID['is_active'])?'checked="checked"':''?> />
<?php if ($error['is_active']) echo $error['is_active'] . ''?>
<br>
<label for="error"><?php echo ss('Error')?></label>
<input type="text" name="error" id="error" value="<?php echo ss($_VALID['error'])?>" />
<?php if ($error['error']) echo $error['error'] . ''?>
<br>
<input type="hidden" name="submitted" value="submitted">
<input type="submit" id="submit" value="<?php echo ss('Submit')?>">
<!-- After submit -->
</form>
<!-- after red_button detail form -->
</div>
<?php
require("inc/footer.inc.php"); 
    ?>