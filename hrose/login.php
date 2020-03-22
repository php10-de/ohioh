<?php
//echo "hello C K F here is Mohamed SARR";
//error_reporting(1);
$modul="login";
$area="all";
require(__DIR__ . "/inc/req.php");

validate("email","string");
validate("pw","string");
validate("ref","string");
validate("var","string");
validate("pk","string");
validate("id","int");
validate("uuid","string");
validate("stay","boolean");
if(isset($_VALID['id'])) {
    // Allow to change the identity only for users with the right for doing so
    RR(1);
    $trueUserId = $_SESSION['user_id'];
    unset($_SESSION);
    session_destroy();
    session_regenerate_id();
    session_start();
    $_SESSION['true_user_id'] = $trueUserId;
}

if(isset($_VALID['uuid'])) {
    // Check right to login with UUID (mobile phone device ID)
    RR(12);
}

if($_VALID['id'] OR $_VALID['uuid'] OR ($_VALID['email']&&$_VALID['pw'])) {
    
    $pwsql="SELECT user_id, firstname, lastname, lang FROM user WHERE is_active=1 AND ";
    if ($_VALID['id']) {
        $pwsql  .= "user_id=".$_VALIDDB['id'];
    } else if ($_VALID['uuid']) {
        $pwsql  .= "uuid=".$_VALIDDB['uuid'];
    } else {
        $pwsql .= "email =".str_replace("\'", "", $_VALIDDB['email'])." AND password = '".my_sql(sha1($_VALID['pw'].SALT))."'";
    }
    
    // echo $pwsql;
    // exit;
    $pwresult=mysqli_query($con, $pwsql);
    $uRow = mysqli_fetch_array($pwresult);

    if($uRow) {
        $user_id = $uRow['user_id'];
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

        $_SESSION['logedin']=true;
        $_SESSION['login_time']=time();
        $_SESSION['user_id'] = $uRow['user_id'];


        setcookie('login_oid', $oid, time() + (86400 * 30 * 30), "/");
        if(!isset($_COOKIE['login_oid']) OR !$_COOKIE['login_oid']){
            header("Refresh:0");
        }

        setcookie('logedin', $_SESSION['user_id'], time() + (86400 * 30 * 30), "/");
        setcookie('login_time', $_SESSION['login_time'], time() + (86400 * 30 * 30), "/");
        setcookie('lang', $_SESSION['lang'], time() + (86400 * 30 * 30), "/");
/*
        if ($_VALID['stay']) {
            // path for cookies - valid for all paths in domain
            $cookie_path = "/";

            // timeout value for the cookie
            $cookie_timeout = 60 * 60 * 25; // timeout value for the garbage collector
            $garbage_timeout = $cookie_timeout + (60 * 10); //cookie + 10 minutes

            session_name();  // dynamically set - beyond question scope
            session_id(); // dynamically set - beyond question scope

            session_set_cookie_params($cookie_timeout, $cookie_path);

            // set the garbage collector to clean the session files
            ini_set('session.gc_maxlifetime', $garbage_timeout);

            // set new session directory to ensurer unique garbage collection
            $sessdir = ini_get('session.save_path').DIRECTORY_SEPARATOR."visitor";
            if (!is_dir($sessdir)) { mkdir($sessdir, 0777); }
            ini_set('session.save_path', $sessdir);
            session_start();
        }*/

        if($_VALID["ref"]!="") {
            $_VALID["ref"] .= ($_VALID["var"])?'&'.str_replace('&amp;','&',$_VALID["var"]):'';
            header('Location:'.$_VALID["ref"]);
            exit;
        } else {
            header('Location:start.php');
            exit;
        }

    }else {
        $headerError = ss('Wrong password.');
    }
}else if(isset($_REQUEST['submitted'])) {
    $headerError = ss('Please enter username and password.');
}/*else {
    $homeMsg = ss('Driver assistance systems and self-driving car');
}*/

$n4a['user_d.php'] = ss('Register');
$n4a['forgotpw.php'] = ss('Forgot Password?');
require("inc/header.inc.php");
?>

<div class="contentheadline"><?php sss('Login')?></div>
<br>
<div class="contenttext">
<form name="formlogin" action="login.php" method="post" class="formLayout">
<?php if($_VALID["ref"]!="") { ?>
<input type="hidden" name="ref" value="<?php echo $_VALID['ref']; ?>">
<input type="hidden" name="var" value="<?php echo $_VALID['var']; ?>">
<?php } ?>
    <?php /*?><div id="login_box" class="field_box">
        <fieldset>
            <legend><img src="img/icon/profile.png" alt="Login"><span class="box_head">Login</span></legend>
            <table id="login" cellpadding="0" cellspacing="0">
                <tr>
                    <td>E-Mail: </td>
                    <td><input type="text" class="input" name="email" value="<?php echo $_VALID['email']?>" size="28" maxlength="50" /></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input id="login_pw" type="password" class="input" name="pw" value="<?php echo $_VALID['pw']?>" size="13" maxlength="50" />&nbsp;<small><a href="forgotpw.php">Forgot password?</a></small></td>
                </tr>
                <tr>
                    <td></td>
                    <td height=30 vAlign=bottom><input type="submit" value="login"></td>
                </tr>
                <!--
                <tr>
                        <td colspan="2"><ul id="login_list"><li><a href="reg.php">Registrieren</a></li><li><a href="forgot_pw.php">Passwort vergessen?</a></li></ul></td>
                </tr>
                -->
            </table>
        </fieldset>
    </div>

    <div class="clear">&nbsp;</div><?php */?>
    <?php
    if($_VALID['email']=="")
    {
    $email = ss('E-Mail');
    }
    else
    {
    $email = $_VALID['email'];
    }
    if($_VALID['pw']=="")
    {
    $pass = ss('Password');
    }
    else
    {
    $pass = $_VALID['pw'];
    }
    ?>
<?php if($err) {
    echo '<span class="red">'.$err.'</span>';
} ?>
  <?php if($_VALID['id']) {
      echo '<input type="hidden" name="id" value="'.$_VALID['id'].'">';
  }?>
  <!--
    <input type="text" name="email" value="<?php //echo $email?>" id="email" required="required" onfocus="clear_email();">
    <br>
    <input type="password" name="pw" id="login_pw" required="required" value="<?php //echo $pass?>" onfocus="clear_password();">
-->
<?php 
$_COOKIE['email'] = str_replace("\'", "", $_COOKIE['email']);
$_COOKIE['password'] = str_replace("\'", "", $_COOKIE['password']);
?>
<input type="text" name="email" value="<?php if(isset($_COOKIE["email"])) echo $_COOKIE['email'];?>" id="email" required="required">
    <br>
    <input type="password" name="pw" id="login_pw" required="required" value="<?php if(isset($_COOKIE["password"])) echo $_COOKIE['password'];?>">

    <br>
    <input name="submitted" type="submit" value="<?php sss('Log in')?>">

      </form>

<script type="text/javascript">
function clear_email()
{
if($('#email').val()=='<?php sss('E-Mail')?>')
{

$("#email").get(0).value = "";
}

}
function clear_password()
{
if($('#login_pw').val()=='<?php sss('Password')?>')
{
$("#login_pw").get(0).value = "";
}
}
</script>
</div>
<?php



require("inc/footer.inc.php");

?>