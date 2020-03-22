<?php 
    $modul="sql_deploy";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to change sql_deploy
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
$orderBy = ($_VALID['sortcol'])?$_VALID['sortcol'] .(($_VALID['sortdir'])?' ' . $_VALID['sortdir']:''):'sql_deploy_id';
    
$headless = (isset($_REQUEST['headless']))?true:false;
$n4a['sql_deploy_d.php'] = '' . ss('Add Sql_deploy') . '';
if (!$headless) require("inc/header.inc.php");

/*** Validation ***/

// Sql_deploy_id
validate('sql_deploy_id', 'int nullable' );
$_SESSION[$modul]['sql_deploy_id'] = $_VALID[' sql_deploy_id'];

// Filename
validate('filename', 'string' );
$_SESSION[$modul]['filename'] = $_VALID[' filename'];

// Hroses
validate('hroses', 'string' );
$_SESSION[$modul]['hroses'] = $_VALID[' hroses'];

// Status
validate('status', 'string nullable' );
$_SESSION[$modul]['status'] = $_VALID[' status'];

// Deployed_date
validate('deployed_date', 'string nullable' );
$_SESSION[$modul]['deployed_date'] = $_VALID[' deployed_date'];
if (isset($_REQUEST['submitted']) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}
// delete
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM sql_deploy WHERE sql_deploy_id = " . (int) $_REQUEST['sql_deploy_id'];
	mysqli_query($con, $sql) or error_log(mysqli_error());
}

// where condition
if ($_VALID['sql_deploy_id']) {
	$where[] = "sql_deploy.sql_deploy_id =  " . $_VALIDDB['sql_deploy_id'];
}
$_SESSION[$modul]['sql_deploy_id'] = $_VALID['sql_deploy_id'];
if ($_VALID['filename']) {
	$where[] = "sql_deploy.filename LIKE '%" . mysqli_real_escape_string($con, $_VALID['filename']) . "%'";
}
$_SESSION[$modul]['filename'] = $_VALID['filename'];
if ($_VALID['hroses']) {
	$where[] = "sql_deploy.hroses LIKE '%" . mysqli_real_escape_string($con, $_VALID['hroses']) . "%'";
}
$_SESSION[$modul]['hroses'] = $_VALID['hroses'];
if ($_VALID['status']) {
	$where[] = "sql_deploy.status LIKE '%" . mysqli_real_escape_string($con, $_VALID['status']) . "%'";
}
$_SESSION[$modul]['status'] = $_VALID['status'];
if ($_VALID['deployed_date']) {
	$where[] = "sql_deploy.deployed_date LIKE '%" . mysqli_real_escape_string($con, $_VALID['deployed_date']) . "%'";
}
$_SESSION[$modul]['deployed_date'] = $_VALID['deployed_date'];
$where = ($where) ? implode(" AND ", $where) : "1=1";
//List Hook After Where
$sql = "SELECT sql_deploy_id, sql_deploy.filename, sql_deploy.hroses, sql_deploy.status, sql_deploy.deployed_date FROM sql_deploy WHERE " . $where . " ORDER BY " . $orderBy;
$listResult = mysqli_query($con, $sql);

if (!$headless) {
?>
<div class="contentheadline"><?php echo ss('Sql_deploy')?></div>
<br>
<div class="contenttext">
<!-- hook vor sql_deploy liste -->
<table cellspacing="0" cellpadding="0" class="bw">
<?php
}
if (!$headless) {
    echo '<tr class="head">';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'sql_deploy_id\')">' . ss('Sql_deploy_id') . '</a>&nbsp;&nbsp;
           <input class="search" name="sql_deploy_id" id="sql_deploy_id" value="'.$_SESSION[$modul]['Sql_deploy_id'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'filename\')">' . ss('Filename') . '</a>&nbsp;&nbsp;
           <input class="search" name="filename" id="filename" value="'.$_SESSION[$modul]['Filename'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'hroses\')">' . ss('Hroses') . '</a>&nbsp;&nbsp;
           <input class="search" name="hroses" id="hroses" value="'.$_SESSION[$modul]['Hroses'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'status\')">' . ss('Status') . '</a>&nbsp;&nbsp;
           <input class="search" name="status" id="status" value="'.$_SESSION[$modul]['Status'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'deployed_date\')">' . ss('Deployed_date') . '</a>&nbsp;&nbsp;
           <input class="search" name="deployed_date" id="deployed_date" value="'.$_SESSION[$modul]['Deployed_date'].'">
           </th>';
    echo '<th>&nbsp;</th>';
    echo '</tr><tbody id="list_tbody">';
}
  if (!$listResult) {
    echo '<tr><td colspan="3">'.ss('No entries found').'</td></tr>';
  } else {
    $i = 0;
	while($row = mysqli_fetch_array($listResult)) {
		echo '<tr class="dotted ' .  ((($i++ % 2)==0) ? "tr_even":"tr_odd") . '" id="tr_'.$row['sql_deploy_id'].'">';
			echo '<td '.$mouseover.' onClick="location.href=\'sql_deploy_d.php?i='.$index.'&amp;sql_deploy_id='.$row['sql_deploy_id'].'\'" nowrap>' . str_limit(htmlspecialchars($row['sql_deploy_id'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['filename'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['hroses'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['status'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['deployed_date'])) . '</td>';
            echo '<td nowrap><a href="sql_deploy_d.php?i=&amp;sql_deploy_id=' . (int) $row['sql_deploy_id'] . '"><img src="css/icon/pencil_icon&16.png" title="' . ss('Edit') . '"></a>';
// people with right to delete see the delete button
    if (R(3))
            echo '&nbsp;&nbsp;<a href="#" onclick="if (confirm(\'' . ss('Do you really want to delete the Sql_deploy?') . '\')) delRow('.$row['sql_deploy_id'].');">
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
      url: 'a/sql_deploy_del.php?id='+pk
    });
    $('#tr_'+pk).hide();
}

var sortcol = 'sql_deploy_id';
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

// hook sql_deploy javascript
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