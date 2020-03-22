<?php
$modul="ajax";

require("../inc/req.php");
validate('dict_id','int');
validate('value', 'string');
validate('elem', 'enum', array('en','de','gr'));

/*** Rights ***/
// Generally for people in the 'Translation' group
GRGR(5);
// Admin group for Administrators only
if ($_VALID['grid'] == 1) {
    GRGR(1);
}

if (!isset($_MISSING)) {
    $sql = "UPDATE dict SET ".$_VALID['elem']." = ".$_VALIDDB['value'] ." WHERE dict_id = ".$_VALIDDB['dict_id'];
    mysqli_query($con, $sql) or print(mysqli_error());
} else {
    error_log('Missing parameter for dict edit');
    error_log(print_r($_MISSING,true));
}
?>
