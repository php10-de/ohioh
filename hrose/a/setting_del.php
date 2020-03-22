<?php
$modul="ajax";

require("../inc/req.php");
validate('id','string');

// For Administrators only
GRGR(1);

$sql = "DELETE FROM setting WHERE id = " . $_VALIDDB['id'];
mysqli_query($con, $sql) or print(mysqli_error());

?>
