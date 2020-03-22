<?php 
    $modul="red_button";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to view red_button
GRGR(3);


/*** General Table variables **/
// fill parameters from session
if (!isset($_REQUEST['headless']) && isset($_SESSION[$modul])) {
    foreach ($_SESSION[$modul] as $key => $value) {
        $_REQUEST[$key] = $value;
    }
}
	
validate('sortcol', 'string');
validate('sortdir', 'enum', array('ASC','DESC'));

if (isset($_REQUEST['sortcol'])) {
   $_SESSION[$modul]['sortcol'] = $_REQUEST['sortcol'];
}
if (isset($_REQUEST['sortdir'])) {
   $_SESSION[$modul]['sortdir'] = $_REQUEST['sortdir'];
}
if (isset($_REQUEST['limit'])) {
   $_SESSION[$modul]['limit'] = $_REQUEST['limit'];
} else {
    $_REQUEST['limit'] = 50;
    $_SESSION[$modul]['limit'] = 50;
}
validate("limit","int");

$orderBy = ($_VALID['sortcol'])?$_VALID['sortcol'] .(($_VALID['sortdir'])?' ' . $_VALID['sortdir']:''):'red_button_id';
$orderBy = mysqli_real_escape_string ( $con , $orderBy );
    
$headless = (isset($_REQUEST['headless']))?true:false;
$n4a['red_button_d.php'] = '' . ss('Add Red_button') . '';
if (!$headless) require("inc/header.inc.php");

/*** Validation ***/

// Red_button_id
validate('red_button_id', 'int nullable' );
$_SESSION[$modul]['red_button_id'] = $_VALID[' red_button_id'];

// Tablename
validate('tablename', 'string' );
$_SESSION[$modul]['tablename'] = $_VALID[' tablename'];

// Replace_from
validate('replace_from', 'string' );
$_SESSION[$modul]['replace_from'] = $_VALID[' replace_from'];

// Replace_to
validate('replace_to', 'string' );
$_SESSION[$modul]['replace_to'] = $_VALID[' replace_to'];

// Is_config
validate('is_config', 'ckb' );
$_SESSION[$modul]['is_config'] = $_VALID[' is_config'];

// Is_active
validate('is_active', 'ckb nullable' );
$_SESSION[$modul]['is_active'] = $_VALID[' is_active'];

// Error
validate('error', 'string nullable' );
$_SESSION[$modul]['error'] = $_VALID[' error'];
if (isset($_REQUEST['submitted']) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}
// delete
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM red_button WHERE red_button_id = " . (int) $_REQUEST['red_button_id'];
	mysqli_query($con, $sql) or error_log(mysqli_error());
}

// where condition
if ($_VALID['red_button_id']) {
	$where[] = "red_button.red_button_id =  " . $_VALIDDB['red_button_id'];
}
$_SESSION[$modul]['red_button_id'] = $_VALID['red_button_id'];
if ($_VALID['tablename']) {
	$where[] = "red_button.tablename LIKE '%" . mysqli_real_escape_string($con, $_VALID['tablename']) . "%'";
}
$_SESSION[$modul]['tablename'] = $_VALID['tablename'];
if ($_VALID['replace_from']) {
	$where[] = "red_button.replace_from LIKE '%" . mysqli_real_escape_string($con, $_VALID['replace_from']) . "%'";
}
$_SESSION[$modul]['replace_from'] = $_VALID['replace_from'];
if ($_VALID['replace_to']) {
	$where[] = "red_button.replace_to LIKE '%" . mysqli_real_escape_string($con, $_VALID['replace_to']) . "%'";
}
$_SESSION[$modul]['replace_to'] = $_VALID['replace_to'];
if (isset($_VALID['is_config'])) {
	$where[] = "red_button.is_config = " . (($_VALIDDB['is_config'] == 1) ? 1 : 0);
}
$_SESSION[$modul]['is_config'] = $_VALID['is_config'];
if (isset($_VALID['is_active'])) {
	$where[] = "red_button.is_active = " . (($_VALIDDB['is_active'] == 1) ? 1 : 0);
}
$_SESSION[$modul]['is_active'] = $_VALID['is_active'];
if ($_VALID['error']) {
	$where[] = "red_button.error LIKE '%" . mysqli_real_escape_string($con, $_VALID['error']) . "%'";
}
$_SESSION[$modul]['error'] = $_VALID['error'];
$where = ($where) ? implode(" AND ", $where) : "1=1";
//List Hook After Where
$sql = "SELECT red_button_id, red_button.tablename, red_button.replace_from, red_button.replace_to, red_button.is_config, red_button.is_active, red_button.error FROM red_button WHERE " . $where . " ORDER BY " . $orderBy . (($_VALID['limit']) ? " LIMIT 0, " . $_VALID['limit'] : "");
$_SESSION[$modul]['sql'] = $sql;
$listResult = mysqli_query($con, $sql);

if (!$headless) {
?>
<div class="contentheadline"><?php echo ss('Red_button')?></div>
<br>
<div class="contenttext">
    <span class="limit" onclick="setLimit(50);$(this).css({'font-weight':'bold'});" style="cursor: pointer; cursor: hand; font-weight: <?php echo (($_VALID['limit'] == 50)?'bold':'normal')?>">50</span>&nbsp;&nbsp;
    <span class="limit" onclick="setLimit(200);$(this).css({'font-weight':'bold'});" style="cursor: pointer; cursor: hand; font-weight: <?php echo (($_VALID['limit'] == 200)?'bold':'normal')?>">200</span>&nbsp;&nbsp;
    <span class="limit" onclick="setLimit(9999);$(this).css({'font-weight':'bold'});"style="cursor: pointer; cursor: hand; font-weight: <?php echo (($_VALID['limit'] == 9999)?'bold':'normal')?>">∞</span>&nbsp;&nbsp;
    <input type="hidden" class="search" name="limit" id="limit" value="<?php echo $_VALID['limit']?>"><br><br>
<!-- hook vor red_button liste -->
<table cellspacing="0" cellpadding="0" class="bw">
<?php
}
if (!$headless) {
    echo '<tr class="head">';
    echo '<th class="grey" nowrap="nowrap"><a href="javascript:void(0)" onClick="changeSort(\'red_button_id\')">' . ss('Red_button_id') . '</a>&nbsp;&nbsp;<br>
           <input class="search" name="red_button_id" id="red_button_id" value="'.$_SESSION[$modul]['red_button_id'].'">
           </th>';
    echo '<th class="grey" nowrap="nowrap"><a href="javascript:void(0)" onClick="changeSort(\'tablename\')">' . ss('Tablename') . '</a>&nbsp;&nbsp;<br>
           <input class="search" name="tablename" id="tablename" value="'.$_SESSION[$modul]['tablename'].'">
           </th>';
    echo '<th class="grey" nowrap="nowrap"><a href="javascript:void(0)" onClick="changeSort(\'replace_from\')">' . ss('Replace_from') . '</a>&nbsp;&nbsp;<br>
           <input class="search" name="replace_from" id="replace_from" value="'.$_SESSION[$modul]['replace_from'].'">
           </th>';
    echo '<th class="grey" nowrap="nowrap"><a href="javascript:void(0)" onClick="changeSort(\'replace_to\')">' . ss('Replace_to') . '</a>&nbsp;&nbsp;<br>
           <input class="search" name="replace_to" id="replace_to" value="'.$_SESSION[$modul]['replace_to'].'">
           </th>';
    echo '<th class="grey" nowrap="nowrap"><a href="javascript:void(0)" onClick="changeSort(\'is_config\')">' . ss('Is_config') . '</a>&nbsp;&nbsp;<br>
                <input type="radio" name="is_config" id="is_config" class="search" value="_filter_0_"'.(($_SESSION[$modul]['is_config'] == '_filter_0_')?' checked="checked"':'').'>
                <input type="radio" name="is_config" id="is_config" class="search" value="_filter_1_"'.(($_SESSION[$modul]['is_config'] == '_filter_1_')?' checked="checked"':'').'>
           </th>';
    echo '<th class="grey" nowrap="nowrap"><a href="javascript:void(0)" onClick="changeSort(\'is_active\')">' . ss('Is_active') . '</a>&nbsp;&nbsp;<br>
                <input type="radio" name="is_active" id="is_active" class="search" value="_filter_0_"'.(($_SESSION[$modul]['is_active'] == '_filter_0_')?' checked="checked"':'').'>
                <input type="radio" name="is_active" id="is_active" class="search" value="_filter_1_"'.(($_SESSION[$modul]['is_active'] == '_filter_1_')?' checked="checked"':'').'>
           </th>';
    echo '<th class="grey" nowrap="nowrap"><a href="javascript:void(0)" onClick="changeSort(\'error\')">' . ss('Error') . '</a>&nbsp;&nbsp;<br>
           <input class="search" name="error" id="error" value="'.$_SESSION[$modul]['error'].'">
           </th>';
    echo '<th><span onclick="resetFilter();" style="cursor: pointer; cursor: hand;"><img src="css/icon/delete_icon&16.png" title="' . ss('Reset Filter') . '"></span></th>';
    echo '</tr><tbody id="list_tbody">';
}
  if (!$listResult) {
    echo '<tr><td colspan="3">'.ss('No entries found').'</td></tr>';
  } else {
    $i = 0;
	while($row = mysqli_fetch_array($listResult)) {
		echo '<tr class="dotted ' .  ((($i++ % 2)==0) ? "tr_even":"tr_odd") . '" id="tr_'.$row['red_button_id'].'">';
			echo '<td '.$mouseover.' onClick="location.href=\'red_button_d.php?i='.$index.'&amp;red_button_id='.$row['red_button_id'].'\'" nowrap>' . str_limit(htmlspecialchars($row['red_button_id'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['tablename'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['replace_from'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['replace_to'])) . '</td>';
			echo '<td nowrap>' . (($row['is_config'] == '1') ? ss('yes') : ss('no')) . '</td>';
			echo '<td nowrap>' . (($row['is_active'] == '1') ? ss('yes') : ss('no')) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['error'])) . '</td>';
            echo '<td nowrap><a href="red_button_d.php?i=&amp;red_button_id=' . (int) $row['red_button_id'] . '"><img src="css/icon/pencil_icon&16.png" title="' . ss('Edit') . '"></a>';
// people with right to delete see the delete button
    if (R(3))
            echo '&nbsp;&nbsp;<a href="#" onclick="if (confirm(\'' . ss('Do you really want to delete the Red_button?') . '\')) delRow('.$row['red_button_id'].');">
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
      url: 'a/red_button_del.php?id='+pk
    });
    $('#tr_'+pk).hide();
}

var sortcol = 'red_button_id';
var sortdir = '';
var del = '';
function updateList() {
    var url = '<?=$_REQUEST['PHP_SELF']?>?headless&sortcol='+sortcol+'&sortdir='+sortdir+'&del='+del;
    var filterparams = '';


    // inputs
    var val = '';
    $('.search:input:not([type=radio])').each(function(index, obj) {
        val = $('#' + obj.name).val();
        if (val != '') filterparams += '&' + obj.name + '=' + encodeURIComponent($('#' + obj.name).val());
    });
    
    $('.search:input[type=radio]').each(function(index, obj) {
        val = $('#' + obj.name + ':checked').val();
        if (typeof val !== 'undefined') filterparams += '&' + obj.name + '=' + val;
    });

    $('.bw select').each(function(index, obj) {
        val = $("#" + obj.name).val();
        if (val != '') filterparams += '&' + obj.name + '=' + encodeURIComponent($('#' + obj.name).val());
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

function resetFilter() {
    $('.search:input').each(function(index, obj) {
        $('#' + obj.name).val('');
    });

    $('.search:input[type=radio]').each(function(index, obj) {
        $('#' + obj.name + ':checked').removeAttr('checked');
    });
    updateList();
}

function setLimit(limit) {
    $('#limit').val(limit);
    $('.limit').css({'font-weight':'normal'});
    updateList();
}

$('.search:input').keyup(function(index) {
    updateList();
});
$('.search:input').change(function(index) {
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
  $(".dotted td").on("click", function() {
        $(".dotted").removeClass("selected");
        var tr = $(this).parent();
        if(tr.hasClass("selected")) {
            tr.removeClass("selected");
        } else {
            localStorage.setItem("<?Php echo $modul; ?>_highlight", tr.attr('id'));
            tr.addClass("selected");
        }
    });

    if(localStorage.getItem("<?Php echo $modul; ?>_highlight")!=""){
        $("#"+localStorage.getItem("<?Php echo $modul; ?>_highlight")).addClass("selected");
    }

});

// hook red_button javascript
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