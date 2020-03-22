<?php
header('Content-Type: text/html; charset=UTF-8');
$modul="dict_list";
$area="all";
require("../inc/req.php");
require_once("../inc/dateConv.php");
session_start();

if($_GET['action'] == 'list-changelog') {
    require("../inc/miniheader.inc.php");
    $changelog_sql ="SELECT *
		FROM dict_changelog WHERE dict_id=".$_GET['dict_id'];
    $result = mysqli_query($con, $changelog_sql);
    if(!mysqli_num_rows($result)) {
        echo 'No changes made.';
        exit(0);
    }
    echo '<table class="tabel2" style="width:auto" cellpadding="0" cellspacing="0">>';
    echo '<tr>
	  <td class="t5" height="30" aling="center">Field</td>
	  <td class="t5" height="30" aling="center">Old Value</td>
	  <td class="t5" height="30" aling="center">New Value</td>
	  <td class="t5" height="30" aling="center">User</th>
	  <td class="t5" height="30" aling="center">Last Updated</td>
	  </tr>
	  <tbody id="list_tbody">';
	  $nt=0;
    while($log_row = mysqli_fetch_row($result)) {
	if($nt%2==0)
		{
		$class = " class='redbg'";
		}
		else
		{
		$class = " class='whitebg'";
		}
        $result2 = mysqli_query($con, "SELECT email FROM user WHERE user_id=".$log_row[4]);
        $user = mysqli_fetch_row($result2);
        echo '<tr $class>';
        echo '<td>'.$log_row[1].'</td>';
        echo '<td>'.$log_row[2].'</td>';
        echo '<td>'.$log_row[3].'</td>';
        echo '<td>'.$user[0].'</td>';
        echo '<td>'.dateConv($log_row[5]).'</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    exit(0);
}
validate("page","int");
validate("del","int");
validate("xls_export","int");
validate("sortcol","string");
validate("sortdir","string");
validate("de","string");
validate("en","string");
validate("cn","string");
//define('DICT_ADMIN', ($_SESSION['crt_dict']==1||$_SESSION['is_admin']==1||$_SESSION['is_superadmin']==1));
define('DICT_ADMIN', 1);
if(!$_VALID['page']) {
    $_VALID['page']=1; //F?r Bl?tterfunktion
}
/***** L?schen *****/
if ($_VALID['del'] && DICT_ADMIN) {
    $sql = "DELETE FROM dict WHERE dict_id=".$_VALID['del'];
    mysqli_query($con, $sql);
}
// Ergebnis aufbauen ------- //
// Artikel auslesen
// Bei Excel-Export auch Unterartikel
$dict_sql="SELECT dict_id, de, cn, en, user_id, dbinsert, dbupdate
		FROM dict WHERE 1=1";   
/* --- Filter --- */
if($_VALID['de']) {
    $dict_sql.=" AND de LIKE '".$_VALID['de']."%'";
}
if($_VALID['cn']) {
    $dict_sql.=" AND cn LIKE '".$_VALID['cn']."%'";
}
if($_VALID['en']) {
    $dict_sql.=" AND en LIKE '".$_VALID['en']."%'";
}
/***** Sortierung *****/
if($_VALID['sortcol']!="") {
    $dict_sql.=  " ORDER BY " . $_VALID['sortcol'] . " ".$_VALID['sortdir'];
}
// Bl?tter Funktion ------- //				
  $contents.= " German \t Chinese \t English \t \n";
//echo $dict_sql;
$dict_query=mysqli_query($con, $dict_sql);
$num_rows = mysqli_num_rows($dict_query);
$nt=0;$i=0;
    if($_VALID['xls_export']!=1) {
while($row=mysqli_fetch_row($dict_query)) {
	$nt++;	
		if($nt%2==0)
		{
		$class = " class='redbg'";
		}
		else
		{
		$class = " class='whitebg'";
		}
    if($_VALID['xls_export']==1) {

      $contents.=iconv("UTF-8", "UTF-16LE//IGNORE", $row['1'])."\t ".$help_tradition ."\t  ".iconv("UTF-8", "UTF-16LE//IGNORE", $row['3'])."\n ";

    }else {
	
        // Tabelleninhalt erstellen
        $caseview.="<tr $class id='".$row['0']."' >";
        if (DICT_ADMIN) {
            $caseview.="<td class='editable'><input type='text' value='".$row[1]."' class='de'>".$_SESSION['report_values'][$i][0]."</td>
					<td class='editable'><input type='text' value='".htmlspecialchars($row[2], ENT_QUOTES, 'UTF-8')."' class='cn'></td>
					<td class='editable'><input type='text' value='".$row[3]."' class='en'>".$_SESSION['report_values'][$i][2]."</td>";
            $caseview.="<td>&nbsp;";
            $caseview .= "<a class='ajax editfancy' href='ajax/dict_list.php?action=list-changelog&dict_id=".$row[0]."'><img src='img/icon/details.png'></a>&nbsp;
					    <a class='button' href='#' onclick=\"delRow(".$row[0].");\"><input type='button' name='but1' value='Delete' class='but1'></a>"; 
        } else {
            $caseview.="<td>".$row[1]."</td>
					<td>".$row[2]."</td>
					<td>".$row[3]."</td>";
            $caseview.="<td>&nbsp;";
        }

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
    header("Content-Disposition: attachment;filename=$filename "); // à¹à¸¥à¹‰à¸§à¸™à¸µà¹ˆà¸à¹‡à¸Šà¸·à¹ˆà¸­à¹„à¸Ÿà¸¥à¹Œ
    header("Content-Transfer-Encoding: UTF-8");
$help_simplified = '这是一份非常间单的说明书…';
  $help_tradition = '這是一份非常間單的說明書…';
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
		

}else {
      $caseview .= '<tr style="height:50px" class="add_row"><td colspan=4 style="border:0px" vAlign=bottom>';
    $caseview .= "<a href='ajax/dict_edit.php' class='addword button iframe' id='addword_bottom' style='margin-right:5px;'><input name='button' type='button' class='but1' id='button' value='Add +' /></	a>&nbsp;";
    //if($num_rows > 0) {
        $caseview .= "&nbsp;<a class='button' id='xlsbutton' href='ajax/dict_list.php?sortcol=".$_VALID['sortcol']."&sortdir=".$_VALID['sortdir']."&page=".$_VALID['page']."&xls_export=1'><input name='button2' type='button' class='but1' id='button' value='xls Export' /></a>";
    //}

    $caseview .= '</td></tr>';
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
?>

<script type="text/javascript">

    jQuery(document).ready(function(){
        // Init Fancybox
        $("#addword_bottom").fancybox({
            'transitionIn'	:	'elastic',
            'transitionOut'	:	'elastic',
            'speedIn'		:	600,
            'speedOut'		:	200,
            'width'			:	935,
            'height'		:	530,
            'autoScale'		:	true,
            'overlayShow'	:	false
        });
        $(".editfancy.ajax").fancybox({
            'type'				: 'iframe',
            'autoScale'			:	true
        });
        $("td.editable textarea, td.editable input").focus(function(){
		 
            $(this).addClass("edit");
        });
        $("td.editable textarea, td.editable input").blur(function(){
            $(this).removeClass("edit");
			
       
            $.ajax({
                url: "ajax/dict_edit.php?action=edit",
                data: "elem="+$(this).attr("class")+"&value="+$(this).val()+"&dict_id="+$(this).parent().parent().attr("id"),
                type: "POST",
				success: function(data) {//alert(data);}
                                    
                                }
				
            });
        });
    });
	
</script>