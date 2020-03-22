<?php
$modul="nav";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to change nav
GR(8);

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
if (isset($_REQUEST['limit'])) {
   $_SESSION[$modul]['limit'] = $_REQUEST['limit'];
}
validate('sortcol', 'string');
validate('sortdir', 'set',array('ASC','DESC'));
$orderBy = ($_VALID['sortcol'])?$_VALID['sortcol'] .(($_VALID['sortdir'])?' '.$_VALID['sortdir']:''):'nav.name';

$headless = (isset($_REQUEST['headless']))?true:false;
$n4a['nav_d.php'] = 'Add Nav';
if (!$headless) require("inc/header.inc.php");

/*** Validation ***/

// Nav_id
validate('nav_id', 'int');
$_SESSION[$modul]['nav_id'] = $_VALID['nav_id'];
$nav_id = $_SESSION[$modul]['nav_id'];

// To_nav_id
validate('to_nav_id', 'int nullable');
validate('parent_name', 'string nullable');
$_SESSION[$modul]['parent_name'] = $_VALID['parent_name'];
$to_nav_id = $_SESSION[$modul]['to_nav_id'];

// Gr_id
validate('gr_id', 'int nullable');
$_SESSION[$modul]['gr_id'] = $_VALID['gr_id'];
$gr_id = $_SESSION[$modul]['gr_id'];

// Level
validate('level', 'int');
$_SESSION[$modul]['level'] = $_VALID['level'];
$level = $_SESSION[$modul]['level'];

// Name
validate('name', 'string');
$_SESSION[$modul]['name'] = $_VALID['name'];
$name = $_SESSION[$modul]['name'];

// Link
validate('link', 'string nullable');
$_SESSION[$modul]['link'] = $_VALID['link'];
$link = $_SESSION[$modul]['link'];

// Params
validate('params', 'string');
$_SESSION[$modul]['params'] = $_VALID['params'];
$params = $_SESSION[$modul]['params'];

if (!$_REQUEST['nav_id'] OR !$_REQUEST['level'] OR !$_REQUEST['name'] OR !$_REQUEST['params']) {
    $error[] = ss('Some mandatory fields are missing');
}
// delete
if (isset($_REQUEST['delete'])) {
    $sql = "DELETE FROM nav WHERE nav_id = " . (int) $_REQUEST['nav_id'];
    mysqli_query($con, $sql) or error_log(mysqli_error());
}

// where condition
if ($_VALID['nav_id']) {
    $where[] = "nav.nav_id =  " . $_VALIDDB['nav_id'];
}

if ($_VALID['parent_name']) {
    $where[] = "nav_parent.name LIKE '%" . my_sql($_VALID['parent_name']) . "%'";
}

if ($_VALID['gr_id']) {
    $where[] = "nav.gr_id =  " . $_VALIDDB['gr_id'];
}

if ($_VALID['level']) {
    $where[] = "nav.level =  " . $_VALIDDB['level'];
}

if ($_VALID['name']) {
    $where[] = "nav.name LIKE '%" . mysqli_real_escape_string($con, $_VALID['name']) . "%'";
}

if ($_VALID['link']) {
    $where[] = "nav.link LIKE '%" . mysqli_real_escape_string($con, $_VALID['link']) . "%'";
}

if ($_VALID['params']) {
    $where[] = "nav.params LIKE '%" . mysqli_real_escape_string($con, $_VALID['params']) . "%'";
}

if ($_VALID['icon']) {
    $where[] = "nav.icon LIKE '%" . mysqli_real_escape_string($con, $_VALID['icon']) . "%'";
}

if (!GR(1)) {
    $where[] .= "gr_id != 1";
}

$_SESSION[$modul]['params'] = $_VALID['params'];
$where = ($where) ? implode(" AND ", $where) : "1=1";

/*** Store SQL in Session ***/
$_SESSION[$modul]['sql'] = $sql;

$sql = "SELECT nav.name,nav.to_nav_id,nav_parent.level as parent_level, nav_parent.name as parent_name,gr.shortname as gr_name,nav.level,nav.link,nav.params,nav.nav_id
        FROM nav
        LEFT JOIN nav nav_parent ON nav_parent.nav_id=nav.to_nav_id
        LEFT JOIN gr ON nav.gr_id=gr.gr_id
        WHERE " . $where . " ORDER BY " . $orderBy;
$_SESSION[$modul]['sql'] = $sql;
$listResult = getMemCache($sql);
// refresh memcache if necessary
$rl = isset($_SESSION[$modul]['rl']) || isset($_GET['ok']);
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
<div class="contentheadline"><?php echo ss('Nav')?></div>
<br>
<div class="contenttext">
<table cellspacing="0" cellpadding="0" class="bw">
<?php
    echo '<tr class="head">';
    echo '<th><a href="javascript:void(0)" onClick="changeSort(\'nav.name\')">' . ss('Nav') . '</a>&nbsp;&nbsp;
           <input class="search" name="name" id="name" value="'.$_SESSION[$modul]['name'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'nav.to_nav_id\')">' . ss('Parent') . '</a>&nbsp;&nbsp;
           <input class="search" name="parent_name" id="parent_name" value="'.$_SESSION[$modul]['parent_name'].'">
           </th>';
    echo '<th><a href="javascript:void(0)" onClick="changeSort(\'gr_name\')">' . ss('Group') . '</a>&nbsp;&nbsp;
           <select id="gr_id" name="gr_id">'.groupConvert($_SESSION[$modul]['gr_id'], true).'</select>
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'nav.level\')">' . ss('Level') . '</a>&nbsp;&nbsp;
           <input class="search integer" name="level" id="level" value="'.$_SESSION[$modul]['level'].'" style="width:50px">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'nav.link\')">' . ss('Link') . '</a>&nbsp;&nbsp;
           <input class="search" name="link" id="link" value="'.$_SESSION[$modul]['link'].'">
           </th>';
    echo '<th>&nbsp;</th>';
    echo '</tr><tbody id="list_tbody">';
}

if (!$listResult) {
    echo ss('No entries found');
} else {
    $i = 0;
    foreach($listResult as $index => $row) {
        echo '<tr class="dotted ' .  ((($i++ % 2)==0) ? "tr_even":"tr_odd") . '" id="tr_'.$row['nav_id'].'">';
            echo '<td '.$mouseover.' onClick="location.href=\'nav_d.php?i='.$index.'&amp;id='.$row['nav_id'].'\'">' . $row['name'] . '</td>';
            echo '<td>' . $row['parent_name'] . '</td>';
            echo '<td>' . ss($row['gr_name']) . '</td>';
            echo '<td>' . $row['level'] . '</td>';
            echo '<td>' . $row['link'] . (($row['params'])?'?'.$row['params']:'').'</td>';
            echo '<td nowrap><a href="nav_d.php?i='.$index.'&amp;id=' . $row['nav_id'] . '"><img src="css/icon/pencil_icon&16.png" title="' . ss('Edit') . '"></a>';
// people with right to delete see the delete button
    if (R(3))
            echo '&nbsp;&nbsp;<a href="#" onclick="if (confirm(\'' . ss('Do you really want to delete the Nav?') . '\')) delRow('.$row['nav_id'].');">
            <img src="css/icon/delete_icon&16.png" title="' . ss('Delete') . '"></a>';
            echo '</td>';
        echo '</tr>';
    }
}

if (!$headless) { ?>
</table>
</div>

<script type="text/javascript">
var sortcol = 'name';
var sortdir = '';
var del = '';
function delRow(pk) {
    $.ajax({
      url: 'a/nav_del.php?id='+pk
    });
    $('#tr_'+pk).hide();
}
function updateList() {
    var url = 'nav.php?headless&sortcol='+sortcol+'&sortdir='+sortdir+'&del='+del;
    var filterparams = '';


    // inputs
    var val = '';
    $('.search:input').each(function(index, obj) {
        val = $('#' + obj.name).val();
        if (val != '') filterparams += '&' + obj.name + '=' + $('#' + obj.name).val();
    });
    $('.bw select').each(function(index, obj) {
        val = $("#" + obj.name).val();
        if (val != '') filterparams += '&' + obj.name + '=' + $("#" + obj.name).val();
    });

    url += filterparams;
    $.get(url, function(data) {
        $('#list_tbody').html(data);

        // also add the filterparam to the xls export
        $('#xlsbutton').attr('href',$('#xlsbutton').attr('href') + filterparams);
    });

}
function changeSort(col) {

    if (sortcol == col) {
        sortdir = (sortdir == 'DESC') ? 'ASC' : 'DESC';
    } else {
        sortdir = 'DESC';
    }
    sortcol = col;

    updateList();
}
$('.search:input').keyup(function(index) {
    updateList();
});
$('.bw select').change(function() {
    updateList();
});
</script>
<?php
require("inc/footer.inc.php");
}
?> 