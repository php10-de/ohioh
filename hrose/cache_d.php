<?php 
    
$modul="cache";

require("inc/req.php");

// For Administrators only
RR(2);

/*** Validation ***/

// Cache_id
validate('cache_id', 'int nullable' );
$_SESSION['cache_id'] = $_VALID[' cache_id'];

// Url
validate('url', 'string' );
$_SESSION['url'] = $_VALID[' url'];

// Updated
validate('updated', 'string nullable' );
$_SESSION['updated'] = $_VALID[' updated'];

// Active
validate('active', 'int nullable' );
$_SESSION['active'] = $_VALID[' active'];
if (isset($_REQUEST['submitted']) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}

if (isset($_REQUEST['submitted']) AND !$error) {
    $checkSql = "SELECT 1 FROM cache WHERE cache_id = " . (int) $_REQUEST['cache_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	$sql = "UPDATE cache SET url = "
    .$_VALIDDB['url']
     . ",updated = " . $_VALIDDB['updated']
     . ",active = " . $_VALIDDB['active']
    . " WHERE cache_id = " . (int) $_REQUEST['cache_id'];
    /*** after cache update ***/
    mysqli_query($con, $sql) or die('DB Update Error');
    
    } else {
    
	$sql = "INSERT INTO cache(cache_id, url, updated, active) VALUES("
    .$_VALIDDB['cache_id']
    . "," . $_VALIDDB['url']
    . "," . $_VALIDDB['updated']
    . "," . $_VALIDDB['active']
    . ") ";
    mysqli_query($con, $sql) or die('DB Insert Error');
    /*** after cache insert ***/
    }
    header('Location: cache.php?ok=Done');
    exit;
}

if ($_REQUEST['cache_id']) {
	$sql = "SELECT * FROM cache WHERE cache_id = " . (int) $_REQUEST['cache_id'];
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
$n4a['cache.php'] = ss('Back to List');
require("inc/header.inc.php");

if ($error) {
	$headerError = implode('<br>', $error);
}
?>
<a href="javascript:void(0)" onClick="window.location.href = 'cache.php'"><img alt="<?php sss('Back to List')?>" title="<?php sss('Back to List')?>" src="css/icon/align_just_icon&16.png" class="listmenuicon"></a><br><br>

<div class="contentheadline">Cache</div>
<br>
<div class="contenttext">
<form id="formcache" name="formcache" method="post" class="formLayout">
<?php if($id) {
  echo '<input type="hidden" name="id" value="'.$id.'">';
}?>

<label for="url"><?php echo ss('Url')?></label>
<input type="text" name="url" id="url" value="<?php echo sss($_VALID['url'])?>" required="required" />
<?php if ($error['url']) echo $error['url'] . ''?>
<br>
<label for="updated"><?php echo ss('Updated')?></label>
<input type="text" name="updated" id="updated" value="<?php echo sss($_VALID['updated'])?>" />
<?php if ($error['updated']) echo $error['updated'] . ''?>
<br>
<label for="active"><?php echo ss('Active')?></label>
<input type="text" name="active" id="active" value="<?php echo $_VALID['active']?>" />
<?php if ($error['active']) echo $error['active'] . ''?>
<br>
<input type="hidden" name="submitted" value="submitted">
<input type="submit" id="submit" value="<?php echo ss('Submit')?>">
</form>
<!-- after cache detail form -->
</div>
<?php
require("inc/footer.inc.php"); 
    ?>