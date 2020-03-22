<?php
if (!isset($limit)) $limit = STANDARD_LIMIT;
echo '<a href="javascript:void(0)" onClick="openNew()"><img src="css/icon/doc_empty_icon&16.png" style="margin-top:1px;margin-bottom:-1px"></a>&nbsp;'.
'<a class="limit" id="limit_50" style="color:black;'. (($limit == 50)?'font-weight: bold':'').'" href="javascript:void(0)" onClick="setLimit(50)">50</a>&nbsp;'.
'<a class="limit" id="limit_200" style="color:black;'. (($limit == 200)?'font-weight: bold':'').'" href="javascript:void(0)" onClick="setLimit(200)">200</a>&nbsp;'.
'<a class="limit" id="limit_9999" style="color:black;'. (($limit == 9999)?'font-weight: bold':'').'" href="javascript:void(0)" onClick="setLimit(9999)">All</a>
<br><br>';
?>
