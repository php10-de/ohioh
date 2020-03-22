<?php
$modul="forgot_pw";

require("inc/req.php");

validate("email","string");
$success = false;

function ae_gen_password($syllables = 3, $use_prefix = false) {

    // Define function unless it is already exists
    if (!function_exists('ae_arr')) {
        // This function returns random array element
        function ae_arr(&$arr) {
            return $arr[rand(0, sizeof($arr)-1)];
        }
    }

    // 20 prefixes
    $prefix = array('aero', 'anti', 'auto', 'bi', 'bio',
            'cine', 'deca', 'demo', 'dyna', 'eco',
            'ergo', 'geo', 'gyno', 'hypo', 'kilo',
            'mega', 'tera', 'mini', 'nano', 'duo');

    // 10 random suffixes
    $suffix = array('dom', 'ity', 'ment', 'sion', 'ness',
            'ence', 'er', 'ist', 'tion', 'or');

    // 8 vowel sounds
    $vowels = array('a', 'o', 'e', 'i', 'y', 'u', 'ou', 'oo');

    // 20 random consonants
    $consonants = array('w', 'r', 't', 'p', 's', 'd', 'f', 'g', 'h', 'j',
            'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm', 'qu');

    $password = $use_prefix?ae_arr($prefix):'';
    $password_suffix = ae_arr($suffix);

    for($i=0; $i<$syllables; $i++) {
        // selecting random consonant
        $doubles = array('n', 'm', 't', 's');
        $c = ae_arr($consonants);
        if (in_array($c, $doubles)&&($i!=0)) { // maybe double it
            if (rand(0, 2) == 1) // 33% probability
                $c .= $c;
        }
        $password .= $c;
        //

        // selecting random vowel
        $password .= ae_arr($vowels);

        if ($i == $syllables - 1) // if suffix begin with vovel
            if (in_array($password_suffix[0], $vowels)) // add one more consonant
                $password .= ae_arr($consonants);

    }

    // selecting random suffix
    $password .= $password_suffix;

    return $password;
}

if($_VALID['email']) {
    $newpassword = ae_gen_password(2,false);

    $membersql="UPDATE user
				SET password = '" . sha1($newpassword.SALT) . "'
				WHERE email = " . ($_VALIDDB['email']);
    $memberresult = mysqli_query($con, $membersql);
    $resultB = mysqli_affected_rows($con);
    if (!$resultB) {
        $err.=" ".ss('E-Mail not found.')." ";
    } else {
        $success = true;
        $msg= ss("Dear Sir or Madam,")."\r\n\r\n ".ss("your Hrose password has been reset to:")."\r\n\r\n " . $newpassword;
        $subject= ss("Password reset for Hrose");
		//echo $msg;
        send_mail($msg, $subject, $_VALID['email'], "Hrose user");
    }

} else if ($_GET['submitted']) {
    $headerError = ss('Please enter your E-Mail.');
}
require("inc/header.inc.php");
?>
<div class="contentheadline"><?php sss('Forgot Password')?></div>
<br>
<div class="contenttext">
<?php
if ($success) {
    sss("Your password has been reset. Please check your mail."); echo "<br><br>>>&nbsp;<a href='login.php'>".ss('Login')."</a>";
} else {
    ?>
  <form name="forgotpw" method="get" action="forgotpw.php" class="formLayout">
  <label for="email"><?php echo ss('E-Mail')?></label>
  <input type="text" name="email" required="required"><br>
  <input name="submitted" type="submit" value="<?php echo ss('Reset Password')?>">
  </form>

    <?php if($err!="") {
        echo '<br><span class="red">'.$err.'</span>';
    } ?>
</div>
    <?php
}
require("inc/footer.inc.php");
?>