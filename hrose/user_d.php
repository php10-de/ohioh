<?php
$modul="user";

require("inc/req.php");

validate('id', 'int');
validate('email', 'email');
validate('password', 'string');
validate('firstname', 'string');
validate('lastname', 'string');
validate('is_active', 'int');
validate('i', 'int');
validate('lang', 'string');
$id = $_VALID['id'];

/*** Rights ***/
// Generally for people with right do manage groups
RR(2);
// Admin group for Administrators only
if ($id == 1) {
    GRGR(1);
}

if (isset($_REQUEST['submitted'])) {
    if (!$_VALID['firstname'] || !$_VALID['lastname'] || !$_VALID['email'] || !$_VALID['lang']) {
        $headerError = ss('Some mandatory fields are missing');
    } else {
        if (!$id) {
            $sql = "INSERT INTO user(firstname, lastname, email, lang".(($_VALID['password'])?', password':'').")
                    VALUES (".$_VALIDDB['firstname']
                    .",".$_VALIDDB['lastname']
                    .",".$_VALIDDB['email']
                    .",".$_VALIDDB['lang']
                    .(($_VALID['password'])?",'".my_sql(sha1($_VALID['password'].SALT))."'":"")
                    .")";
            $res = mysqli_query($con, $sql);
            if (!$res) {
                $headerError = ss('Something went wrong.');
            } else {
                $_SESSION[$modul]['rl'] = true;
                header('Location: user.php?ok=User added');
                exit;
            }
        } else {
            //  Admin user edit for Administrators only
            if ($id) {
                $sql = "SELECT 1 FROM user2gr WHERE gr_id=1 AND user_id=".$id;
                $res = mysqli_query($con, $sql);
                $row = mysqli_fetch_row($res);
                if ($row) {
                    GRGR(1);
                }
            }
            $sql = "REPLACE INTO user( ".(($id)?'user_id,':'').(($_VALID['password'])?'password,':'')." email, firstname, lastname, is_active, ".(($id)?'':'dbinsert, ')."lang, dbupdate) VALUES("
            . (($id)? $id.',':'')
            .(($_VALID['password'])? "'".my_sql(sha1($_VALID['password'].SALT))."',":'')
            . $_VALIDDB['email']
            . ", " . $_VALIDDB['firstname']
            . ", " . $_VALIDDB['lastname']
            . ", " .  (int) $_VALIDDB['is_active']
            .(($id)? "":", now()")
            . ", " . $_VALIDDB['lang']
            . ", now())";
            mysqli_query($con, $sql) or die(mysqli_error());

            if (!$id) {
                $id = mysqli_insert_id($con);
            }
            $_SESSION[$modul]['rl'] = true;
            header('Location: user.php?ok=Done');
            exit;
        }
    }
} else if ($id) {
    // Infos auslesen
    $sql = "SELECT * FROM user WHERE user_id = " . $id;
    $data = mysqli_fetch_assoc(mysqli_query($con, $sql));
    foreach ($data as $key => $value) {
        $data[$key] = $value;
    }
}

// manuelle Eingabe überschreibt DB-Werte
if (isset($_REQUEST['submitted'])) {
    foreach ($_VALID as $key => $value) {
        $data[$key] = $value;
    }
}

//$n4a['user.php'] = ss('Back to user list');
require("inc/header.inc.php");
if ($error) {
    echo '<p class="error">' . implode('<br>', $error) . '</p>';
}
?>
<a href="javascript:void(0)" onClick="window.location.href = '<?php echo $modul?>.php'"><img alt="<?php sss('Back to user list')?>" title="<?php sss('Back to List')?>" src="css/icon/align_just_icon&16.png" class="listmenuicon"></a>
<?php
/*** Pagination ***/
if ($id) {
    $listResult = memcacheArray($_SESSION['user']['sql']);
    $prevEntry = $listResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '&nbsp;&nbsp;<a href="user_d.php?i='.($_VALID['i']-1).'&amp;id='.$prevEntry['user_id'].'"><img src="css/icon/br_prev_icon&16.png" title="'.ss('Previous').'"></a>';
    } else {
        echo '&nbsp;<span style="margin:8px">&nbsp;</span>';
    }

    $nextEntry = $listResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="user_d.php?i='.($_VALID['i']+1).'&amp;id='.$nextEntry['user_id'].'"><img src="css/icon/br_next_icon&16.png" title="'.ss('Next').'"></a>';
    }

    echo '<br><br><div class="contentheadline">' . ss($data['firstname'] . ' ' . $data['lastname']).'</div><br>';
}?>
<div class="contenttext">

<form id="form<?php echo $modul?>" name="form<?php echo $modul?>" method="post" class="formLayout">
<?php if($id) {
  echo '<input type="hidden" name="id" value="'.$id.'">';
}?>

<label for="email"><?php echo ss('E-Mail')?></label>
<input type="text" name="email" id="email" value="<?php sss($data['email'])?>" required="required" />

<br>
<label for="password"><?php echo ss('Password')?></label>
<input type="password" name="password" id="password" value="" />

<br>
<label for="firstname"><?php echo ss('Firstname')?></label>
<input type="text" name="firstname" id="firstname" value="<?php sss($data['firstname'])?>" required="required" />

<br>
<label for="lastname"><?php echo ss('Lastname')?></label>
<input type="text" name="lastname" id="lastname" value="<?php sss($data['lastname'])?>" required="required" />

<br>
<label for="lang"><?php echo ss('Language')?></label>
<select name="lang" required="required" id="lang"><?php echo languageConvert($data['lang'],true)?></select>

<?php
if (GR(6)) { ?>
<br>
<label for="is_active"><?php echo ss('Active')?></label>
<input type="checkbox" name="is_active" id="is_active" value="1" <?php echo ($data['is_active'] OR !isset($data))?'checked="checked"':''?>  />
<?php }?>

<br>
<input type="hidden" name="submitted" value="submitted">
<input type="submit" id="submit" value="<?php sss('Submit')?>">
</form>
<?php if($err!="") {
    echo '<br><span class="red">'.$err.'</span>';
}


if ($id) {
/*** Group ***/
if (R(2)) {
?>
<br>
<br>
<div class="contentheadline"><a href="javascript:void(0)" onClick="loadGr(this)"><?php sss('Groups')?></a></div>
<table cellspacing="0" cellpadding="0" class="bw">
<tbody id="grlist_tbody">

</tbody>
</table>

<script type="text/javascript">

var grRefresh = false;
function loadGr() {
    var url = 'gr.php?headless&user_id=<?php echo $id?>';
    if(grRefresh) url += '&rl';
    $.get(url, function(data) {
        $('#grlist_tbody').html(data);
    });
}

function ynGr(gr_id) {
    if($('#grtick_'+gr_id).attr("src")=="css/icon/checkbox_unchecked_icon&16.png") {
        $('#grtick_'+gr_id).attr("src", "css/icon/checkbox_checked_icon&16.png");
        right = 1;
    }
    else{
        $('#grtick_'+gr_id).attr("src", "css/icon/checkbox_unchecked_icon&16.png");
        right = 0;
    }
    $.post("a/user2gr_edit.php?yn="+right, { gr_id: gr_id, user_id: <?php echo $id ?> });
}
</script>
<?php }

/*** Rights ***/
if (R(4)) {
?>
<br>
<br>
<div class="contentheadline"><a href="javascript:void(0)" onClick="loadR(this)"><?php sss('Rights')?></a></div>
<table cellspacing="0" cellpadding="0" class="bw">
<tbody id="rlist_tbody">

</tbody>
</table>

<script type="text/javascript">

var rRefresh = false;
function loadR() {
    var url = 'r.php?headless&user_id=<?php echo $id?>';
    if(rRefresh) url += '&rl';
    $.get(url, function(data) {
        $('#rlist_tbody').html(data);
    });
}

function ynR(r_id, gr_yn) {
    if($('#rtick_'+r_id).attr("src")=="css/icon/checkbox_unchecked_icon&16.png") {
        $('#rtick_'+r_id).attr("src", "css/icon/checkbox_checked_icon&16.png");
        right = 1;
    }
    else{
        $('#rtick_'+r_id).attr("src", "css/icon/checkbox_unchecked_icon&16.png");
        right = 0;
    }
    $.post("a/user2r.php?yn="+right, { r_id: r_id, gr_yn: gr_yn, user_id: <?php echo $id ?> });
}
</script>
<?php } // if groups
} // if $id?>
</div>
<?php
require("inc/footer.inc.php");
?>