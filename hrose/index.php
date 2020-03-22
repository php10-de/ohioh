<?php
//define('__ROOT__', dirname(dirname(__FILE__)));
//require_once(__ROOT__.'/mobilize.php');
//include_once('homepages/17/d31998980/htdocs/hrose/m/mobilize.php');

//require_once('/home/path/to/subdomain/m/mobilize.php');
session_name("hrose");
session_start();

if (isset($_REQUEST['m'])) {
    $_SESSION['m'] = true;
}

if(isset($_SESSION['logedin']) || isset($_COOKIE['logedin'])) {
    header('Location:start.php');
}else {
	$loc = (strpos($_SERVER['HTTP_HOST'],'snacks.hrose') !== false)?'snack_register.php':'login.php';
    header('Location:'.$loc);
}
?>
<?php 
echo "this page is empty";
?>