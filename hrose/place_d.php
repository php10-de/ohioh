<?php 
    
$modul="place";

require("inc/req.php");

// Generally for people with the right to edit place
$groupID = 26;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'place/place.php')) {
    require MODULE_ROOT.'place/place.php';
}
//Form Hook After Group

validate('i', 'int nullable');

/*** Validation ***/

// Place_id
validate('place_id', 'int nullable' );

// Name
validate('name', 'string' );

// Lat
validate('lat', 'numeric' );

// Lon
validate('lon', 'numeric' );

// Qr_code
validate('qr_code', 'string nullable' );
if (isset($_REQUEST['submitted']) AND is_array($_MISSING) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}

if (isset($_REQUEST['submitted']) AND !$error) {
    $checkSql = "SELECT 1 FROM place WHERE place_id = " . (int) $_REQUEST['place_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	$sql = "UPDATE place SET name = "
    .$_VALIDDB['name']
     . ",lat = " . $_VALIDDB['lat']
     . ",lon = " . $_VALIDDB['lon']
     . ",qr_code = " . $_VALIDDB['qr_code']
    . " WHERE place_id = " . (int) $_REQUEST['place_id'];
    /*** after place update ***/
    mysqli_query($con, $sql) or die('DB Update Error');
    
    } else {
    
	$sql = "INSERT INTO place(place_id, name, lat, lon, qr_code) VALUES("
    .$_VALIDDB['place_id']
    . ",
	" . $_VALIDDB['name']
    . ",
	" . $_VALIDDB['lat']
    . ",
	" . $_VALIDDB['lon']
    . ",
	" . $_VALIDDB['qr_code']
    . ") ";
    mysqli_query($con, $sql) or die('DB Insert Error');
    $_VALID['place_id'] = mysqli_insert_id($con);
    /*** after place insert ***/
    }
    header('Location: place.php?ok=Done');
    exit;
}

if ($_REQUEST['place_id']) {
	$sql = "SELECT * FROM place WHERE place_id = " . (int) $_REQUEST['place_id'];
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
$n4a['place.php'] = ss('Back to List');
require("inc/header.inc.php");

if ($error) {
	$headerError = implode('<br>', $error);
}
?>
<a href="javascript:void(0)" onClick="window.location.href = 'place.php'"><img alt="<?php sss('Back to List')?>" title="<?php sss('Back to List')?>" src="css/icon/align_just_icon&16.png" class="listmenuicon"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['place_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;place_id='.$prevEntry[$modul.'_id'].'"><img src="css/icon/br_prev_icon&16.png" title="' . ss('Previous') . '"></a>';
    } else {
        echo '&nbsp;<span style="margin:8px">&nbsp;</span>';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;place_id='.$nextEntry[$modul.'_id'].'"><img src="css/icon/br_next_icon&16.png" title="' . ss('Next') . '"></a>';
    }
}?>
<br><br><div class="contentheadline"><?php echo ss('Place')?></div>
<br>
<div class="contenttext">
<form id="formplace" name="formplace" method="post" class="formLayout" >
<?php if($id) {
  echo '<input type="hidden" name="id" value="'.$id.'">';
}?>

<label for="name"><?php echo ss('Name')?></label>
<input type="text" name="name" id="name" value="<?php echo ss($_VALID['name'])?>" required="required" />
<?php if (isset($error['name'])) echo $error['name'] . ''?>
<br>
<label for="lat"><?php echo ss('Lat')?></label>
<input type="string" name="lat" id="lat" value="<?php echo number_format($_VALID['lat'],2,",","")?>" required="required" />
<?php if (isset($error['lat'])) echo $error['lat'] . ''?>
<br>
<label for="lon"><?php echo ss('Lon')?></label>
<input type="string" name="lon" id="lon" value="<?php echo number_format($_VALID['lon'],2,",","")?>" required="required" />
<?php if (isset($error['lon'])) echo $error['lon'] . ''?>
<br>
<label for="qr_code"><?php echo ss('Qr_code')?></label>
<input type="text" name="qr_code" id="qr_code" value="<?php echo ss($_VALID['qr_code'])?>" />
<?php if (isset($error['qr_code'])) echo $error['qr_code'] . ''?>
<br>
<input type="hidden" name="submitted" value="submitted">
<input type="submit" id="submit" value="<?php echo ss('Submit')?>">
<!-- After submit -->
</form>
<!-- after place detail form -->
</div>
<?php
require("inc/footer.inc.php"); 
    ?>