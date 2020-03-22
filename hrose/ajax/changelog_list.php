<?php
header('Content-Type: text/html; charset=UTF-8');

$modul="dict_list";
$area="all";

require("../inc/req.php");
require_once("../inc/dateConv.php");

validate("page","int");
validate("del","int");
validate("xls_export","int");
validate("sortcol","string");
validate("sortdir","string");
validate("de","string");
validate("en","string");
validate("cn","string");

$sql = "SELECT * FROM `changelog` WHERE  approved='A' OR movetotask = 1 ORDER by version DESC";
$sql_query=mysqli_query($con, $sql);
$num_rows = mysqli_num_rows($sql_query);
$nt=0;
$caseview  = "<style>
				.accordionButton1 span{
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
            <script type='text/javascript'>
                $(document).ready(function() {
                        $('.accordionButton1').click(function() {
                            $('.accordionButton1').removeClass('on');
                            $('.accordionContent1').slideUp('normal');
                            if($(this).next().is(':hidden') == true) {
                                $(this).addClass('on');
                                $(this).next().slideDown('normal');
                             } 
                         });
                        $('.accordionButton1').mouseover(function() {
                            $(this).addClass('over');
                        }).mouseout(function() {
                            $(this).removeClass('over');										
                        });
                        $('.accordionContent1').hide();
                    });
                </script>";
while($row=mysqli_fetch_array($sql_query,MYSQL_BOTH))
{ 
	$nt++;	
	if($nt%2==0)
	{
		$class = " class='redbg'";
	}
	else
	{
		$class = " class='whitebg'";
	}
	
 	if(!$version || $version != $row[1])
	{ 
 		$caseview	.="<table class='chglog' cellpadding='6' cellspacing='1' width='100%'><tr><td colspan='13'>
           				<tr><td colspan='13' class='t5' align='center'>PSO Update ". $row[1]."</td></tr>
                		<tr><td colspan='7'></td></tr>
						 <tr class='table5'>
						 <td class='t5'>ID</td>
						<td class='t5' style='width:18%'>Update</td>
               			<td class='t5' style='width:9%'>Status</td>
               			<td class='t5' style='width:17%'>Notice</td>
                		<td class='t5' style='width:16%'>Files</td>
                		<td class='t5' style='width:15%'>DB</td>
						 <td class='t5' style='width:3%'>Uploaded file</td>
					  	 <td class='t5'>Priority</td>
						 <td class='t5'>Up</td>
						 <td class='t5'>Down</td>
						 <td class='t5'>Version</td>
						  <td class='t5' colspan='2'>&nbsp;</td>
						</tr>
						<tr><td colspan='17'>&nbsp;</td></tr></table>";
			
	}
// Tabelleninhalt erstellen
    $caseview .= "<div class='accordionButton1' style='background-color:#E3E3E3; width:99%; padding:5px; float:left;margin-bottom:5px; margin-top:5px; cursor:pointer'><b>".$row['id']."</b><span>&nbsp;</span></div> 
                <div class='accordionContent1' style='float:left;width:100%;'> 
                 <table class='chglog' cellpadding='6' cellspacing='1' width='100%'>"; 
	$caseview .="<tr id='row-".$row[0]."' version='".$row[1]."'".$class." onclick=\"this.className='".$row[0]."'\">";
	$caseview .="<td id='id-".$row[0]."'>".$row[0]."</td>";
	$caseview .="<td class='editable' id='description-".$row[0]."'><textarea readonly class='description'>".$row['description']."</textarea></td>";
	$caseview .="<td class='editable' id='status-".$row[0]."'><select class='status' readonly>".statusConvert($row['status'], true)."</select></td>";
	$caseview .="<td class='editable' id='notice-".$row[0]."'><textarea readonly class='notice'>".$row['notice']."</textarea></td>";
	$caseview .="<td class='editable' id='files-".$row[0]."'><textarea readonly class='files'>". $row['files']."</textarea></td>";
	$caseview .="<td class='editable' id='db-".$row[0]."'><textarea readonly class='db'>". $row['db']."</textarea></td>";
	if($row[10])
	{
		$caseview .="<td class='editable'  id='db-".$row[0]."'>
						<div class='doc_icon ". substr($row[10], -3, 3)."' style='padding-left:15px;'>&nbsp;
							<a href='".UPLOAD_ROOT."changelog/".$row[10]."' target='_blank'>". $row['upload_file']."</a>
						</div>
					</td>";
	} else {
		$caseview .=" <td class='editable' id='db-'".$row[0]."'>&nbsp;</td>";
	}
	$caseview .="<td class='editable' id='priority_status-".$row['id']."'>";
			switch($row['priority_status'])
			{
				case "L":
				{
					$caseview .= "Low";
					break;
				}
				case "M":
				{
					$caseview .= "Medium";
					break;
				}
				case "H":
				{
					$caseview .= "High";
					break;
				}
				case "C":
				{
					$caseview .= "Critical";
					break;
				}
				default:
				{
					$caseview .= "";
					break;
				}
			}
	$caseview .="&nbsp;</td>";
	$caseview .="<td class='editable'><img src='img/icon/up-arrow.png' class='up-arrow'></td>
		         <td class='editable'><img src='img/icon/down-arrow.png' class='down-arrow'></td>
	             <td class='editable'><img src='img/icon/arrow_up.png' class='arrow-up'></td>
				 <td class='editable' id='version-".$row[0]."'><input type='text' readonly class='version' value=".$row[1]." size=10></td>
		         <td class='editable'><img src='img/icon/delete.gif' class='delete'></td>
	             </tr></table></div>";
	$version = $row[1];
}
//$caseview	.="</table></td></tr></table></td></tr></table></td></tr></table></td></tr></table>";
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
        $("td.editable textarea, td.editable input, td.editable select").focus(function(){
	  $(this).removeAttr("readonly");
            $(this).addClass("edit");
        });
        $("td.editable textarea, td.editable input, td.editable select").blur(function(){
            $(this).removeClass("edit");
			
            //alert($(this).parent().attr("id")+" "+$(this).parent().parent().attr("id"));
            $.ajax({
                url: "ajax/changelog_edit.php?action=edit",
                data: "elem="+$(this).attr("class")+"&value="+$(this).val()+"&current="+$(this).parent().parent().attr("class"),
                type: "POST",
                contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				success: function(data) {
                        
                         }
				
            });
        });
		
		   $("td.editable .up-arrow").click(function(){
                    if($(this).parent().parent().prev().hasClass("editable")){
				//	alert('test');
                        $(this).parent().parent().prev().before($(this).parent().parent());
                        $(this).parent().parent().animate({ backgroundColor: '#EEE30D'}, { duration: 100, queue:false, complete: function() {
                        	$(this).animate({ backgroundColor: '#EFEFEF'}, 8000);
                        }});
                      //  $.post("ajax/changelog.php?action=move-up", { current: $(this).parent().parent().attr("id"), old: $(this).parent().parent().prev().attr("id") });
					  		  $.ajax({
								 
               				 url: "ajax/changelog.php?action=move-up",
               				 data: "elem="+$(this).attr("class")+"&value="+$(this).val()+"&current="+$(this).parent().parent().attr("class")+"&version="+$(this).parent().parent().attr("version")+"&postion=bottom",
                			 type: "POST",
                			 contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				  			 success: function(data) {
				  			//alert(data);
                     		 var url = 'ajax/changelog_list.php?action=show-all&sortcol='+sortcol+'&sortdir='+sortdir+'&del='+del;
       				 		 $.get(url, function(data) {
         			  		 $('#list_tbody').html(data);
       					 	 });
                         	}
				
           				 });
                    } else {
					//alert('test123');
                        $(this).parent().parent().prev().prev().prev().prev().before($(this).parent().parent());
                        $(this).parent().parent().animate({ backgroundColor: '#EEE30D'}, { duration: 100, queue:false, complete: function() {
                        	$(this).animate({ backgroundColor: '#EFEFEF'}, 8000);
                        }});
                        //$.post("ajax/changelog.php?action=change-version", { current: $(this).parent().parent().attr("id"), version: $(this).parent().parent().prev().attr("version"), position:"bottom" });
						  $.ajax({
								 
               				 url: "ajax/changelog.php?action=change-version",
               				 data: "elem="+$(this).attr("class")+"&value="+$(this).val()+"&current="+$(this).parent().parent().attr("class")+"&version="+$(this).parent().parent().attr("version")+"&postion=bottom",
                			 type: "POST",
                			 contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				  			 success: function(data) {
				  			//alert(data);
                     		 var url = 'ajax/changelog_list.php?action=show-all&sortcol='+sortcol+'&sortdir='+sortdir+'&del='+del;
       				 		 $.get(url, function(data) {
         			  		 $('#list_tbody').html(data);
       					 	 });
                         	}
				
           				 });
                    }
                });
                $("td.editable .down-arrow").click(function(){
                    if($(this).parent().parent().next().hasClass("editable")){
                        $(this).parent().parent().next().after($(this).parent().parent());
                        $(this).parent().parent().animate({ backgroundColor: '#EEE30D'}, { duration: 100, queue:false, complete: function() {
                        	$(this).animate({ backgroundColor: '#EFEFEF'}, 8000);
                        }});
						
                        //$.post("ajax/changelog.php?action=move-down", { current: $(this).parent().parent().attr("id"), old: $(this).parent().parent().next().attr("id") });
						
						  $.ajax({
                			url: "ajax/changelog.php?action=move-down",
                			data: "elem="+$(this).attr("class")+"&value="+$(this).val()+"&current="+$(this).parent().parent().attr("class"),
              			    type: "POST",
              				contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				  			success: function(data) {
				 			// alert(data);
                     		var url = 'ajax/changelog_list.php?action=show-all&sortcol='+sortcol+'&sortdir='+sortdir+'&del='+del;
       				 		$.get(url, function(data) {
         			  		$('#list_tbody').html(data);
       					 	});
                         	}
				
           				 }); 
                    } else {
					
                        $(this).parent().parent().next().next().next().next().after($(this).parent().parent());
                        $(this).parent().parent().animate({ backgroundColor: '#EEE30D'}, { duration: 100, queue:false, complete: function() {
                        	$(this).animate({ backgroundColor: '#EFEFEF'}, 8000);
                        }});
					   	  $.ajax({
								 
               				 url: "ajax/changelog.php?action=change-version",
               				 data: "elem="+$(this).attr("class")+"&value="+$(this).val()+"&current="+$(this).parent().parent().attr("class")+"&version="+$(this).parent().parent().attr("version")+"&postion=top",
                			 type: "POST",
                			 contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				  			 success: function(data) {
				  			//alert(data);
                     		 var url = 'ajax/changelog_list.php?action=show-all&sortcol='+sortcol+'&sortdir='+sortdir+'&del='+del;
       				 		 $.get(url, function(data) {
         			  		 $('#list_tbody').html(data);
       					 	 });
                         	}
				
           				 }); 
                    }
                });
                $("td.editable .delete").click(function(){
	
                $.ajax({
                url: "ajax/changelog.php?action=delete",
                data: "elem="+$(this).attr("class")+"&value="+$(this).val()+"&current="+$(this).parent().parent().attr("class"),
                type: "POST",
                contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				  success: function(data) {
                     var url = 'ajax/changelog_list.php?action=show-all&sortcol='+sortcol+'&sortdir='+sortdir+'&del='+del;
       				 $.get(url, function(data) {
         			  $('#list_tbody').html(data);
       					 });
                         }
				
            }); 
                });
$("td.editable .arrow-up").click(function(){
		
				$.ajax({
                url: "ajax/changelog.php?action=next-version",
                data: "elem="+$(this).attr("class")+"&value="+$(this).val()+"&current="+$(this).parent().parent().attr("class"),
                type: "POST",
                contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				success: function(data) {
                     var url = 'ajax/changelog_list.php?action=show-all&sortcol='+sortcol+'&sortdir='+sortdir+'&del='+del;
       				 $.get(url, function(data) {
         			  $('#list_tbody').html(data);
       					 });
                         }
				
              });  
                });
		
    });
</script>