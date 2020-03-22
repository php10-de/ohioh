<?php 
    $modul="cache";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to change cache
RR(2);


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
$orderBy = ($_VALID['sortcol'])?$_VALID['sortcol'] .(($_VALID['sortdir'])?' ' . $_VALID['sortdir']:''):'cache_id';
    
$headless = (isset($_REQUEST['headless']))?true:false;
$n4a['cache_d.php'] = '' . ss('Add Cache') . '';
if (!$headless) require("inc/header.inc.php");

/*** Validation ***/

// Cache_id
validate('cache_id', 'int nullable' );
$_SESSION['cache_id'] = $_VALID[' cache_id'];

// Url
validate('url', 'string' );
$_SESSION['url'] = $_VALID[' url'];

// Updated
validate('updated', 'string nullable' );
$_SESSION['updated'] = $_VALID[' updated'];

// Active
validate('active', 'int nullable' );
$_SESSION['active'] = $_VALID[' active'];
if (isset($_REQUEST['submitted']) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}
// delete
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM cache WHERE cache_id = " . (int) $_REQUEST['cache_id'];
	mysqli_query($con, $sql) or error_log(mysqli_error());
}

// where condition
if ($_VALID['cache_id']) {
	$where[] = "cache.cache_id =  " . $_VALIDDB['cache_id'];
}
$_SESSION[$modul]['cache_id'] = $_VALID['cache_id'];
if ($_VALID['url']) {
	$where[] = "cache.url LIKE '%" . mysqli_real_escape_string($con, $_VALID['url']) . "%'";
}
$_SESSION[$modul]['url'] = $_VALID['url'];
if ($_VALID['updated']) {
	$where[] = "cache.updated LIKE '%" . mysqli_real_escape_string($con, $_VALID['updated']) . "%'";
}
$_SESSION[$modul]['updated'] = $_VALID['updated'];
if ($_VALID['active']) {
	$where[] = "cache.active =  " . $_VALIDDB['active'];
}
$_SESSION[$modul]['active'] = $_VALID['active'];
$where = ($where) ? implode(" AND ", $where) : "1=1";
$sql = "SELECT cache.cache_id, cache.url, cache.updated, cache.active FROM cache WHERE " . $where . " ORDER BY " . $orderBy;
$listResult = mysqli_query($con, $sql);

if (!$headless) {
?>
<div class="contentheadline"><?php echo ss('Cache')?></div>
<br>
<div class="contenttext">
<!-- hook vor cache liste -->
<table cellspacing="0" cellpadding="0" class="bw">
<?php
}
if (!$headless) {
    echo '<tr class="head">';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'cache_id\')">' . ss('cache_id') . '</a>&nbsp;&nbsp;
           <input class="search" name="cache_id" id="cache_id" value="'.$_SESSION[$modul]['Cache_id'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'url\')">' . ss('url') . '</a>&nbsp;&nbsp;
           <input class="search" name="url" id="url" value="'.$_SESSION[$modul]['Url'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'updated\')">' . ss('updated') . '</a>&nbsp;&nbsp;
           <input class="search" name="updated" id="updated" value="'.$_SESSION[$modul]['Updated'].'">
           </th>';
    echo '<th class="grey"><a href="javascript:void(0)" onClick="changeSort(\'active\')">' . ss('active') . '</a>&nbsp;&nbsp;
           <input class="search" name="active" id="active" value="'.$_SESSION[$modul]['Active'].'">
           </th>';
    echo '<th>&nbsp;</th>';
    echo '</tr><tbody id="list_tbody">';
}
  if (!$listResult) {
    echo '<tr><td colspan="3">'.ss('No entries found').'</td></tr>';
  } else {
    $i = 0;
	while($row = mysqli_fetch_array($listResult)) {
		echo '<tr class="dotted ' .  ((($i++ % 2)==0) ? "tr_even":"tr_odd") . '" id="tr_'.$row['cache_id'].'">';
			echo '<td '.$mouseover.' onClick="location.href=\'cache_d.php?i='.$index.'&amp;cache_id='.$row['cache_id'].'\'" nowrap>' . htmlspecialchars($row['cache_id']) . '</td>';
			echo '<td nowrap><a href="' . ((strpos($row['url'], 'http') === false) ? 'http://' : '') . $row['url'] . '">' . $row['url'] . '</a></td>';
			echo '<td nowrap>' . htmlspecialchars($row['updated']) . '</td>';
			echo '<td nowrap>' . htmlspecialchars($row['active']) . '</td>';
            echo '<td nowrap><a href="cache_d.php?i=&amp;cache_id=' . (int) $row['cache_id'] . '"><img src="css/icon/pencil_icon&16.png" title="' . ss('Edit') . '"></a>';
// people with right to delete see the delete button
    if (R(3))
            echo '&nbsp;&nbsp;<a href="#" onclick="if (confirm(\'' . ss('Do you really want to delete the Cache?') . '\')) delRow('.$row['cache_id'].');">
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
      url: 'a/cache_del.php?id='+pk
    });
    $('#tr_'+pk).hide();
}

var sortcol = 'cache_id';
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

// hook cache javascript
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