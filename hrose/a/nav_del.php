<?php
$modul="ajax";

require("../inc/req.php");
validate('id','int');

// Generally for people with the right to change nav
GR(8);

// first the childs
/*
$sql = "SELECT nav_id FROM nav WHERE to_nav_id=" . $_VALID['id'];
if (!GR(1)) {
    // Non-Administrators don't delete Admin-Navigation entries
    $sql .= " AND gr_id != 1";
}
$r = mysqli_query($con, $res);
while ($row = mysqli_fetch_row($r)) {
    $sql = "DELETE FROM nav WHERE nav_id = " . $row[0];
    if (!GR(1)) {
        // Non-Administrators don't delete Admin-Navigation entries
        $sql .= " AND gr_id != 1";
    }
    mysqli_query($con, $sql) or print(mysqli_error());
}
*/
$sql = "DELETE FROM nav WHERE nav_id = " . $_VALID['id'];
if (!GR(1)) {
    // Non-Administrators don't delete Admin-Navigation entries
    $sql .= " AND gr_id != 1";
}
mysqli_query($con, $sql) or print(mysqli_error());

$_SESSION['nav']['rl'] = true;
?>
