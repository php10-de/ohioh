<?php
$modul="gr";

require("inc/req.php");
validate('user_id','int');

/*** Rights ***/
// Generally for people with right to manage groups
RR(2);

$n4a['gr_d.php'] = ss('Add group');
$headless = (isset($_REQUEST['headless']))?true:false;
if (!$headless) require("inc/header.inc.php");

// Ergebnis aufbauen ------- //
$sql="SELECT gr.gr_id, gr.shortname, gr.longname";
if ($_VALID['user_id']) {
    $sql .= ", (
            SELECT 1
            FROM user2gr
            WHERE user2gr.user_id = ".$_VALID['user_id']."
                AND user2gr.gr_id = gr.gr_id
          ) AS yn";
}
$sql .= " FROM gr";


/*** Filter ***/
// Admin group for Administrators only
if (!GR(1)) {
    // Admin group for admins only
    $where[] = "gr.gr_id != 1";
}
$sql .= ' where ' . (($where) ? implode(" AND ", $where) : "1=1");

/*** Order By ***/
$sql .= " ORDER BY gr.shortname";
$_SESSION[$modul]['sql'] = $sql;
$listResult = getMemCache($sql);
// refresh memcache after saving (ok) or deleting (rl)
if (!$listResult || isset($_SESSION[$modul]['rl'])) {
    $r = mysqli_query($con, $sql) or die(mysqli_error());
    unset($listResult);
    while($row=mysqli_fetch_array($r))
        $listResult[]=$row;
    if ($memcache) {
        setMemCache($sql, $listResult);
    }
    unset($_SESSION[$modul]['rl']);
}

if (!$headless) {
?>
<!--<a href="gr_d.php"><img alt="<?php sss('Add group')?>" title="<?php sss('Add new entry')?>" src="css/icon/doc_empty_icon&16.png" class="listmenuicon"></a><br><br>-->
<div class="contentheadline"><?php sss('Group')?></div>
<br>
<div class="contenttext">
<table cellspacing="0" cellpadding="0" class="bw">
<?php
}
foreach ($listResult as $index => $row) {
    echo '<tr class="dotted" id="tr_'.$row['gr_id'].'">';
    echo '<td width="470" '.$mouseover.' onClick="location.href=\''.$modul.'_d.php?i='.$index.'&amp;id='.$row[$modul.'_id'].'\'">' . $row['shortname'].(($row['longname'])?' (' . $row['longname'].')':'').'</td>';
    echo '<td align="right">';
  if (!$_VALID['user_id']) {
    echo '<td align="right">
        <a href="gr_d.php?id='.$row['gr_id'].'"><img src="css/icon/pencil_icon&16.png" title="'.ss('Edit').'"></a>&nbsp;
        <a href="#" onclick="if (confirm(\''.ss('Do you really want to delete it?').'\')) delRow('.$row['gr_id'].');"><img src="css/icon/delete_icon&16.png" title="'.ss('Delete').'"></a>
         </td>';
  } else {

    echo '<td align="right">
        <img class="tick" id="grtick_'.$row['gr_id'].'" src="css/icon/checkbox_'.(($row['yn'])?'':'un').'checked_icon&16.png" onClick="ynGr('.$row['gr_id'].')">
         </td>';
  }
    echo '</td>';
    echo '</tr>';
}

if (!$headless) {?>

</table>
</div>

<script type="text/javascript">
    function delRow(pk) {
        $.ajax({
          url: 'a/gr_del.php?id='+pk
        });
        $('#tr_'+pk).hide();
    }
</script>
<?php
require("inc/footer.inc.php");
}
?>