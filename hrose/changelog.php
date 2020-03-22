<?php
header('Content-Type: text/html; charset=UTF-8');

$modul="changelog";
$area="no_customer";
$menu_item="changelog";

require("inc/req.php");
require_once("inc/sqlInjection.php");

require("inc/header.inc.php");

if($_POST['add-row']) {
    /*$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $version = filter_input(INPUT_POST, 'version', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $notice = filter_input(INPUT_POST, 'notice', FILTER_SANITIZE_STRING);
    $files = filter_input(INPUT_POST, 'files', FILTER_SANITIZE_STRING);
    $db = filter_input(INPUT_POST, 'db', FILTER_SANITIZE_STRING);*/
	$description 	= $_POST['description'];
    $version 		= $_POST['version'];
	//Change Here By Ravi For the Status Start
    $status 		= (isset($_POST['workStatus'])?$_POST['workStatus']:(isset($_POST['status'])?$_POST['status']:""));
	//Change Here By Ravi For the Status Ends
    $notice 		= $_POST['notice'];
    $files 			= $_POST['files'];
    $db 			= $_POST['db'];
	$priority 		= $_POST['priority'];
	$upload_file	= $_FILES['upload_file']['name'];
	$upload_error	= $_FILES['upload_file']['error'];
	$file_upload	= "true";
	$uploads_dir 	= UPLOAD_ROOT.'changelog';
	validate("workStatus","string");
	if($upload_file && $upload_error <=0)
	{ 
		$tmp_name = $_FILES["upload_file"]["tmp_name"]; 
        move_uploaded_file($tmp_name, "$uploads_dir/$upload_file");
	}
	else
	{
		$file_upload="false";
	}

	if($file_upload=='false')
	{	
		$file_upload="false";
		echo '<br><div class="error">You have an error in the uploaded file. Please try again.</div>';
	}
	else if(!$version || $version=='Version')
	{	
		$file_upload="false";
		echo '<br><div class="error">Please enter version number.</div>';
	}
    else if(!$status)
	{
	    if(!$file_upload)
        	mysqli_query($con, "INSERT INTO changelog (version, description, notice, files, db,priority_status, created, modified) VALUES (".sql_injection($version).", '".sql_injection($description)."', '".sql_injection($notice)."', '".sql_injection($files)."', '".sql_injection($db)."','".sql_injection($priority)."', NOW(), NOW())");
		else
		   	mysqli_query($con, "INSERT INTO changelog (version, description, notice, files, db,priority_status, created, modified, upload_file) VALUES (".sql_injection($version).", '".sql_injection($description)."', '".sql_injection($notice)."', '".sql_injection($files)."', '".sql_injection($db)."','".sql_injection($priority)."', NOW(), NOW(),'".sql_injection($upload_file)."')");
	}
	else
    { 
		mysqli_query($con, "INSERT INTO changelog (version, description, status, notice, files, db,priority_status, created, modified,upload_file) 
					VALUES ('".sql_injection($version)."', '".sql_injection($description)."', ".sql_injection($status).", '".sql_injection($notice)."', '".sql_injection($files)."', '".sql_injection($db)."', '".sql_injection($priority)."',NOW(), NOW(),'".sql_injection($upload_file)."')");
	}
    echo mysqli_error();
}

$sql =	"	SELECT * FROM `changelog` WHERE (`version` in (SELECT max(`version`) as a FROM `changelog`) and approved='A' OR movetotask = 1)
			Union
			SELECT * FROM `changelog` WHERE (`version` in (SELECT max(`version`) FROM `changelog` WHERE `version` not in (SELECT max(`version`) as a FROM `changelog`)) and approved='A'  OR movetotask = 1) ORDER BY id DESC;";
$result = mysqli_query($con, $sql);
$version = null;
?><div id="loading-image" style="position: absolute; top: 140px; left: 450px;"><img alt="Loading..." src="images/ajax-loader.gif" style=""></div>
<table width="100%" border="0" cellspacing="0" cellpadding="0"  class="table_top">
  <tr>
    <td bgcolor="#f7f7f7">
	
	<table width="100%" border="0" cellpadding="6" cellspacing="0">

  <tr>
    <td>
    <table width="100%" border="0" cellpadding="5" cellspacing="1" class="table2">
    <tr><td class="t5 row_head">Changelog</td></tr>
    
    <tr class="table-content"><td>
    
  <table class="chglog" cellpadding="6" cellspacing="1" width="100%">
           <tr><td colspan="21"  bgcolor="#FFFFFF"> <div id="add-row" class="add-row1" onclick="$('.new-row').show();$('#cancel-row').toggle();" style="background-color:#fff; float:right"><input type="button" name="btn1" value="Add Row" class="sub-button" /></div>
            <div id="cancel-row" class="add-row1" onclick="$('.new-row').hide();$(this).toggle();" style="display:none; background-color:#fff;"><input type="button" name="btn2" value="Cancel" class="sub-button" /></div>
            </td></tr>
            <form action="#" method="post" enctype="multipart/form-data">
                <tr class="new-row" style="display:none;">
                <td width="2">&nbsp;</td>
                    <td width="226"><textarea name="description"></textarea></td>
                    <td width="131" class="status"><select name="workStatus"><?php echo statusConvert($_VALID['workStatus'], true) ?></select></td>
                    <td width="184" class=""><textarea name="notice"></textarea></td>
                    <td width="181" class=""><textarea name="files"></textarea></td>
                    <td width="181" class=""><textarea name="db"></textarea></td>
                   	<td  width="218"><input type="file" name="upload_file" value="" /><br />Upload Max Size&nbsp;<?php echo $max_upload = ini_get('upload_max_filesize');?></td>
                    <td width="98" ><select name="priority" id="priority" title="Priority" ><option value="L">Low</option><option value="M">Medium</option><option value="H">High</option><option value="C">Critical</option></select>&nbsp;</td>  <td colspan="2" class="version"><input type="text" name="version" value="Version" onfocus="if($(this).val()=='Version'){$(this).addClass('active');$(this).val('');}" onblur="if($(this).val()==''){$(this).removeClass('active');$(this).val('Version');}"></td>
                    <td width="43" colspan="4" class="ok"><input type="submit" value="OK" name="add-row" class="sub-button"></td>
                </tr>
            </form>
            <tr><td colspan="16"> <table width="100%" class="table2" id="list_tbody">
            <tr><td colspan="7" class="t5" align="center">PSO Update <?php echo $row[1]; ?></td></tr>
            <tr class="table5">
                <td class="t5">ID</td>
                <td class="t5" style="width:23%">Update</td>
                <td class="t5" style="width:7%">Status</td>
                <td class="t5" style="width:17%">Notice</td>
                <td class="t5" style="width:16%">Files</td>
                <td class="t5" style="width:15%">DB</td>
                <td class="t5">Uploaded file</td>    
                <td class="t5">Prority</td>
                <td class="t5">Updated On</td>
                <td class="t5">Up</td>
                <td class="t5">Down</td>
                <td class="t5">Version</td>
                <td class="t5" colspan="2">&nbsp;</td>
            </tr>
            <tr><td colspan="17">&nbsp;</td></tr>
            </table>
             <style>
				.accordionButton span{
					background:url(images/plus.png) no-repeat center right;
					float:right;
					width:15px;
					height:15px;
				}
				.on span{
					background:url(images/minus.png) no-repeat center right;
					float:right;
					width:15px;
					height:15px;
				}
                </style>
            <script type="text/javascript">
                $(document).ready(function() {
                        $('.accordionButton').click(function() {
                            $('.accordionButton').removeClass('on');
                            $('.accordionContent').slideUp('normal');
                            if($(this).next().is(':hidden') == true) {
                                $(this).addClass('on');
                                $(this).next().slideDown('normal');
                             } 
                         });
                        $('.accordionButton').mouseover(function() {
                            $(this).addClass('over');
                        }).mouseout(function() {
                            $(this).removeClass('over');										
                        });
                        $('.accordionContent').hide();
                    });
                </script>
            <?php
			$k=0; $n=0;
			if (!$result) continue;
			 while($row=mysqli_fetch_array($result,MYSQL_ASSOC)) {
			 $k++;
			  	if($k%2==0)
				{
				$class = " class='redbg'";
				}
				else
				{
				$class = " class='whitebg'";
				}
                if(!$version || $version != $row['version'] ) {
                   
				   $nt++; ?>
         <!--   <tr><td colspan="7"></td></tr>-->
            
                    <?php } ?>
                <div class="accordionButton" style="background-color:#E3E3E3; width:99%; padding:5px; float:left;margin-bottom:5px; margin-top:5px; cursor:pointer"><b><?php echo $row['feature_title']; ?></b><span>&nbsp;</span></div>
                <div class="accordionContent" style="float:left;width:100%;"> 
                 <table class="chglog" cellpadding="6" cellspacing="1" width="100%"><tr<?php echo $class;?>id="row-<?php echo $row['id']; ?>" version="<?php echo $row['version']; ?>" onclick="this.className='trr'"  >
                    <td id="id-<?php echo $row[0]; ?>"><?php echo $row['id']; ?></td>
                    <td class="editable description" id="description-<?php echo $row['id']; ?>"><textarea readonly><?php echo $row['description']; ?></textarea></td>
                    <td class="editable" id="status-<?php echo $row['id']; ?>" valign="top"><select class="status" readonly><?php echo statusConvert($row['status'], true) ?></select></td>
                    <td class="editable" id="notice-<?php echo $row['id']; ?>"><textarea readonly><?php echo $row['notice']; ?></textarea></td>
                    <td class="editable" id="files-<?php echo $row['id']; ?>"><textarea readonly><?php echo $row['files']; ?></textarea></td>
                    <td class="editable" id="db-<?php echo $row['id']; ?>"><textarea readonly><?php echo $row['db']; ?></textarea></td>
                    <td class="editable"  id="upload_file-<?php echo $row['id']; ?>">      
                    <?php if($row['upload_file']){?>
                        <div class="doc_icon <?php echo substr($row['created'], -3, 3);?>" style="padding-left:15px;">&nbsp;
                            <a href="downloads.php?type=changelog&amp;file=<?php echo $row['upload_file']; ?>" target="_blank">
                                <?php echo html($row['upload_file']); ?>
                            </a>
                        </div>
                    <?php } ?>
                    </td>
                     <td class="editable" id="priority_status-<?php echo $row['id']; ?>">
                     <?php
                        switch($row['priority_status'])
                        {
                            case "L":
                            {
                                echo "Low";
                                break;
                            }
                            case "M":
                            {
                                echo "Medium";
                                break;
                            }
                            case "H":
                            {
                                echo "High";
                                break;
                            }
                            case "C":
                            {
                                echo "Critical";
                                break;
                            }
                            default:
                            {
                                echo "";
                                break;
                            }
                        }
                     ?>&nbsp;</td>
                     <td class="editable" id="modified-<?php echo $row['id']; ?>"><?php echo date("d-m-Y H:i:s", strtotime(html($row['modified'])));?></td>
                    <td class="editable"><img src="img/icon/up-arrow.png" class="up-arrow"></td>
                    <td class="editable"><img src="img/icon/down-arrow.png" class="down-arrow"></td>
                    <td class='editable' id="version-<?php echo $row['id']; ?>"><input type="text" readonly class="version" value="<?php echo $row['version'] ?>" size=10></td>
                    <td class="editable"><img src="img/icon/delete.gif" class="delete"></td>
                </tr></table>
            	</div>
            <?php $version = $row['version']; }	?>
			</td></tr>
			<tr><td colspan="15" align="right" bgcolor="#FFFFFF" id="show_button"><input type="button" value="Show All" class="sub-button"  onclick="javascript:updateList();"/></td></tr>
        </table>
    </td></tr>
    </table></td></tr></table></td></tr></table>
    
    



<?php
require("inc/footer.inc.php");
?>
<script type="text/javascript">
    var sortcol = 'de';
    var sortdir = 'DESC';
    var del = '';
 function updateList() {
        var url = 'ajax/changelog_list.php?action=show-all&sortcol='+sortcol+'&sortdir='+sortdir+'&del='+del;
        $.get(url, function(data) {
            $('#list_tbody').html(data);
			$('#show_button').hide();
            // also add the filterparam to the xls export
          //  $('#xlsbutton').attr('href',$('#xlsbutton').attr('href') + filterparams);
        });
    }
	jQuery(window).load(function() {			
		$("#loading-image")
		.bind("ajaxSend", function(){
		$(this).show();
		})
		.bind("ajaxComplete", function(){
		$(this).hide();
		});
			jQuery('#loading-image').hide();
		});



$("#loading-image")
		.bind("ajaxSend", function(){
		$(this).show();
		})
		.bind("ajaxComplete", function(){
		$(this).hide();
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
	margin-right:550px;
	top:300px;;
	-khtml-border-radius: 10px;
}
</style>

