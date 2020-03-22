<?php
$modul="register";

require("inc/req.php");

validate('id', 'int');
validate('email', 'email');
validate('password', 'string');
validate('firstname', 'string');
validate('lastname', 'string');
validate('is_active', 'int');
validate('i', 'int');
validate('lang', 'string');
validate('uuid', 'string');
$id = $_VALID['id'];

if (isset($_REQUEST['submitted'])) {
    if (!$_VALID['firstname'] || !$_VALID['lastname'] || !$_VALID['email'] || !$_VALID['lang']) {
        $headerError = ss('Some mandatory fields are missing');
    } else {
        if (!$id) {
            $sql = "INSERT INTO user(firstname, lastname, email, lang"
                .(($_VALID['password'])?', password':'')
                .(($_VALID['uuid'])?', uuid':'')
                .")
                    VALUES (".$_VALIDDB['firstname']
                    .",".$_VALIDDB['lastname']
                    .",".$_VALIDDB['email']
                    .",".$_VALIDDB['lang']
                    .(($_VALID['password'])?",'".my_sql(sha1($_VALID['password'].SALT))."'":"")
                    .(($_VALID['uuid'])?",".$_VALIDDB['uuid']:"")
                    .")";
            $res = mysqli_query($con, $sql);
            if (!$res) {
                $headerError = ss('Something went wrong.');
            } else {
            	setcookie("email", $_VALIDDB['email'], time()+360000);
				setcookie("password", $_VALIDDB['password'], time()+360000);
                $_SESSION[$modul]['rl'] = true;
                header('Location: register.php?ok=Registration succesful');
                exit;
            }
        }
    }
}

// manuelle Eingabe überschreibt DB-Werte
if (isset($_REQUEST['submitted'])) {
    foreach ($_VALID as $key => $value) {
        $data[$key] = $value;
    }
}

//$n4a['user.php'] = ss('Back to user list');
require("inc/header.inc.php");
if ($error) {
    echo '<p class="error">' . implode('<br>', $error) . '</p>';
}
?>
<div class="contenttext">

<form id="form<?php echo $modul?>" name="form<?php echo $modul?>" method="post" class="formLayout"<?php echo EXTERN?' style="width:27em;"':''?>>
<?php if($id) {
  echo '<input type="hidden" name="id" value="'.$id.'">';
}?>

<label for="email"><?php echo ss('E-Mail')?></label>
<input type="text" name="email" id="email" value="<?php sss($data['email'])?>" required="required" />

<br>
<label for="password"><?php echo ss('Password')?></label>
<input type="password" name="password" id="password" value="" />

<br>
<label for="firstname"><?php echo ss('Firstname')?></label>
<input type="text" name="firstname" id="firstname" value="<?php sss($data['firstname'])?>" required="required" />

<br>
<label for="lastname"><?php echo ss('Lastname')?></label>
<input type="text" name="lastname" id="lastname" value="<?php sss($data['lastname'])?>" required="required" />

<?php if(!EXTERN) {?>
<br>
<label for="lang"><?php echo ss('Language')?></label>
<select name="lang" required="required" id="lang"><?php echo languageConvert($data['lang'],true)?></select>
<?php } else echo '<input type=hidden name=lang value=DE>'?>


<?php if($_SESSION['uuid']) { ?>
<input type=hidden name=uuid value="<?php echo $_SESSION['uuid']?>">
<?php }?>

<br>
<input type="hidden" name="submitted" value="submitted">
<input type="submit" id="submit" value="<?php sss('Submit')?>">
</form>
<?php if($err!="") {
    echo '<br><span class="red">'.$err.'</span>';
}

require("inc/footer.inc.php");
?>