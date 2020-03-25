<?php 
    $modul="place";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to view place
$groupID = 26;
GRGR($groupID);


// include module if exists
if (file_exists(MODULE_ROOT.'place/place.php')) {
    require MODULE_ROOT.'place/place.php';
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

$orderBy = ($_VALID['sortcol'])?$_VALID['sortcol'] .(($_VALID['sortdir'])?' ' . $_VALID['sortdir']:''):'name';
$orderBy = mysqli_real_escape_string ( $con , $orderBy );
    
$headless = (isset($_REQUEST['headless']))?true:false;
$n4a['place_d.php'] = '' . ss('Add Place') . '';
if (!$headless) require("inc/header.inc.php");

/*** Validation ***/

// Place_id
validate('place_id', 'int nullable' );
$_SESSION[$modul]['place_id'] = $_VALID[' place_id'];

// Name
validate('name', 'string' );
$_SESSION[$modul]['name'] = $_VALID[' name'];

// Lat
validate('lat', 'numeric' );
$_SESSION[$modul]['lat'] = $_VALID[' lat'];

// Lon
validate('lon', 'numeric' );
$_SESSION[$modul]['lon'] = $_VALID[' lon'];

// Qr_code
validate('qr_code', 'string nullable' );
$_SESSION[$modul]['qr_code'] = $_VALID[' qr_code'];
if (isset($_REQUEST['submitted']) AND is_array($_MISSING) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}
// delete
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM place WHERE place_id = " . (int) $_REQUEST['place_id'];
	mysqli_query($con, $sql) or error_log(mysqli_error());
}

// where condition
if ($_VALID['place_id']) {
	$where[] = "place.place_id =  " . $_VALIDDB['place_id'];
}
$_SESSION[$modul]['place_id'] = $_VALID['place_id'];
if ($_VALID['name']) {
	$where[] = "place.name LIKE '%" . mysqli_real_escape_string($con, $_VALID['name']) . "%'";
}
$_SESSION[$modul]['name'] = $_VALID['name'];
if ($_VALID['lat']) {
	$where[] = "place.lat =  '" . mysqli_real_escape_string($con, $_VALID['lat']) . "'";
}
$_SESSION[$modul]['lat'] = $_VALID['lat'];
if ($_VALID['lon']) {
	$where[] = "place.lon =  '" . mysqli_real_escape_string($con, $_VALID['lon']) . "'";
}
$_SESSION[$modul]['lon'] = $_VALID['lon'];
if ($_VALID['qr_code']) {
	$where[] = "place.qr_code LIKE '%" . mysqli_real_escape_string($con, $_VALID['qr_code']) . "%'";
}
$_SESSION[$modul]['qr_code'] = $_VALID['qr_code'];
$where = ($where) ? implode(" AND ", $where) : "1=1";
//List Hook After Where
$sql = "SELECT place_id,
place.name,
place.lat,
place.lon,
place.qr_code
FROM place
WHERE " . $where . "
ORDER BY " . $orderBy . (($_VALID['limit']) ? "
LIMIT 0, " . $_VALID['limit'] : "");
$_SESSION[$modul]['sql'] = $sql;
$listResult = mysqli_query($con, $sql);

if (!$headless) {
?>
<div class="contentheadline"><?php echo ss('Place')?></div>
<br>
<div class="contenttext">
    <span class="limit" onclick="setLimit(50);$(this).css({'font-weight':'bold'});" style="cursor: pointer; cursor: hand; font-weight: <?php echo (($_VALID['limit'] == 50)?'bold':'normal')?>">50</span>&nbsp;&nbsp;
    <span class="limit" onclick="setLimit(200);$(this).css({'font-weight':'bold'});" style="cursor: pointer; cursor: hand; font-weight: <?php echo (($_VALID['limit'] == 200)?'bold':'normal')?>">200</span>&nbsp;&nbsp;
    <span class="limit" onclick="setLimit(9999);$(this).css({'font-weight':'bold'});"style="cursor: pointer; cursor: hand; font-weight: <?php echo (($_VALID['limit'] == 9999)?'bold':'normal')?>">∞</span>&nbsp;&nbsp;
    <input type="hidden" class="search" name="limit" id="limit" value="<?php echo $_VALID['limit']?>"><br><br>
<!-- hook vor place liste -->
<table cellspacing="0" cellpadding="0" class="bw">
<?php
}
if (!$headless) {
    echo '<tr class="head">';
    echo '<th class="grey" nowrap="nowrap" style=""><a href="javascript:void(0)" onClick="changeSort(\'place_id\')">' . ss('Place_id') . '</a>&nbsp;&nbsp;<br>
           <input class="search" name="place_id" id="place_id" value="'.$_SESSION[$modul]['place_id'].'" autocomplete="off" style="width:50px">
           </th>';
    echo '<th class="grey" nowrap="nowrap"><a href="javascript:void(0)" onClick="changeSort(\'name\')">' . ss('Name') . '</a>&nbsp;&nbsp;<br>
           <input class="search" name="name" id="name" value="'.$_SESSION[$modul]['name'].'" autocomplete="off">
           </th>';
    echo '<th class="grey" nowrap="nowrap"><a href="javascript:void(0)" onClick="changeSort(\'lat\')">' . ss('Lat') . '</a>&nbsp;&nbsp;<br>
           <input class="search" name="lat" id="lat" value="'.$_SESSION[$modul]['lat'].'" autocomplete="off">
           </th>';
    echo '<th class="grey" nowrap="nowrap"><a href="javascript:void(0)" onClick="changeSort(\'lon\')">' . ss('Lon') . '</a>&nbsp;&nbsp;<br>
           <input class="search" name="lon" id="lon" value="'.$_SESSION[$modul]['lon'].'" autocomplete="off">
           </th>';
    echo '<th class="grey" nowrap="nowrap"><a href="javascript:void(0)" onClick="changeSort(\'qr_code\')">' . ss('Qr_code') . '</a>&nbsp;&nbsp;<br>
           <input class="search" name="qr_code" id="qr_code" value="'.$_SESSION[$modul]['qr_code'].'" autocomplete="off">
           </th>';
    echo '<th><span onclick="resetFilter();" style="cursor: pointer; cursor: hand;"><img src="css/icon/delete_icon&16.png" title="' . ss('Reset Filter') . '"></span></th>';
    echo '</tr><tbody id="list_tbody">';
}
  if (!$listResult) {
    echo '<tr><td colspan="3">'.ss('No entries found').'</td></tr>';
  } else {
    $i = 0;
	while($row = mysqli_fetch_array($listResult)) {
		echo '<tr class="dotted ' .  ((($i++ % 2)==0) ? "tr_even":"tr_odd") . '" id="tr_'.$row['place_id'].'">';
			echo '<td '.$mouseover.' onClick="location.href=\'place_d.php?i='.($i-1).'&amp;place_id='.$row['place_id'].'\'" nowrap>' . str_limit(htmlspecialchars($row['place_id'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['name'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['lat'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['lon'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['qr_code'])) . '</td>';
            echo '<td nowrap><a href="place_d.php?i=&amp;place_id=' . (int) $row['place_id'] . '"><img src="css/icon/pencil_icon&16.png" title="' . ss('Edit') . '"></a>';
// people with right to delete see the delete button
    if (R(3))
            echo '&nbsp;&nbsp;<a href="#" onclick="if (confirm(\'' . ss('Do you really want to delete the Place?') . '\')) delRow('.$row['place_id'].');">
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
      url: 'a/place_del.php?id='+pk
    });
    $('#tr_'+pk).hide();
}

var sortcol = 'name';
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

// hook place javascript
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