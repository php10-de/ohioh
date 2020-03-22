<?php
$modul="dict";

require("inc/req.php");

/*** Rights ***/
// Generally for people in the 'Translation' group
GRGR(5);

/*** Validation ***/

// Dict_id
validate('dict_id', 'int');

// ID
validate('ID', 'string');

// Gr_id
validate('gr_id', 'int nullable');

// De
validate('de', 'string nullable');

// En
validate('en', 'string nullable');

// Gr
validate('gr', 'string nullable');

if (isset($_REQUEST['submitted'])) {
if (!$_REQUEST['ID']) {
    $error[] = ss('Some mandatory fields are missing');
}
if (!$error) {
    $sql = "INSERT INTO dict(ID, gr_id, de, en, gr) VALUES("
     . $_VALIDDB['ID']
    . ", " . $_VALIDDB['gr_id']
    . ", " . $_VALIDDB['de']
    . ", " . $_VALIDDB['en']
    . ", " . $_VALIDDB['gr']
    . ")";
    mysqli_query($con, $sql) or die('DB Insert Error');
    header('Location: dict.php?ok=Done&refresh');
    exit;
}

} else if ($_REQUEST['dict_id']) {
    $sql = "SELECT * FROM dict WHERE dict_id = " . (int) $_REQUEST['dict_id'];
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
$n4a['dict.php'] = ss('Back to List');
require("inc/header.inc.php");

if ($error) {
    $headerError = implode('<br>', $error);
}
?>
<a href="javascript:void(0)" onClick="window.location.href = 'dict.php'"><img alt="<?php sss('Back to List')?>" title="<?php sss('Back to List')?>" src="css/icon/align_just_icon&16.png" class="listmenuicon"></a><br><br>

<div class="contentheadline">Dict</div>
<br>
<div class="contenttext">
<form id="formdict" name="formdict" method="post" class="formLayout">
<?php if($id) {
  echo '<input type="hidden" name="id" value="'.$id.'">';
}?>

<label for="ID"><?php echo ss('ID')?></label>
<input type="text" name="ID" id="ID" value="<?php echo sss($_VALID['ID'])?>" required="required" />
<?php if ($error['ID']) echo $error['ID'] . ''?>
<br>
<label for="gr_id"><?php echo ss('Gr_id')?></label>
<select name="gr_id"><?php echo groupConvert($_VALID['gr_id'], true)?></select>
<?php if ($error['gr_id']) echo $error['gr_id'] . ''?>
<br>
<label for="de"><?php echo ss('De')?></label>
<input type="text" name="de" id="de" value="<?php echo sss($_VALID['de'])?>" />
<?php if ($error['de']) echo $error['de'] . ''?>
<br>
<label for="cn"><?php echo ss('Cn')?></label>
<input type="text" name="cn" id="cn" value="<?php echo sss($_VALID['cn'])?>" />
<?php if ($error['cn']) echo $error['cn'] . ''?>
<br>
<label for="en"><?php echo ss('En')?></label>
<input type="text" name="en" id="en" value="<?php echo sss($_VALID['en'])?>" />
<?php if ($error['en']) echo $error['en'] . ''?>
<br>
<label for="gr"><?php echo ss('Gr')?></label>
<input type="text" name="gr" id="gr" value="<?php echo sss($_VALID['gr'])?>" />
<?php if ($error['gr']) echo $error['gr'] . ''?>
<br>
<input type="hidden" name="submitted" value="submitted">
<input type="submit" id="submit" value="<?php echo ss('Submit')?>">
</form>
</div>
<?php
require("inc/footer.inc.php"); 