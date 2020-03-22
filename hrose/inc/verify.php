<?php

if($_SESSION["logedin"]) {
    // stuff after login

} else {

    if (isset($_COOKIE['logedin'])) {
        $_SESSION['logedin']=true;
        $_SESSION['login_time']=time();
        $_SESSION['user_id'] = $_COOKIE['logedin'];

        setcookie('logedin', $_SESSION['user_id'], time() + (86400 * 30 * 30), "/");
        setcookie('login_time', $_SESSION['login_time'], time() + (86400 * 30 * 30), "/");

        $user_id = $_SESSION['user_id'];

        // user groups
        $grSql = "SELECT gr_id FROM user2gr WHERE user2gr.user_id=".$user_id;
        $grRes = mysqli_query($con, $grSql);
        if ($grRes) {
            while ($grRow = mysqli_fetch_row($grRes)) {
                $_SESSION['GROUP'][$grRow[0]] = $grRow[0];
            }
        }

        // group rights
        $grrSql = "SELECT right_id, yn as gr_yn FROM right2gr WHERE right2gr.gr_id IN (SELECT gr_id FROM user2gr WHERE user2gr.user_id=".$user_id.")";
        $grrRes = mysqli_query($con, $grrSql);
        if ($grrRes) {
            while ($urRow = mysqli_fetch_row($grrRes)) {
                $_SESSION['RIGHTS'][$urRow[0]] = $urRow[1];
            }
        }

        // user rights
        $urSql = "(SELECT right_id, yn as u_yn FROM right2user WHERE right2user.user_id=".$user_id.")";
        $urRes = mysqli_query($con, $urSql);
        if ($urRes) {
            while ($urRow = mysqli_fetch_row($urRes)) {
                $_SESSION['RIGHTS'][$urRow[0]] = $urRow[1];
            }
        }
    } else {

        // URL Parameter mitnehmen        -----------//
        if(count($_GET)!=0) {
            foreach($_GET as $key => $value) {
                $vars.=$key."=".$value."&";
            }
            $vars = substr($vars,0,-1);
        }

        header("Location: login.php?ref=".basename($_SERVER['PHP_SELF'])."?rn&var=".urlencode($vars));

    }
}

?>