<?php
$modul="gr";

require("inc/req.php");

validate("shortname","string");
validate("longname","string nullable");
validate("id","int");
validate('i', 'int');
$id = $_VALID['id'];

/*** Rights ***/
// Generally for people with right do manage groups
RR(2);
// Admin group for Administrators only
if ($id == 1) {
    GRGR(1);
}

if (!$id) {
    if(isset($_REQUEST['submitted'])) {
        if (!$_VALID['shortname']) {
            $headerError = ss('Some mandatory fields are missing');
        } else {
            $sql = "INSERT INTO gr(shortname, longname)
                    VALUES (".$_VALIDDB['shortname'].",".$_VALIDDB['longname'].")";
            $res = mysqli_query($con, $sql);
            $id = mysqli_insert_id($con);
            if ($id) {
                foreach ($_VALID as $key => $value) {
                    $data[$key] = $value;
                }
            }
            $_SESSION[$modul]['rl'] = true;
            header('Location: gr.php?ok=Done');
            exit;
        }
    }
} else {
    if (isset($_REQUEST['submitted'])) {
        if (!$_VALID['shortname']) {
            $headerError = ss('Some mandatory fields are missing');
        } else {
            $sql = "UPDATE gr SET shortname=".$_VALIDDB['shortname'].",
                longname = ".$_VALIDDB['longname']." WHERE gr_id=".$id;
            mysqli_query($con, $sql);
            $_SESSION[$modul]['rl'] = true;
            header('Location: gr.php?ok=Done');
            exit;
        }

    }
    $sql = "SELECT * FROM gr WHERE gr_id=".$id . " LIMIT 0,1";
    $res = mysqli_query($con, $sql);
    $data = mysqli_fetch_array($res);
    
}

if ($id) {
    // Rechte Ergebnis aufbauen ------- //
    $sql="SELECT r.right_id, r.shortname, r.longname, (
            SELECT yn
            FROM right2gr
            WHERE right2gr.right_id = r.right_id
            AND gr_id = ".$id."
          ) AS yn FROM r
          WHERE 1
          ORDER BY shortname";
    $listResult=mysqli_query($con, $sql);
}

// manuelle Eingabe überschreibt DB-Werte
if (isset($_VALID['submitted'])) {
    foreach ($_VALID as $key => $value) {
        if (isset($data[$key])) $data[$key] = $value;
    }
}

$n4a['gr.php'] = ss('Back to group list');
require("inc/header.inc.php");
?>
<a href="javascript:void(0)" onClick="window.location.href = '<?php echo $modul?>.php'"><img alt="<?php sss('Back to List')?>" title="<?php sss('Back to List')?>" src="css/icon/align_just_icon&16.png" class="listmenuicon"></a>
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

    echo '<br><br><div class="contentheadline">' . ss($data['shortname']).'</div><br>';
}?>
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
    
if ($listResult) {?>
    <br>
    <div class="contentheadline"><?php sss('Group Rights')?></div>
    <br>
    <table cellspacing="0" cellpadding="0" class="bw">
    <?php
    while($row=mysqli_fetch_array($listResult)) {
        echo '<tr class="dotted" id="'.$row['right_id'].'">';
        echo '<td width="470">' . $row['shortname'].(($row['longname'])?' (' . $row['longname'].')':'').'</td>';
        echo '<td align="right"><img class="tick" src="css/icon/checkbox_'.(($row['yn'])?'':'un').'checked_icon&16.png"></td>';
        echo '</tr>';
    }?>

    </table>
    <script>
    $(document).ready(function(){

        $(".tick").click(function(){
            if($(this).attr("src")=="css/icon/checkbox_unchecked_icon&16.png") {
                $(this).attr("src", "css/icon/checkbox_checked_icon&16.png");
                right = 1;
            }
            else{
                $(this).attr("src", "css/icon/checkbox_unchecked_icon&16.png");
                right = 0;
            }
            $.post("a/right2gr_edit.php?action=right&yn="+right, { right_id: $(this).parent().parent().attr("id"), id: <?php echo $id ?> });
            });
    });
    </script>
<?php } ?>
</div>
<?php
require("inc/footer.inc.php");
?>