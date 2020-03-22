<?php 
    $modul="cron";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to change cron
GRGR(3);


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
validate('sortdir', 'set', array('ASC','DESC'));
$orderBy = ($_VALID['sortcol'])?$_VALID['sortcol'] .(($_VALID['sortdir'])?' ' . $_VALID['sortdir']:''):'cron_id';
    
$headless = (isset($_REQUEST['headless']))?true:false;
$n4a['cron_d.php'] = '' . ss('Add Cron') . '';
if (!$headless) require("inc/header.inc.php");

/*** Validation ***/

// Cron_id
validate('cron_id', 'int nullable' );
$_SESSION[$modul]['cron_id'] = $_VALID[' cron_id'];

// Task
validate('task', 'string' );
$_SESSION[$modul]['task'] = $_VALID[' task'];

// Active
validate('active', 'ckb' );
$_SESSION[$modul]['active'] = $_VALID[' active'];

// Mhdmd
validate('mhdmd', 'string' );
$_SESSION[$modul]['mhdmd'] = $_VALID[' mhdmd'];

// File
validate('file', 'string' );
$_SESSION[$modul]['file'] = $_VALID[' file'];

// Parameters
validate('parameters', 'string nullable' );
$_SESSION[$modul]['parameters'] = $_VALID[' parameters'];

// Ran_at
validate('ran_at', 'int nullable' );
$_SESSION[$modul]['ran_at'] = $_VALID[' ran_at'];

// End_time
validate('end_time', 'int nullable' );
$_SESSION[$modul]['end_time'] = $_VALID[' end_time'];

// Ok
validate('ok', 'ckb' );
$_SESSION[$modul]['ok'] = $_VALID[' ok'];

// Log_level
validate('log_level', 'ckb nullable' );
$_SESSION[$modul]['log_level'] = $_VALID[' log_level'];
if (isset($_REQUEST['submitted']) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}
// delete
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM cron WHERE cron_id = " . (int) $_REQUEST['cron_id'];
	mysqli_query($con, $sql) or error_log(mysqli_error());
}

// where condition
if ($_VALID['cron_id']) {
	$where[] = "cron.cron_id =  " . $_VALIDDB['cron_id'];
}
$_SESSION[$modul]['cron_id'] = $_VALID['cron_id'];
if ($_VALID['task']) {
	$where[] = "cron.task LIKE '%" . mysqli_real_escape_string($con, $_VALID['task']) . "%'";
}
$_SESSION[$modul]['task'] = $_VALID['task'];
if (isset($_VALID['active'])) {
	$where[] = "cron.active = " . (($_VALIDDB['active'] == 1) ? 1 : 0);
}
$_SESSION[$modul]['active'] = $_VALID['active'];
if ($_VALID['mhdmd']) {
	$where[] = "cron.mhdmd LIKE '%" . mysqli_real_escape_string($con, $_VALID['mhdmd']) . "%'";
}
$_SESSION[$modul]['mhdmd'] = $_VALID['mhdmd'];
if ($_VALID['file']) {
	$where[] = "cron.file LIKE '%" . mysqli_real_escape_string($con, $_VALID['file']) . "%'";
}
$_SESSION[$modul]['file'] = $_VALID['file'];
if ($_VALID['parameters']) {
	$where[] = "cron.parameters LIKE '%" . mysqli_real_escape_string($con, $_VALID['parameters']) . "%'";
}
$_SESSION[$modul]['parameters'] = $_VALID['parameters'];
if ($_VALID['ran_at']) {
	$where[] = "cron.ran_at =  " . $_VALIDDB['ran_at'];
}
$_SESSION[$modul]['ran_at'] = $_VALID['ran_at'];
if ($_VALID['end_time']) {
	$where[] = "cron.end_time =  " . $_VALIDDB['end_time'];
}
$_SESSION[$modul]['end_time'] = $_VALID['end_time'];
if (isset($_VALID['ok'])) {
	$where[] = "cron.ok = " . (($_VALIDDB['ok'] == 1) ? 1 : 0);
}
$_SESSION[$modul]['ok'] = $_VALID['ok'];
if (isset($_VALID['log_level'])) {
	$where[] = "cron.log_level = " . (($_VALIDDB['log_level'] == 1) ? 1 : 0);
}
$_SESSION[$modul]['log_level'] = $_VALID['log_level'];
$where = ($where) ? implode(" AND ", $where) : "1=1";
//List Hook After Where
$sql = "SELECT cron_id, cron.task, cron.active, cron.mhdmd, cron.file, cron.parameters, cron.ran_at, cron.end_time, cron.ok, cron.log_level FROM cron WHERE " . $where . " ORDER BY " . $orderBy;
$listResult = mysqli_query($con, $sql);

if (!$headless) {
?>
<div class="contentheadline"><?php echo ss('Cron')?></div>
<br>
<div class="contenttext">
<!-- hook vor cron liste -->
<table cellspacing="0" cellpadding="0" class="bw">
<?php
}
if (!$headless) {
    echo '<tr class="head">';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'cron_id\')">' . ss('Cron_id') . '</a>&nbsp;&nbsp;
           <input class="search" name="cron_id" id="cron_id" value="'.$_SESSION[$modul]['Cron_id'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'task\')">' . ss('Task') . '</a>&nbsp;&nbsp;
           <input class="search" name="task" id="task" value="'.$_SESSION[$modul]['Task'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'active\')">' . ss('Active') . '</a>&nbsp;&nbsp;
           <input class="search" name="active" id="active" value="'.$_SESSION[$modul]['Active'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'mhdmd\')">' . ss('Mhdmd') . '</a>&nbsp;&nbsp;
           <input class="search" name="mhdmd" id="mhdmd" value="'.$_SESSION[$modul]['Mhdmd'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'file\')">' . ss('File') . '</a>&nbsp;&nbsp;
           <input class="search" name="file" id="file" value="'.$_SESSION[$modul]['File'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'parameters\')">' . ss('Parameters') . '</a>&nbsp;&nbsp;
           <input class="search" name="parameters" id="parameters" value="'.$_SESSION[$modul]['Parameters'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'ran_at\')">' . ss('Ran_at') . '</a>&nbsp;&nbsp;
           <input class="search" name="ran_at" id="ran_at" value="'.$_SESSION[$modul]['Ran_at'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'end_time\')">' . ss('End_time') . '</a>&nbsp;&nbsp;
           <input class="search" name="end_time" id="end_time" value="'.$_SESSION[$modul]['End_time'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'ok\')">' . ss('Ok') . '</a>&nbsp;&nbsp;
           <input class="search" name="ok" id="ok" value="'.$_SESSION[$modul]['Ok'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'log_level\')">' . ss('Log_level') . '</a>&nbsp;&nbsp;
           <input class="search" name="log_level" id="log_level" value="'.$_SESSION[$modul]['Log_level'].'">
           </th>';
    echo '<th>&nbsp;</th>';
    echo '</tr><tbody id="list_tbody">';
}
  if (!$listResult) {
    echo '<tr><td colspan="3">'.ss('No entries found').'</td></tr>';
  } else {
    $i = 0;
	while($row = mysqli_fetch_array($listResult)) {
		echo '<tr class="dotted ' .  ((($i++ % 2)==0) ? "tr_even":"tr_odd") . '" id="tr_'.$row['cron_id'].'">';
			echo '<td '.$mouseover.' onClick="location.href=\'cron_d.php?i='.$index.'&amp;cron_id='.$row['cron_id'].'\'" nowrap>' . str_limit(htmlspecialchars($row['cron_id'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['task'])) . '</td>';
			echo '<td nowrap>' . (($row['active'] == '1') ? ss('yes') : ss('no')) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['mhdmd'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['file'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['parameters'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['ran_at'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['end_time'])) . '</td>';
			echo '<td nowrap>' . (($row['ok'] == '1') ? ss('yes') : ss('no')) . '</td>';
			echo '<td nowrap>' . (($row['log_level'] == '1') ? ss('yes') : ss('no')) . '</td>';
            echo '<td nowrap><a href="cron_d.php?i=&amp;cron_id=' . (int) $row['cron_id'] . '"><img src="css/icon/pencil_icon&16.png" title="' . ss('Edit') . '"></a>';
// people with right to delete see the delete button
    if (R(3))
            echo '&nbsp;&nbsp;<a href="#" onclick="if (confirm(\'' . ss('Do you really want to delete the Cron?') . '\')) delRow('.$row['cron_id'].');">
            <img src="css/icon/delete_icon&16.png" title="' . ss('Delete') . '"></a>';
            echo '</td>';
		echo '</tr>';
	}
}

if (!$headless) { ?>
</table>
</div>

<script type="text/javascript">
function delRow(pk) {
    $.ajax({
      url: 'a/cron_del.php?id='+pk
    });
    $('#tr_'+pk).hide();
}

var sortcol = 'cron_id';
var sortdir = '';
var del = '';
function updateList() {
    var url = '<?=$_REQUEST['PHP_SELF']?>?headless&sortcol='+sortcol+'&sortdir='+sortdir+'&del='+del;
    var filterparams = '';


    // inputs
    var val = '';
    $('.search:input').each(function(index, obj) {
        val = $('#' + obj.name).val();
        if (val != '') filterparams += '&' + obj.name + '=' + $('#' + obj.name).val();
    });

    $('.bw select').each(function(index, obj) {
        val = $("#" + obj.name).val();
        if (val != '') filterparams += '&' + obj.name + '=' + $('#' + obj.name).val();
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

jQuery(document).ready(function(){
    $("#loading-image")
    .bind("ajaxSend", function(){
    $(this).show();
    })
    .bind("ajaxComplete", function(){
    $(this).hide();
    });
    jQuery('#loading-image').hide();
  //  updateList();

});

// hook cron javascript
</script>
<style type="text/css" >
#loading-image {

	width: 65px;
	height: 55px;
	position: fixed;
	right: 160px;
	z-index: 1;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	/*	background-color: #333;
	border-radius: 10px; */
	margin-right:580px;
	top:260px;;
	-khtml-border-radius: 10px;
}
</style>
<?php
require("inc/footer.inc.php");
} 
    ?>