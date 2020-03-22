<?php
define("HROSE_VERSION", '1.0.0');
define("SALT","zwFRyp8"); //Salt -> Wichtig: Nur beim neuaufstzen umstellen, sonst bestehende User defekt!!!
define('CRONTOKEN', 'ks4fn7833333SDFsdk348');
if (file_exists('settings.inc.php')) include 'settings.inc.php';

if (isset($argv[1])) {
    $param = explode('/', $argv[1]);
    if (isset($param[0]) AND (CRONTOKEN == $param[0])) {
        $_SERVER['HTTP_HOST'] = $HTTP_HOST;
        if (!defined('__DIR__')) {
            define('__DIR__', $DIR);
        }
        $_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/..';
        define('CRONRUN', true);
    } else {
        define('CRONRUN', false);
    }
} else {
    define('CRONRUN', false);
}

define('MASTER_HROSE', 'https://location-sender.com/ohioh/hrose');

define("HOVERCOLOR","#d6e8ff"); //Tabellenfarbe bei Hover
define("col1","#FFFFFF"); //Tabellenfarbe 1
define("col2","#EEEEEE"); //Tabellenfarbe 2
define("LOGOUT_TIME","10"); // Nach x Stunden ohne Aktivität -> Logout // bsp. 0.1 = 6min
define("STANDARD_LIMIT","50");  //Datensätze pro Seite - darf nie 0 sein
define("SHORTVIEW_NUM","1000000000"); //Datensätze pro Seite - darf nie 0 sein
ini_set('display_errors',0);
ini_set("session.gc_maxlifetime", 1440000);
define("IS_MEMCACHE", false);

$with_slashes = true; // Validates variables for database with apostrophs
if (isset($_SERVER['SERVER_NAME']) AND $_SERVER['SERVER_NAME'] == 'localhost') {
    define('SUBDIR', '');
    define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT'].'/'.SUBDIR);
    define('HTTP_SUB', '/' . SUBDIR);
} else {
    define('SUBDIR', 'ohioh/hrose/');
    define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT'].'/ohioh/hrose/');
    define('HTTP_SUB', '/ohioh/hrose/');
}

if (strpos($_SERVER['HTTP_HOST'], 'platooning-lite') !== false) {
    define("TITLE","Platooning Lite"); //Title
    define("PLATOONING",true);
} else {
    define("TITLE","Hrose"); //Title
    define("PLATOONING",false);
    define('LOGO', HTTP_SUB.'images/hrose-logo.png');
}

define('HTTP_HOST', 'http://'.$_SERVER['HTTP_HOST'].HTTP_SUB);
define('INC_ROOT',DOC_ROOT.'inc/');
define('SQL_ROOT',INC_ROOT.'sql/');
define('RB_ROOT',DOC_ROOT.'red_button/');
define('APP_ROOT',DOC_ROOT.'app/');
define('SSH_ROOT',DOC_ROOT . '../');
define('MODULE_ROOT',DOC_ROOT.'module/');
define('MEDIA_ROOT',DOC_ROOT.'media/');
define('MEDIA_PRIV_ROOT',DOC_ROOT.'var/media/');
define('VAR_ROOT',DOC_ROOT.'var/');
define('SNACKS', strpos($_SERVER['HTTP_HOST'], 'snacks.hrose') !== false);
define('ONLY_DE', SNACKS);
$NO = array('No','Nein','n','N');

//Mail//

define("MAILFrom","info@php10.de");
define("MAILFromName","OHIO");

$CRT = array(
    'admin' => 1,
    'superadmin' => 2,
    'project' => 4,
    'article_add' => 8,
    'areamanager' => 16 ,
    'quality' => 32
);
define("ROW_ODD","#ffffff");
define("ROW_EVEN","#f5eded");
define("FEATURE_DESCR","2000");
define("LIST_TEXT_LENGTH", 30);

define("SQL_AUTOEXEC_DAYS", 100); // days until sql statements are autoexecuted
error_reporting(E_ALL ^ E_NOTICE);
$mouseover =  'style='.chr(34).'cursor:pointer'.chr(34).' onmouseover='.chr(34).'this.style.backgroundColor=\''.HOVERCOLOR.'\';'.chr(34).' onmouseout='.chr(34).'this.style.backgroundColor=\'\';'.chr(34);
?>