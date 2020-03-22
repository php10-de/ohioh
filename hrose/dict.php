<?php
$modul="dict";

require("inc/req.php");
validate("limit","int");
validate("del","int");
validate("xls_export","int");
validate("sortcol","string");
validate("sortdir","string");
if (isset($_REQUEST['sortcol'])) {
   $_SESSION[$modul]['sortcol'] = $_REQUEST['sortcol'];
}
if (isset($_REQUEST['sortdir'])) {
   $_SESSION[$modul]['sortdir'] = $_REQUEST['sortdir'];
}
if (isset($_REQUEST['limit'])) {
   $_SESSION[$modul]['limit'] = $_REQUEST['limit'];
}
validate("de","string");
validate("en","string");
validate("gr","string");
validate("gr_id","string");

/*** Rights ***/
// Generally for people in the 'Translation' group
GRGR(5);

$n4a['dict.php?refresh'] = ss('Regenerate Cache');
$n4a['dict_d.php'] = ss('Add word');
//$n4a['dict_list.php?xls_export=1'] = ss('xls Export');
$headless = (isset($_REQUEST['headless']))?true:false;
$nav5top = true;
if (!$headless) require("inc/header.inc.php");
/***** Löschen *****/
if ($_VALID['del'] && DICT_ADMIN) {
    $sql = "DELETE FROM dict WHERE dict_id=".$_VALID['del'];
    // Only Administrators can edit admin words
    if (!GR(1)) $sql .= " AND gr_id != 1";
    
    mysqli_query($con, $sql);
}
// Ergebnis aufbauen ------- //
// Artikel auslesen
// Bei Excel-Export auch Unterartikel
$dict_sql="SELECT dict_id, ID, en, de, gr, gr_id
		FROM dict WHERE 1=1";

if (isset($_REQUEST['refresh'])) {
    // Cache
    $csql = "SELECT * FROM dict WHERE 1=1";
    $cresult = mysqli_query($con, $csql);
    while ($row = mysqli_fetch_array($cresult)) {
        $de[] = "'".$row['ID']."' => '".addslashes($row['de'])."'";
        $en[$row['ID']] = $row['en'];
        $gr[$row['ID']] = $row['gr'];
    }

    $s = '<?php ';
    $s .= '$DE = array(';
    $s .= implode(",",$de);
    $s .= ");";
    $s .= ' ?>';
    $fp = fopen('inc/de.inc.php', 'w');
    fwrite($fp, $s);
    fclose($fp);
    //file_put_contents('inc/de.inc.php', $s);
}

/* --- Filter --- */
// Only Administrators can edit admin words
if (!GR(1)) {
    $dict_sql .= " AND (gr_id IS NULL or gr_id != 1)";
}
if($_VALID['de']) {
    $dict_sql.=" AND de LIKE '".$_VALID['de']."%'";
    $_SESSION['dict']['de'] = $_VALID['de'];
}
if($_VALID['gr']) {
    $dict_sql.=" AND gr LIKE '".$_VALID['gr']."%'";
    $_SESSION['dict']['gr'] = $_VALID['gr'];
}
if($_VALID['en']) {
    $dict_sql.=" AND (ID LIKE '".$_VALID['en']."%'";
    $dict_sql.=" OR en LIKE '".$_VALID['en']."%')";
    $_SESSION['dict']['en'] = $_VALID['en'];
}
if($_VALID['gr_id']) {
    $dict_sql.=" AND gr_id LIKE '".$_VALID['gr_id']."%'";
    $_SESSION['dict']['gr_id'] = $_VALID['gr_id'];
}
/***** Sortierung *****/
if($_VALID['sortcol']!="") {
    $dict_sql.=  " ORDER BY " . $_VALID['sortcol'] . " ".$_VALID['sortdir'];
    $_SESSION['dict']['sortcol'] = $_VALID['sortcol'];
    $_SESSION['dict']['sortdir'] = $_VALID['sortdir'];
}
// Bl?tter Funktion ------- //
if($_VALID['limit']!="") {
    $dict_sql.=  " LIMIT 0, " .$_VALID['limit'];
    $_SESSION['dict']['limit'] = $_VALID['limit'];
}
  $contents.= " German \t Chinese \t English \t \n";
//echo $dict_sql;
$dict_query=mysqli_query($con, $dict_sql);
$num_rows = mysqli_num_rows($dict_query);

//echo '<h1>Dictionary</h1>';
if (!$headless) {?>
<div class="contentheadline"><?php echo ss('Dictionary')?></div><br>
<div id="loading-image" style="position: absolute; top: 140px; left: 450px;"><img alt="Loading..." src="images/ajax-loader.gif" style=""></div>
<div class="contenttext">
    Achtung: das Ändern ist aktuell nur bei ungefilterter Liste möglich.<br><br>
    <a href="dict.php?limit=50"><?php echo (($_VALID['limit'] == 50)?'<b>50</b>':'50')?></a>&nbsp;&nbsp;
    <a href="dict.php?limit=200"><?php echo (($_VALID['limit'] == 200)?'<b>200</b>':'200')?></a>&nbsp;&nbsp;
    <a href="dict.php?limit=9999"><?php echo (($_VALID['limit'] == 9999)?'<b>∞</b>':'∞')?></a>&nbsp;&nbsp;<br><br>
<?php
echo '<table cellpadding=0 cellspacing=0 class="bw">';
echo '<tr class="head grey">
	  <th height="30" align="left"><a href="#" onClick="changeSort(\'en\')">'.ss('English').'</a><br><input class=search type=text name=en id=en value="'.html($_VALID["en"])  . '"></th>
	  <th height="30" align="left"><a href="#" onClick="changeSort(\'de\')">'.ss('German').'</a><br><input class=search type=text name=de id=de value="'.html($_VALID["de"])  . '"></th>
	  <th height="30" align="left"><a href="#" onClick="changeSort(\'gr\')">'.ss('Greek').'</a><br><input class=search type=text name=gr id=gr value="'.html($_VALID["gr"])  . '"></th>
	  <th height="30" align="left"></td>
	  </tr>
	  <tbody id="list_tbody">';
}
	  $nt=0;$i=0;
    if($_VALID['xls_export']!=1) {
while($row=mysqli_fetch_array($dict_query)) {
	$nt++;	
		if($nt%2==0)
		{
		$class = " class='dotted grey'";
		}
		else
		{
		$class = " class='dotted'";
		}
    if($_VALID['xls_export']==1) {

      $contents.=iconv("UTF-8", "UTF-16LE//IGNORE", $row['1'])."\t ".$help_tradition ."\t  ".iconv("UTF-8", "UTF-16LE//IGNORE", $row['3'])."\n ";

    }else {
	
        // Tabelleninhalt erstellen
        $caseview.="<tr $class id='".$row['0']."' >";
        $caseview.="<td class='editable'><input type='text' value='".(($row['en'])?html($row['en']):html($row['ID']))."' class='en'></td>
                    <td class='editable'><input type='text' value='".html($row['de'])."' class='de'></td>
                    <td class='editable'><input type='text' value='".html($row['gr'])."' class='gr'></td>";
        $caseview.="<td>&nbsp;";
        $caseview .= "<a class='button' href='#' onclick=\"delRow(".$row[0].");\"><img src=\"css/icon/delete_icon&16.png\" title=\"" . ss('Delete') . "\"></a>";

        $caseview.="</td></tr>";

    }
	}
}
	
/***** Excel-Export *****/
if($_VALID["xls_export"]==1) {

  $filename ="pso_dict_".date('d-m-y').".xls";
  header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header('Content-Type: text/html; charset=UTF-8');
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");;
    header("Content-Disposition: attachment;filename=$filename "); // แล้วนี่ก็ชื่อไฟล์
    header("Content-Transfer-Encoding: UTF-8");

	echo '<html>
<head>
<meta http-equiv="Content-Type" CONTENT="application/vnd.ms-excel">
<meta http-equiv="Content-Disposition" CONTENT="inline">
</head>
<body>
<table border=1>';
 echo '<tr>';
  echo '<td><strong>German</strong></td>';
  echo '<td><strong>Chinese</strong></td>';
  echo '<td><strong>English</strong></td>';
 echo '</tr>';
 while($row=mysqli_fetch_row($dict_query)) {
 echo '<tr>';
  echo '<td>'.$row[1].'</td>';
  echo '<td>'.$row[2].'</td>';
  echo '<td>'.$row[3].'</td>'; echo '</tr>';
 }
 
 echo '</table></body></html>';
  print($help_simplified.'<br/>');
  print($help_tradition.'<br/>');

 
   /* xlsBOF();
                xlsWriteLabel(0,0,"German");
                xlsWriteLabel(0,1,"Chinese");
      
                xlsWriteLabel(0,2,"English");
              
                $xlsRow = 1;
				while($row=mysqli_fetch_row($dict_query)) {
               
                    ++$i;
                          xlsWriteLabel($xlsRow,0,"1");
                          xlsWriteLabel($xlsRow,1,$row[2]);
                          xlsWriteLabel($xlsRow,2,"2");
                         
                    $xlsRow++;
                    }
                     xlsEOF();*/
                 exit();

/*header('Content-type:application/ms-excel; charset=UTF-8');
   header('Content-type: application/ms-excel');
    header('Content-Disposition: attachment; filename='.$filename);
    echo $contents;
    die();*/
		

}

if($_VALID['xls_export']!=1) {

    if($num_rows > 0) {

        $view_duration = ($scroll_page["anfang"]+$scroll_page["duration"]);
        if($scroll_page["page_rows"]< $view_duration) {
            $view_duration = $scroll_page["page_rows"];
        }


    }else {
        echo "<tr><td colspan=13 class='whitebg'><p class='advice'>No translations available</p></td></tr>";
    }

}
echo $caseview;
if (!$headless) { ?>
</tbody></table></div>
<script type="text/javascript">

    jQuery(document).ready(function(){
        $("td.editable textarea, td.editable input").focus(function(){
		 
            $(this).addClass("edit");
        });
        $("td.editable textarea, td.editable input").blur(function(){
            $(this).removeClass("edit");
			
       
            $.ajax({
                url: "a/dict_edit.php?action=edit",
                data: "elem="+$(this).attr("class")+"&value="+$(this).val()+"&dict_id="+$(this).parent().parent().attr("id"),
                type: "POST",
				success: function(data) {}
				
            });
        });
    });
	
</script>

<script type="text/javascript">
    var sortcol = 'de';
    var sortdir = 'DESC';
    var del = '';
    function updateList() {
        var url = 'dict.php?headless&sortcol='+sortcol+'&sortdir='+sortdir+'&del='+del;
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
    function changeSort(col) {

        if (sortcol == col) {
            sortdir = (sortdir == 'DESC') ? 'ASC' : 'DESC';
        } else {
            sortdir = 'DESC';
        }
        sortcol = col;

        updateList();


    }
    function delRow(pk) {
        del=pk;
        updateList();
    }

    $('.search:input').keyup(function(index) {
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