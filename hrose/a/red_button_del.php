<?php
$modul="ajax";

require("../inc/req.php");
validate('id','int');

// Generally for people with the right to change nav
GR(8);

$sql = "DELETE FROM red_button WHERE red_button_id = " . $_VALID['id'];

mysqli_query($con, $sql) or print(mysqli_error());

?>
