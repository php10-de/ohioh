<?php
$modul="nav";

require("inc/req.php");

/*** Rights ***/
// Generally for people in the management group
GRGR(6);

$n4a['nav_d.php'] = ss('Add menu entry');
require("inc/header.inc.php");

// Ergebnis aufbauen ------- //
$sql="SELECT * FROM nav WHERE 1=1";

/*** Filter ***/

/*** Order By ***/
$sql .= " ORDER BY name";
$listResult=mysqli_query($con, $sql);
$num_rows = mysqli_num_rows($listResult);

?>
<!--<a href="gr_d.php"><img alt="<?php sss('Add group')?>" title="<?php sss('Add new entry')?>" src="css/icon/doc_empty_icon&16.png" class="listmenuicon"></a><br><br>-->
<div class="contentheadline"><?php sss('Group')?></div>
<br>
<div class="contenttext">
<table cellspacing="0" cellpadding="0" class="bw">
<?php
while($row=mysqli_fetch_array($listResult)) {
    echo '<tr class="dotted" id="tr_'.$row['gr_id'].'">';
    echo '<td width="470">' . $row['shortname'].(($row['longname'])?' (' . $row['longname'].')':'').'</td>';
    echo '<td align="right"><a href="gr_d.php?id='.$row['gr_id'].'"><img src="css/icon/pencil_icon&16.png" title="'.ss('Edit').'"></a>&nbsp;<a href="#" onclick="if (confirm(\''.ss('Do you really want to delete it?').'\')) delRow('.$row['gr_id'].');"><img src="css/icon/delete_icon&16.png" title="'.ss('Delete').'"></a></td>';
    echo '</tr>';
}?>

</table>
</div>
<?php
require("inc/footer.inc.php");

?>

<script type="text/javascript">
    function delRow(pk) {
        $.ajax({
          url: 'a/gr_del.php?id='+pk
        });
        $('#tr_'+pk).hide();
    }
</script>