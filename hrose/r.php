<?php
$modul="r";

require("inc/req.php");
validate('user_id','int');

/*** Rights ***/
// Generally for people with right to manage groups
RR(2);

$n4a['r_d.php'] = ss('Add right');
$headless = (isset($_REQUEST['headless']))?true:false;
if (!$headless) require("inc/header.inc.php");

// Ergebnis aufbauen ------- //
$sql="SELECT r.right_id, r.shortname, r.longname";
if ($_VALID['user_id']) {
    $sql .= ", (
            SELECT yn
            FROM right2user
            WHERE right2user.user_id = ".$_VALID['user_id']."
                AND right2user.right_id = r.right_id
          ) AS yn, (
            SELECT max(yn)
            FROM right2gr
            WHERE right2gr.gr_id IN (
                SELECT gr_id FROM user2gr 
                    WHERE user2gr.user_id = ".$_VALID['user_id']."
                )
                AND right2gr.right_id = r.right_id
                GROUP BY right2gr.right_id
          ) AS gr_yn";
}
$sql .= " FROM r";



/*** Filter ***/

/*** Order By ***/
$sql .= " ORDER BY r.shortname";
$listResult = getMemCache($sql);
// refresh memcache after saving (ok) or deleting (rl)
$rl = isset($_SESSION[$modul]['rl']) || isset($_GET['ok']);
if (!$listResult || $rl) {
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
<!--<a href="r_d.php"><img alt="<?php sss('Add right')?>" title="<?php sss('Add right')?>" src="css/icon/doc_empty_icon&16.png" class="listmenuicon"></a><br><br>-->
<div class="contentheadline"><?php sss('Rights')?></div>
<br>
<div class="contenttext">
<table cellspacing="0" cellpadding="0" class="bw">
<?php
}
foreach ($listResult as $row) {
    echo '<tr class="dotted" id="tr_'.$row['right_id'].'">';
    echo '<td width="470">' . $row['shortname'].(($row['longname'])?' (' . $row['longname'].')':'').'</td>';
    echo '<td align="right">';
  if (!$_VALID['user_id']) {
    echo '<td align="right">
        <a href="r_d.php?id='.$row['right_id'].'"><img src="css/icon/pencil_icon&16.png" title="'.ss('Edit').'"></a>&nbsp;
        <a href="#" onclick="if (confirm(\''.ss('Do you really want to delete it?').'\')) delRow('.$row['right_id'].');"><img src="css/icon/delete_icon&16.png" title="'.ss('Delete').'"></a>
         </td>';
  } else {
    $yn = (isset($row['yn']))?$row['yn']:$row['gr_yn'];
    echo '<td align="right">
        <span style="display:'.((isset($row['yn']) AND ($row['gr_yn'] != $row['yn']))?'inline':'none').';" class="'.(($yn)?'green':'red').'">!&nbsp;</span>
        <img class="tick" id="rtick_'.$row['right_id'].'" src="css/icon/checkbox_'.(($yn)?'':'un').'checked_icon&16.png" onClick="ynR('.$row['right_id'].', '.(int) $row['gr_yn'].')">
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
          url: 'a/r_del.php?rn&id='+pk
        });
        $('#tr_'+pk).hide();
    }
</script>
<?php
require("inc/footer.inc.php");
}
?>