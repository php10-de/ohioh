<?php

require 'functions_validate.php';
require 'functions_convert.php';

function html($var) {
    return htmlentities($var, ENT_QUOTES, 'UTF-8');
}

if (file_exists('de.inc.php')) {
    require_once 'de.inc.php';
}
require 'translate.php';

function my_sql($var) {
    global $con;
    return get_magic_quotes_gpc()? $var : mysqli_real_escape_string($con, $var);
}

/** PSO **/

/* Session user timestamp */

function make_user_timestamp() {
    $sql="UPDATE user SET timestamp=".time()." WHERE user_id=".$_SESSION["user_id"];
    mysqli_query($con, $sql);
}

function R($r) {
    return (isset($_SESSION['RIGHTS'][$r]))?$_SESSION['RIGHTS'][$r]:false;
}

function dieWithError($msg = 'You are almost allowed to do this.') {
    $headerError = ss($msg);
    if (!EXTERN) {
        require_once('header.inc.php');
        include 'footer.inc.php';
    } else {
        echo $headerError;
    }
    die();

}

function RR($r) {
    if (!R($r)) {
        dieWithError();
    }
}

function GR($gr) {
    if (!is_array($gr)) {
        return isset($_SESSION['GROUP'][$gr]);
    } else {
        return count(array_intersect($gr, $_SESSION['GROUP'])) > 0;
    }
}

function GRGR($gr) {
    if (!GR($gr)) {
        dieWithError();
    }
}

function send_mail($msg,$subject,$recipient_mail,$recipient_name, $int = false, $isHTML=false,$priority = FALSE, $mailFrom = fals) {

    //mail($recipient_mail, $subject, $msg);
    //return;

    if($int == true) {
        $path = "class.phpmailer.php";
    }else {
        $path = dirname(__FILE__)."/class.phpmailer.php";
    }

    include_once($path);

    //$mail->IsSMTP(); / $mail->IsSendmail();

    $mail = new PHPMailer();
    //$msg = html_entity_decode($msg);
    #$msg = htmlentities($msg, ENT_QUOTES, 'utf-8');
    $mail->Body = $msg;
    if($isHTML)
        $mail->isHTML(true);

    //if($mode=="int"){
    //	$mail->IsSMTP();
    //}

    $mail->Priority  = $priority;
    //if($mode=="ext"){
    $mail->IsSendmail();
    //}

    //$mail->IsSMTP(); / $mail->IsSendmail();
    $mail->CharSet == "UTF-8";
    $mail->Host = SMTPHost; // SMTP server
    $mail->SMTPAuth = SMTPAUTH;     //Authentifizierung aktivieren
    $mail->Username = SMTPUsername;  // SMTP Benutzername
    $mail->Password = SMTPPassword; // SMTP Passwort

    $mail->From       = MAILFrom;
    $mail->FromName   = $mailFrom ?:MAILFromName;
    $mail->Subject    = $subject;
    $mail->AddReplyTo('');

    if (defined("MAIL_RECIPIENT")) {
        $recipient_mail = MAIL_RECIPIENT;
    }

    $mail->AddAddress($recipient_mail,$recipient_name);

    if(!$mail->Send()) {
        $err="Mailer Error: " . $mail->ErrorInfo;
        error_log($err);
        return false;
    }
    return true;
}

function str_limit($str, $len = LIST_TEXT_LENGTH, $end = '...')
{
    if(strlen($str) < $len)
    {
        return $str;
    }

    $str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));

    if(strlen($str) <= $len)
    {
        return $str;
    }

    $out = '';
    foreach(explode(' ', trim($str)) as $val)
    {
        $out .= $val . ' ';

        if(strlen($out) >= $len)
        {
            $out = trim($out);
            return (strlen($out) == strlen($str)) ? $out : $out . $end;
        }
    }
}



function encrypt($plaintext, $salt = true) {
    global $config;
    $method = 'aes-256-cbc';
    $salt = $salt?:IHSECRET;
    $password = substr(hash('sha256', $salt, true), 0, 32);

// IV must be exact 16 chars (128 bit)
    $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

    $encrypted = base64_encode(openssl_encrypt($plaintext, $method, $password, OPENSSL_RAW_DATA, $iv));

    return $encrypted;

}

function decrypt($encrypted, $salt = true) {
    global $config;
    $method = 'aes-256-cbc';
    $salt = $salt?:IHSECRET;
    $password = substr(hash('sha256', $salt, true), 0, 32);

// IV must be exact 16 chars (128 bit)
    $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

    $decrypted = openssl_decrypt(base64_decode($encrypted), $method, $password, OPENSSL_RAW_DATA, $iv);
    return $decrypted;
}

require DOC_ROOT . 'vendor/autoload.php';

?>