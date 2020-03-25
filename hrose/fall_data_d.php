<?php 
    
$modul="fall_data";

require("inc/req.php");

// Generally for people with the right to edit fall_data
$groupID = 26;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'fall_data/fall_data.php')) {
    require MODULE_ROOT.'fall_data/fall_data.php';
}
//Form Hook After Group

validate('i', 'int nullable');

/*** Validation ***/

// Fall_data_id
validate('fall_data_id', 'int nullable' );

// Fall_id
validate('fall_id', 'int' );

// Lat
validate('lat', 'numeric' );

// Lon
validate('lon', 'numeric' );

// Accuracy
validate('accuracy', 'int' );

// Timestamp
validate('timestamp', 'string' );
if (isset($_REQUEST['submitted']) AND is_array($_MISSING) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}

if (isset($_REQUEST['submitted']) AND !$error) {
    $checkSql = "SELECT 1 FROM fall_data WHERE fall_data_id = " . (int) $_REQUEST['fall_data_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	$sql = "UPDATE fall_data SET fall_id = "
    .$_VALIDDB['fall_id']
     . ",lat = " . $_VALIDDB['lat']
     . ",lon = " . $_VALIDDB['lon']
     . ",accuracy = " . $_VALIDDB['accuracy']
     . ",timestamp = " . $_VALIDDB['timestamp']
    . " WHERE fall_data_id = " . (int) $_REQUEST['fall_data_id'];
    /*** after fall_data update ***/
    mysqli_query($con, $sql) or die('DB Update Error');
    
    } else {
    
	$sql = "INSERT INTO fall_data(fall_data_id, fall_id, lat, lon, accuracy, timestamp) VALUES("
    .$_VALIDDB['fall_data_id']
    . ",
	" . $_VALIDDB['fall_id']
    . ",
	" . $_VALIDDB['lat']
    . ",
	" . $_VALIDDB['lon']
    . ",
	" . $_VALIDDB['accuracy']
    . ",
	" . $_VALIDDB['timestamp']
    . ") ";
    mysqli_query($con, $sql) or die('DB Insert Error');
    $_VALID['fall_data_id'] = mysqli_insert_id($con);
    /*** after fall_data insert ***/
    }
    header('Location: fall_data.php?ok=Done');
    exit;
}

if ($_REQUEST['fall_data_id']) {
	$sql = "SELECT * FROM fall_data WHERE fall_data_id = " . (int) $_REQUEST['fall_data_id'];
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
$n4a['fall_data.php'] = ss('Back to List');
require("inc/header.inc.php");

if ($error) {
	$headerError = implode('<br>', $error);
}
?>
<a href="javascript:void(0)" onClick="window.location.href = 'fall_data.php'"><img alt="<?php sss('Back to List')?>" title="<?php sss('Back to List')?>" src="css/icon/align_just_icon&16.png" class="listmenuicon"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['fall_data_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;fall_data_id='.$prevEntry[$modul.'_id'].'"><img src="css/icon/br_prev_icon&16.png" title="' . ss('Previous') . '"></a>';
    } else {
        echo '&nbsp;<span style="margin:8px">&nbsp;</span>';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;fall_data_id='.$nextEntry[$modul.'_id'].'"><img src="css/icon/br_next_icon&16.png" title="' . ss('Next') . '"></a>';
    }
}?>
<br><br><div class="contentheadline"><?php echo ss('Fall_data')?></div>
<br>
<div class="contenttext">
<form id="formfall_data" name="formfall_data" method="post" class="formLayout" >
<?php if($id) {
  echo '<input type="hidden" name="id" value="'.$id.'">';
}?>

<label for="fall_id"><?php echo ss('Fall_id')?></label>
<input type="text" name="fall_id" id="fall_id" value="<?php echo $_VALID['fall_id']?>" required="required" />
<?php if (isset($error['fall_id'])) echo $error['fall_id'] . ''?>
<br>
<label for="lat"><?php echo ss('Lat')?></label>
<input type="string" name="lat" id="lat" value="<?php echo number_format($_VALID['lat'],2,",","")?>" required="required" />
<?php if (isset($error['lat'])) echo $error['lat'] . ''?>
<br>
<label for="lon"><?php echo ss('Lon')?></label>
<input type="string" name="lon" id="lon" value="<?php echo number_format($_VALID['lon'],2,",","")?>" required="required" />
<?php if (isset($error['lon'])) echo $error['lon'] . ''?>
<br>
<label for="accuracy"><?php echo ss('Accuracy')?></label>
<input type="text" name="accuracy" id="accuracy" value="<?php echo $_VALID['accuracy']?>" required="required" />
<?php if (isset($error['accuracy'])) echo $error['accuracy'] . ''?>
<br>
<label for="timestamp"><?php echo ss('Timestamp')?></label>
<input type="text" name="timestamp" id="timestamp" value="<?php echo ss($_VALID['timestamp'])?>" required="required" />
<?php if (isset($error['timestamp'])) echo $error['timestamp'] . ''?>
<br>
<input type="hidden" name="submitted" value="submitted">
<input type="submit" id="submit" value="<?php echo ss('Submit')?>">
<!-- After submit -->
</form>
<!-- after fall_data detail form -->
</div>
<?php
require("inc/footer.inc.php"); 
    ?>