<?php

require_once(__DIR__ . "/hrose_ini.php");
if (file_exists(INC_ROOT."cache_start.php")) {
    require_once(INC_ROOT."cache_start.php");
}
ini_set('error_reporting',E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', 1);
require_once(realpath(dirname(__FILE__))."/../db/db_connect.php");
date_default_timezone_set("Europe/Berlin");
session_name("hrose");
session_start();
//session_regenerate_id(true);
if (isset($_REQUEST['extern'])) {
    $_SESSION['extern'] = 1;
}
define('EXTERN',isset($_SESSION['extern']));
require_once(INC_ROOT."functions.php");

//Frei zugängliche Seite ohne eingeloggt zu sein        ---------//

$open_site = array("login","register","team","forgot_pw","platooning","tech","cron","checker");

// Seiten die eingeloggten Zustand erforden, Berechtigungen prüfen in verify.php        ---------//

if(!in_array($modul,$open_site)) {
    require_once(INC_ROOT."verify.php");
}

?>