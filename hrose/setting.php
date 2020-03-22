<?php
$modul="settings";

require("inc/req.php");

/*** Rights ***/
// For Administrators only
GRGR(6);

if (isset($_POST['sent'])) {
    foreach ($_REQUEST['s'] as $key=>$value) {
        $sql = "UPDATE setting SET value = '".my_sql($value) . "' WHERE id='".my_sql($key)."' AND gr_id IN (".implode(',', $_SESSION['GROUP']).")";
        mysqli_query($con, $sql) or die(mysqli_error());
    }
}

// Ergebnis aufbauen und cachen ------- //
$sql="SELECT id, value FROM setting
      WHERE 1
      ORDER BY id DESC";
$listResult=mysqli_query($con, $sql);

// Cache
$s = '<?php ';
while ($row = mysqli_fetch_row($listResult)) {
    $setting[$row[0]] = $row[1];
    $s .= 'define(\''.$row[0].'\',\''.$row[1].'\'); ';
}
$s .= ' ?>';
file_put_contents('inc/settings.inc.php', $s);


// Ergebnis Gruppen-Rechte abhängig aufbauen ------- //
/*$sql="SELECT s.id, s.value FROM setting s
      WHERE 1=1";

/*** Filter ***/

/*** Order By **
$sql .= " ORDER BY id DESC";
$listResult=mysqli_query($con, $sql);
*/
$n4a['setting_d.php'] = ss('Add setting');
require("inc/header.inc.php");
?>
<a href="setting_d.php"><img alt="<?php sss('Add new entry')?>" title="<?php sss('Add setting')?>" src="css/icon/doc_empty_icon&16.png" class="listmenuicon"></a>&nbsp;
<a href="javascript:void(0)" onClick="$('#formsettings').submit();"><img alt="<?php sss('Save')?>" title="<?php sss('Save')?>" src="css/icon/save_icon&16.png" class="listmenuicon"></a><br><br>
<div class="contentheadline"><?php sss('Settings')?></div>
<br>
<div class="contenttext">
  <form id="formsettings" method="POST">
    <input type="hidden" name="sent" value="1">
    <table cellspacing="0" cellpadding="0" class="bw">
    <?php
    foreach ($setting as $key => $value) {
        echo '<tr class="dotted" id="tr_'.$key.'">';
        echo '<td width="470">' . $key .'</td>';
        echo '<td align="right"><input type="text" value="' . $value.'" name="s[' . $key.']">&nbsp;
        <a href="#" onclick="if (confirm(\''.ss('Do you really want to delete it?').'\')) delRow(\''.$key.'\');"><img src="css/icon/delete_icon&16.png" title="'.ss('Delete').'"></a></td>';
        echo '</tr>';
    }?>

    </table>
  </form>

<?php if($err!="") {
    echo '<br><span class="red">'.$err.'</span>';
}
?>
</div>

<script type="text/javascript">
    function delRow(pk) {
        $.ajax({
          url: 'a/setting_del.php?id='+pk
        });
        $('#tr_'+pk).hide();
    }
</script>
<?php
require("inc/footer.inc.php");
?>