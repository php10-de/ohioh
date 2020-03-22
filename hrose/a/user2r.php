<?php
$modul="ajax";

require("../inc/req.php");
validate('user_id','int');
validate('r_id', 'int');
validate('yn', 'boolean');
validate('gr_yn', 'boolean');


/*** Rights ***/
// Generally for people with right do manage groups
RR(2);
// and for the ones with rights to grant rights
RR(4);

// Admin group for Administrators only
if ($_VALID['user_id'] == 1) {
    GRGR(1);
}

if (!isset($_MISSING)) {
    //$sql = "DELETE yn FROM right2gr WHERE "
    if ($_VALID['yn'] == $_VALID['gr_yn']) {
        $sql = "DELETE FROM right2user WHERE user_id = ".$_VALIDDB['user_id']." AND right_id = ".$_VALIDDB['r_id'];
    } else {
        $sql = "REPLACE INTO right2user(user_id,right_id,yn) VALUES(".$_VALIDDB['user_id'].",".$_VALIDDB['r_id'].",".$_VALIDDB['yn'].")";
    }echo $sql;
    mysqli_query($con, $sql) or print(mysqli_error());
    if (LOG == 1) {
        $sql = "INSERT INTO right2user_log(user_id,right_id,yn,dbupdate,update_user_id)
            VALUES(".$_VALIDDB['user_id'].",".$_VALIDDB['r_id'].",".(int) $_VALID['yn'].",now(),'".$_SESSION['user_id']."')";
        mysqli_query($con, $sql) or print(mysqli_error());
    }
    $_SESSION['r']['rl'] = true;
}
?>
