<?php
$modul="rights_overview";
$area="superadmin";
$menu_item="rights_overview";

require("inc/req.php");

if($_GET['action']=='edit'){
    $res = mysqli_query($con, "UPDATE rights SET group_bit = group_bit ^ ". (int) $_GET['group'] . " WHERE id = '".$_POST['id']."'");
    exit(0);
}

require("inc/header.inc.php");
?><div id="loading-image" style="position: absolute; top: 140px; left: 450px;"><img alt="Loading..." src="images/ajax-loader.gif" style=""></div>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_top_margin">
  <tr>
    <td bgcolor="#f7f7f7" >
	
	<table width="100%" border="0" cellpadding="6" cellspacing="0">
    	<table width="100%" border="0" cellpadding="6" cellspacing="0" class="table2">
    
    <tr>
      <td height="30" class="t5 row_head">User Rights</td>
    </tr>
    
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%" border="0" cellpadding="6" cellspacing="0" class="table2">
    <tr class="t5">
        <th>&nbsp;</th>
        <th>Article</th>
        <th>Project</th>
        <th>CN user</th>
        <th>Admin</th>
        <th>Superadmin</th>
        <th>Area Manager</th>
    </tr>
    <?php
	$nt=0;
    $sql = "SELECT id, description, group_bit+0 as group_bit FROM rights WHERE 1=1";
    $result = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_array($result))
     { //echo '<pre>';
	//print_r($row);
		$nt++;
			if($nt%2==0)
			{
			$class2 = " class='redbg'";
			 echo '<tr '.$class2.' id="'.html($row['id']).'"  >';
			}
			else
			{
		$class2 = " class='whitebg'";
			 echo '<tr   id="'.html($row['id']).'" '.$class2.' >';
			}
    //    echo '<tr  $class2 id="'.html($row['id']).'"  >';
        echo '
        <td>'
            .html($row['description']).' ('.html($row['id']).')
        </td>';
        for ($i=0;$i<6;$i++) {
            $exp = pow(2,$i);
            echo '<td align="center" class="editable"><img id="'.$exp.'" class="tick" src="img/icon/'.((($row['group_bit']&$exp)==$exp)?'eye':'cross').'.png"></td>';
        }
    }
        ?>

    <tr>
        <td colspan="10" class="t5" height="30"><b>Statisch</b></td>
    </tr>
  
    <tr class='redbg'>
        <td style="">
            Manage project members
        </td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
    </tr>
    <tr class='whitebg'>
        <td>
            Add project, add documents to projects
        </td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
    </tr>
  <tr class='redbg'>
        <td>
            Manage projects for all customers
        </td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
    </tr>
    <tr class='whitebg'>
        <td>
            Page Superadmin General
        </td>
        <td align="center" ><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
    </tr>
  <tr class='redbg'>
        <td>
            Edit, add and delete articles. <br>Upload documents for articles.
        </td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
    </tr>
       <tr class='whitebg'>
        <td>
            Edit article price
        </td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
    </tr>
   <tr class='redbg'>
        <td>
            Manage article members
        </td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
    </tr>
         <tr class='whitebg'>
        <td>
            Dictionary edit
        </td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
    </tr>
       <tr class='redbg'>
        <td>
            Edit project payment
        </td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
    </tr>
     <tr class='whitebg'>
        <td>
            Add shipment, edit price, add payment
        </td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td> 
    </tr>
      <tr class='redbg'>
        <td>
            Manage shipments for all customers
        </td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td> 
    </tr>
     <tr class='whitebg'>
        <td>
            User Management
        </td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/eye.png"></td>
        <td align="center"><img src="img/icon/cross.png"></td> 
    </tr>
</table></td></tr></table></td></tr></table>

<?php
require("inc/footer.inc.php");
?>
<script>
$(document).ready(function(){
	$("td.editable .tick").click(function(){
        var group = $(this).attr('id');
        $.post("userrights.php?action=edit&group="+group, { id: $(this).parent().parent().attr("id") });
        if($(this).attr("src")=="img/icon/eye.png") {
            $(this).attr("src", "img/icon/cross.png");
        }
        else{
            $(this).attr("src", "img/icon/eye.png");
        }
    });
});

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