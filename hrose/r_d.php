<?php
$modul="r";

require("inc/req.php");

validate("shortname","string");
validate("longname","string nullable");
validate("id","int");
$id = $_VALID['id'];

/*** Rights ***/
// Generally for people with right do change rights
RR(4);

if (!$id) {
    if(isset($_REQUEST['submitted'])) {
        if (!$_VALID['shortname']) {
            $headerError = ss('Some mandatory fields are missing');
        } else {
            $sql = "INSERT INTO r(shortname, longname)
                    VALUES (".$_VALIDDB['shortname'].",".$_VALIDDB['longname'].")";
            $res = mysqli_query($con, $sql);
            $id = mysqli_insert_id($con);
            if ($id) {
                foreach ($_VALID as $key => $value) {
                    $data[$key] = $value;
                }
            }
            header('Location: r.php?ok=Done');
        }
    }
} else {
    if (isset($_REQUEST['submitted'])) {
        if (!$_VALID['shortname']) {
            $headerError = ss('Some mandatory fields are missing');
        } else {
            $sql = "UPDATE r SET shortname=".$_VALIDDB['shortname'].",
                longname = ".$_VALIDDB['longname']." WHERE right_id=".$id;
            mysqli_query($con, $sql);
            header('Location: r.php?ok=Done');
        }

    }
    $sql = "SELECT * FROM r WHERE right_id=".$id . " LIMIT 0,1";
    $res = mysqli_query($con, $sql);
    $data = mysqli_fetch_array($res);
    
}

// manuelle Eingabe überschreibt DB-Werte
if (isset($_VALID['submitted'])) {
    foreach ($_VALID as $key => $value) {
        if (isset($data[$key])) $data[$key] = $value;
    }
}

$n4a['r.php'] = ss('Back to rights list');
require("inc/header.inc.php");
?>
<!--<a href="javascript:void(0)" onClick="window.location.href = '<?php echo $modul?>.php'"><img alt="<?php sss('Back to List')?>" title="<?php sss('Back to List')?>" src="css/icon/align_just_icon&16.png" class="listmenuicon"></a><br><br>-->
<div class="contentheadline"><?php sss('Right')?></div>
<br>
<div class="contenttext">
  <form name="form<?php echo $modul?>" class="formLayout">
  <?php if($_VALID['id']) {
      echo '<input type="hidden" name="id" value="'.$_VALID['id'].'">';
  }?>
    <label for="shortname"><?php echo ss('Shortname')?></label>
    <input type="text" name="shortname" id="shortname" value="<?php echo sss($data['shortname'])?>" required="required" />
    <br>
    <label for="longname"><?php echo ss('Description')?></label>
    <input type="text" name="longname" id="longname" value="<?php echo sss($data['longname'])?>" />

    <br>
    <input type="hidden" name="submitted" value="submitted">
    <input type="submit" id="submit" value="<?php echo ss('Save')?>">

  </form><br><br>


<?php if($err!="") {
    echo '<br><span class="red">'.$err.'</span>';
}
?>
</div>
<?php
require("inc/footer.inc.php");
?>