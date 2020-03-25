<?php 
    $modul="fall";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to view fall
$groupID = 26;
GRGR($groupID);


// include module if exists
if (file_exists(MODULE_ROOT.'fall/fall.php')) {
    require MODULE_ROOT.'fall/fall.php';
}

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

$orderBy = ($_VALID['sortcol'])?$_VALID['sortcol'] .(($_VALID['sortdir'])?' ' . $_VALID['sortdir']:''):'fall_id';
$orderBy = mysqli_real_escape_string ( $con , $orderBy );
    
$headless = (isset($_REQUEST['headless']))?true:false;
$n4a['fall_d.php'] = '' . ss('Add Fall') . '';
if (!$headless) require("inc/header.inc.php");

/*** Validation ***/

// Fall_id
validate('fall_id', 'int nullable' );
$_SESSION[$modul]['fall_id'] = $_VALID[' fall_id'];

// Transfer_date
validate('transfer_date', 'string' );
$_SESSION[$modul]['transfer_date'] = $_VALID[' transfer_date'];
if (isset($_REQUEST['submitted']) AND is_array($_MISSING) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}
// delete
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM fall WHERE fall_id = " . (int) $_REQUEST['fall_id'];
	mysqli_query($con, $sql) or error_log(mysqli_error());
}

// where condition
if ($_VALID['fall_id']) {
	$where[] = "fall.fall_id =  " . $_VALIDDB['fall_id'];
}
$_SESSION[$modul]['fall_id'] = $_VALID['fall_id'];
if ($_VALID['transfer_date']) {
	$where[] = "fall.transfer_date LIKE '%" . mysqli_real_escape_string($con, $_VALID['transfer_date']) . "%'";
}
$_SESSION[$modul]['transfer_date'] = $_VALID['transfer_date'];
$where = ($where) ? implode(" AND ", $where) : "1=1";
//List Hook After Where
$sql = "SELECT fall_id,
fall.transfer_date
FROM fall
WHERE " . $where . "
ORDER BY " . $orderBy . (($_VALID['limit']) ? "
LIMIT 0, " . $_VALID['limit'] : "");
$_SESSION[$modul]['sql'] = $sql;
$listResult = mysqli_query($con, $sql);

if (!$headless) {
?>
<div class="contentheadline"><?php echo ss('Fall')?></div>
<br>
<div class="contenttext">
    <span class="limit" onclick="setLimit(50);$(this).css({'font-weight':'bold'});" style="cursor: pointer; cursor: hand; font-weight: <?php echo (($_VALID['limit'] == 50)?'bold':'normal')?>">50</span>&nbsp;&nbsp;
    <span class="limit" onclick="setLimit(200);$(this).css({'font-weight':'bold'});" style="cursor: pointer; cursor: hand; font-weight: <?php echo (($_VALID['limit'] == 200)?'bold':'normal')?>">200</span>&nbsp;&nbsp;
    <span class="limit" onclick="setLimit(9999);$(this).css({'font-weight':'bold'});"style="cursor: pointer; cursor: hand; font-weight: <?php echo (($_VALID['limit'] == 9999)?'bold':'normal')?>">∞</span>&nbsp;&nbsp;
    <input type="hidden" class="search" name="limit" id="limit" value="<?php echo $_VALID['limit']?>"><br><br>
<!-- hook vor fall liste -->
<table cellspacing="0" cellpadding="0" class="bw">
<?php
}
if (!$headless) {
    echo '<tr class="head">';
    echo '<th class="grey" nowrap="nowrap"><a href="javascript:void(0)" onClick="changeSort(\'fall_id\')">' . ss('Fall_id') . '</a>&nbsp;&nbsp;<br>
           <input class="search" name="fall_id" id="fall_id" value="'.$_SESSION[$modul]['fall_id'].'" autocomplete="off" style="width:50px">
           </th>';
    echo '<th class="grey" nowrap="nowrap"><a href="javascript:void(0)" onClick="changeSort(\'transfer_date\')">' . ss('Transfer_date') . '</a>&nbsp;&nbsp;<br>
           <input class="search" name="transfer_date" id="transfer_date" value="'.$_SESSION[$modul]['transfer_date'].'" autocomplete="off">
           </th>';
    echo '<th><span onclick="resetFilter();" style="cursor: pointer; cursor: hand;"><img src="css/icon/delete_icon&16.png" title="' . ss('Reset Filter') . '"></span></th>';
    echo '</tr><tbody id="list_tbody">';
}
  if (!$listResult) {
    echo '<tr><td colspan="3">'.ss('No entries found').'</td></tr>';
  } else {
    $i = 0;
	while($row = mysqli_fetch_array($listResult)) {
		echo '<tr class="dotted ' .  ((($i++ % 2)==0) ? "tr_even":"tr_odd") . '" id="tr_'.$row['fall_id'].'">';
			echo '<td '.$mouseover.' onClick="location.href=\'fall_d.php?i='.($i-1).'&amp;fall_id='.$row['fall_id'].'\'" nowrap>' . str_limit(htmlspecialchars($row['fall_id'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['transfer_date'])) . '</td>';
            echo '<td nowrap><a href="fall_d.php?i=&amp;fall_id=' . (int) $row['fall_id'] . '"><img src="css/icon/pencil_icon&16.png" title="' . ss('Edit') . '"></a>';
// people with right to delete see the delete button
    if (R(3))
            echo '&nbsp;&nbsp;<a href="#" onclick="if (confirm(\'' . ss('Do you really want to delete the Fall?') . '\')) delRow('.$row['fall_id'].');">
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
      url: 'a/fall_del.php?id='+pk
    });
    $('#tr_'+pk).hide();
}

var sortcol = 'fall_id';
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

// hook fall javascript
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