<?php
$modul="nav";

require("inc/req.php");

// For Administrators only
GRGR(1);

/*** Validation ***/
// index
validate('i', 'int');

// Nav_id
validate('id', 'int');
$id = $_VALID['id'];

// To_nav_id
validate('to_nav_id', 'int nullable');

// Gr_id
validate('gr_id', 'int nullable');

// Level
validate('level', 'int');

// Name
validate('name', 'string');

// Link
validate('link', 'string nullable');

// Params
validate('params', 'string');

// Icon
validate('icon', 'string nullable');

if (isset($_REQUEST['submitted'])) {
if (!($_VALID['level']) OR !($_VALID['name']) OR !($_VALID['link'])) {
    $error[] = ss('Some mandatory fields are missing');
}
if (!$error) {
    if ($id) {
        $sql = "REPLACE INTO nav(nav_id, to_nav_id, gr_id, level, name, link, params, icon) VALUES("
        . $_VALID['id']
        . ", " .  $_VALIDDB['to_nav_id']
        . ", " .  $_VALIDDB['gr_id']
        . ", " .  $_VALIDDB['level']
        . ", '" . $_VALID['name']
        . "', '" . $_VALID['link']
        . "', '" . $_VALID['params']
        . "', '" . $_VALID['icon']
        . "')";
    } else {
        $sql = "INSERT INTO nav(to_nav_id, gr_id, level, name, link, params, icon) VALUES("
        . $_VALIDDB['to_nav_id']
        . ", " .  $_VALIDDB['gr_id']
        . ", " .  $_VALIDDB['level']
        . ", '" . $_VALID['name']
        . "', '" . $_VALID['link']
        . "', '" . $_VALID['params']
        . "', '" . $_VALID['icon']
        . "')";
    }
    mysqli_query($con, $sql) or die('DB Insert Error');
    header('Location: nav.php?rn&ok=Done');
    exit;
}

} else if ($id) {
    $sql = "SELECT * FROM nav WHERE nav_id = " . (int) ($_VALID['id']);
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
$n4a['nav.php'] = ss('Back to List');
require("inc/header.inc.php");

if ($error) {
    $headerError = implode('<br>', $error);
}
?>
<a href="javascript:void(0)" onClick="window.location.href = 'nav.php'"><img alt="<?php sss('Back to List')?>" title="<?php sss('Back to List')?>" src="css/icon/align_just_icon&16.png" class="listmenuicon"></a>
<?php
/*** Pagination ***/
if ($id) {
    $pageResult = getMemCache($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;id='.$prevEntry[$modul.'_id'].'"><img src="css/icon/br_prev_icon&16.png" title="'.ss('Previous').'"></a>';
    } else {
        echo '&nbsp;<span style="margin:8px">&nbsp;</span>';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;id='.$nextEntry[$modul.'_id'].'"><img src="css/icon/br_next_icon&16.png" title="'.ss('Next').'"></a>';
    }

    echo '<br><br><div class="contentheadline">' . ss($_VALID['shortname']).'</div><br>';
}?>
<br>
<div class="contenttext">
<form id="formnav" name="formnav" method="post" class="formLayout">
<?php if($id) {
  echo '<input type="hidden" name="id" value="'.$id.'">';
}?>

<label for="gr_id"><?php echo ss('Group')?></label>
<select name="gr_id"><?php echo groupConvert($_VALID['gr_id'], true)?></select>
<?php if ($error['gr_id']) echo $error['gr_id'] . ''?>
<br>
<label for="to_nav_id"><?php echo ss('Parent')?></label>
<select name="to_nav_id"><?php echo navConvert($_VALID['to_nav_id'], true)?></select>
<?php if ($error['to_nav_id']) echo $error['to_nav_id'] . ''?>
<br>
<label for="level"><?php echo ss('Level')?></label>
<input type="text" name="level" id="level" value="<?php echo $_VALID['level']?>" required="required" />
<?php if ($error['level']) echo $error['level'] . ''?>
<br>
<label for="name"><?php echo ss('Name')?></label>
<input type="text" name="name" id="name" value="<?php echo sss($_VALID['name'])?>" required="required" />
<?php if ($error['name']) echo $error['name'] . ''?>
<br>
<label for="link"><?php echo ss('Link')?></label>
<input type="text" name="link" id="link" value="<?php echo sss($_VALID['link'])?>" required="required" />
<?php if ($error['link']) echo $error['link'] . ''?>
<br>
<label for="params"><?php echo ss('Params')?></label>
<input type="text" name="params" id="params" value="<?php echo sss($_VALID['params'])?>" />
<?php if ($error['params']) echo $error['params'] . ''?>
<br>
<label for="icon"><?php echo ss('Icon')?></label>
<input type="text" name="icon" id="icon" value="<?php echo sss($_VALID['icon'])?>" />
<?php if ($error['icon']) echo $error['icon'] . ''?>
<br>
<input type="hidden" name="submitted" value="submitted">
<input type="submit" id="submit" value="<?php echo ss('Submit')?>">
</form>
</div>
<?php
require("inc/footer.inc.php");
?> 