<?php
$modul="user";

require("inc/req.php");
/*** General Table variables **/
// fill parameters from session
if (!isset($_REQUEST['headless']) && isset($_SESSION[$modul])) {
    foreach ($_SESSION[$modul] as $key => $value) {
        $_REQUEST[$key] = $value;
    }
}
if (isset($_REQUEST['sortcol'])) {
   $_SESSION[$modul]['sortcol'] = $_REQUEST['sortcol'];
}
if (isset($_REQUEST['sortdir'])) {
   $_SESSION[$modul]['sortdir'] = $_REQUEST['sortdir'];
}
validate('sortcol', 'string');
validate('sortdir', 'boolean');
$orderBy = ($_VALID['sortcol'])?$_VALID['sortcol'] .(($_VALID['sortdir'])?' DESC':''):'name';

/*** special table parameters ***/
validate('name', 'string');

/*** Rights ***/
// Generally for people with right do manage groups
RR(2);

$n4a['user_d.php'] = ss('Add user');
$headless = (isset($_REQUEST['headless']))?true:false;
if (!$headless) require("inc/header.inc.php");

// Ergebnis aufbauen ------- //
$sql="SELECT u.user_id, u.firstname, u.lastname, CONCAT(u.firstname,' ', u.lastname) as name, GROUP_CONCAT(shortname SEPARATOR ', ') as groups
        FROM user u
        LEFT JOIN user2gr ON user2gr.user_id=u.user_id
        LEFT JOIN gr ON gr.gr_id=user2gr.gr_id
      WHERE 1=1";

/*** Filter ***/
// Admin group for Administrators only
if (!GR(1)) {
    $sql .= " AND u.user_id != 1";
}
if ($_VALID['name']) {
    $sql .= " AND (firstname LIKE '%".my_sql($_VALID['name'])."%'"
         . " OR lastname LIKE '%".my_sql($_VALID['name'])."%')";
}

/*** Group By ***/
$sql .= " GROUP BY u.user_id, u.firstname, u.lastname";

/*** Order By ***/
$sql .= " ORDER BY " . my_sql($orderBy);
/*** Store Search values in Session ***/
$_SESSION[$modul]['name'] = $_VALID['name'];
$_SESSION[$modul]['sql'] = $sql;

$listResult = getMemCache($sql);
// refresh memcache if necessary
$rl = isset($_SESSION[$modul]['rl']) || isset($_GET['rl']);
if (!$listResult || $rl) {
    $r = mysqli_query($con, $sql) or die(mysqli_error($con));
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
<!--<a href="user_d.php"><img alt="<?php sss('Add new user')?>" title="<?php sss('Add new user')?>" src="css/icon/doc_empty_icon&16.png" class="listmenuicon"></a><br><br>-->
<div class="contentheadline"><?php sss('User')?></div>
<br>
<div class="contenttext">
<table cellspacing="0" cellpadding="0" class="bw">
<?php
}
if (!$listResult) {
    echo ss('No entries found');
} else {
if (!$headless) {
    echo '<tr class="head">';
    echo '<th><a href="javascript:void(0)" onClick="changeSort(\'name\')">'.ss('Name').'</a>&nbsp;&nbsp;<input class="search" name="name" id="name" value="'.$_SESSION[$modul]['name'].'"></th>';
    echo '<th class="grey">'.ss('Group').'</th>';
    echo '<th>&nbsp;</th>';
    echo '</tr><tbody id="list_tbody">';
}
foreach($listResult as $index => $row) {
    echo '<tr class="dotted" id="tr_'.$row['user_id'].'">';
    echo '<td nowrap '.$mouseover.' onClick="location.href=\''.$modul.'_d.php?i='.$index.'&amp;id='.$row[$modul.'_id'].'\'">' . $row['firstname'].' ' .$row['lastname'].'</td>';
    echo '<td class="grey">' . $row['groups'].'&nbsp;</td>';
    echo '<td align="right" nowrap><a href="user_d.php?i='.$index.'&amp;id='.$row['user_id'].'"><img src="css/icon/pencil_icon&16.png" title="'.ss('Edit').'"></a>';
    // people with right to delete see the delete button
    if (R(3)) echo '&nbsp;&nbsp;<a href="#" onclick="if (confirm(\''.ss('Do you really want to delete the user?').'\')) delRow('.$row['user_id'].');">
            <img src="css/icon/delete_icon&16.png" title="'.ss('Delete').'"></a>';
    // people with right to use identity will see the icon
    if (R(1)) echo '&nbsp;&nbsp;<a href="#" onclick="if (confirm(\''.ss('Use the profile of this user?').'\')) location.href=\'login.php?rn&id='.$row['user_id'].'\';">
            <img src="css/icon/user_icon&16.png" title="'.ss('Use this profile now').'"></a>';
    echo '</td>';
    echo '</tr>';
}}
if (!$headless) { ?>
</table>
</div>
<script type="text/javascript">


    var sortcol = 'name';
    var sortdir = '';
    var del = '';
    var refresh = false;
    function updateList() {
        var url = 'user.php?headless&sortcol='+sortcol+'&sortdir='+sortdir+'&del='+del;
        if(refresh) url += '&rl';
        var filterparams = '';

        // inputs
        $('.search:input').each(function(index, obj) {

            filterparams += '&' + obj.name + '=' + $('#' + obj.name).val();
        });

        url += filterparams;
        $.get(url, function(data) {
            $('#list_tbody').html(data);

            // also add the filterparam to the xls export
            $('#xlsbutton').attr('href',$('#xlsbutton').attr('href') + filterparams);
        });

    }
    function delRow(pk) {
        $.ajax({
          url: 'a/user_del.php?id='+pk
        });
        refresh = true;
        updateList();
    }
    function changeSort(col) {

        if (sortcol == col) {
            sortdir = (sortdir == 'DESC') ? '' : 'DESC';
        } else {
            sortdir = 'DESC';
        }
        sortcol = col;

        updateList();
    }

    $('.search:input').keyup(function(index) {
        updateList();
    });
</script>
<?php
require("inc/footer.inc.php");
}
?>