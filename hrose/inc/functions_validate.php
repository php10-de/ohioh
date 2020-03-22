<?php

$GLOBALS['normalizeChars'] = array(
    'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
    'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
    'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
    'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f'
);

function fileName($toClean) {

    $toClean     =     str_replace('&', '-and-', $toClean);
    $toClean     =    trim(preg_replace('/[^\w\d_ -]/si', '', $toClean));//remove all illegal chars
    $toClean     =     str_replace(' ', '-', $toClean);
    $toClean     =     str_replace('--', '-', $toClean);

    return strtr($toClean, $GLOBALS['normalizeChars']);
}

/*Variablen Validierung*/

function validate($varname,$type,$set=NULL) {
    global $_VALID, $_VALIDDB, $with_slashes, $_MISSING, $NO, $con;
    list($type,$param) = explode(" ",$type);
    switch ($type) {
        case "boolean":
        case "ckb":
            if (isset($_REQUEST[$varname]) && $_REQUEST[$varname] !== "NULL") {
                if ($_REQUEST[$varname] == false || $_REQUEST[$varname] == NULL) {
                    $_VALID[$varname] = false;
                    $_VALIDDB[$varname] = 0;
                } elseif (($_REQUEST[$varname] == '0') || (in_array($_REQUEST[$varname], $NO))) {
                    $_VALID[$varname] = true;
                    $_VALIDDB[$varname] = 0;
                } elseif ($_REQUEST[$varname] == '_filter_0_') {
                    $_VALID[$varname] = '_filter_0_';
                    $_VALIDDB[$varname] = 0;
                } elseif ($_REQUEST[$varname] == '_filter_1_') {
                    $_VALID[$varname] = '_filter_1_';
                    $_VALIDDB[$varname] = 1;
                } else {
                    $_VALID[$varname] = true;
                    $_VALIDDB[$varname] = 1;
                }
            } elseif ($param == "nullable") {
                $_VALIDDB[$varname] = "NULL";
            } else {
                $_VALIDDB[$varname] = 0;
            }
            break;
        case "int":
            if (isset($_REQUEST[$varname])&&$_REQUEST[$varname]!=="NULL") {
                $_VALID[$varname] = (int) $_REQUEST[$varname];
                $_VALIDDB[$varname] = $_VALID[$varname];
            } elseif ($param=="nullable")
                $_VALIDDB[$varname] = "NULL";
            else {
                $_MISSING[$varname] = $varname;
            }
            break;
        case "numeric":
            if (isset($_REQUEST[$varname])&&$_REQUEST[$varname]!=="NULL") {
                $_VALID[$varname] = (float) str_replace(',', '.', $_REQUEST[$varname]);
                $_VALIDDB[$varname] = $_VALID[$varname];
            } elseif ($param=="nullable")
                $_VALIDDB[$varname] = "NULL";
            else {
                $_MISSING[$varname] = $varname;
            }
            break;
        case "string":
            if (isset($_REQUEST[$varname])) {
                $_VALID[$varname] = "";
                $_VALIDDB[$varname] = "";
            } elseif ($param=="nullable") {
                $_VALIDDB[$varname] = "NULL";
            }
            if (strlen($_REQUEST[$varname])>0) {
                $_VALID[$varname] = valid_string($_REQUEST[$varname]);
                $_VALIDDB[$varname] = ($with_slashes)?"'".valid_db_string($_REQUEST[$varname])."'":valid_db_string($_REQUEST[$varname]);
            } elseif ($param=="nullable") {
                $_VALIDDB[$varname] = "NULL";
            } else {
                $_MISSING[$varname] = $varname;
                if($with_slashes) {
                    $_VALIDDB[$varname] = "''";
                }
            }
            break;
        case "xss_string": // Achtung: nur Ausgabe in HTML mit htmlspecialchars oder htmlentities() mit ENT_QUOTES!
            if (isset($_REQUEST[$varname])) {
                $_VALID[$varname] = "";
                $_VALIDDB[$varname] = "";
            } elseif ($param=="nullable") {
                $_VALIDDB[$varname] = "NULL";
            }
            if (strlen($_REQUEST[$varname])>0) {
                $_VALID[$varname] = valid_string($_REQUEST[$varname]);
                $_VALIDDB[$varname] = ($with_slashes)?"'".xss_db_string($_REQUEST[$varname])."'":xss_db_string($_REQUEST[$varname]);
            } elseif ($param=="nullable") {
                $_VALIDDB[$varname] = "NULL";
            } else {
                $_MISSING[$varname] = $varname;
                if($with_slashes) {
                    $_VALIDDB[$varname] = "''";
                }
            }
            break;
        case "text":
            if (isset($_REQUEST[$varname])) {
                $_VALIDDB[$varname] = "";
            } elseif ($param=="nullable") {
                $_VALIDDB[$varname] = "NULL";
            }
            if ($_REQUEST[$varname]) {
                $_VALID[$varname] = valid_text($_REQUEST[$varname]);
                $_VALIDDB[$varname] = ($with_slashes)?"'".valid_db_text($_REQUEST[$varname])."'":valid_db_text($_REQUEST[$varname]);
            } elseif ($param=="nullable") {
                $_VALIDDB[$varname] = "NULL";
            } else {
                $_MISSING[$varname] = $varname;
                if($with_slashes) {
                    $_VALIDDB[$varname] = "''";
                }
            }
            break;
        case "xss_text":
            if (isset($_REQUEST[$varname])) {
                $_VALIDDB[$varname] = "";
            } elseif ($param=="nullable") {
                $_VALIDDB[$varname] = "NULL";
            }
            if ($_REQUEST[$varname]) {
                $_VALID[$varname] = valid_text($_REQUEST[$varname]);
                $_VALIDDB[$varname] = ($with_slashes)?"'".xss_db_text($_REQUEST[$varname])."'":xss_db_text($_REQUEST[$varname]);
            } elseif ($param=="nullable") {
                $_VALIDDB[$varname] = "NULL";
            } else {
                $_MISSING[$varname] = $varname;
                if($with_slashes) {
                    $_VALIDDB[$varname] = "''";
                }
            }
            break;
        case "uri":
            if(is_valid_url($_REQUEST[$varname])) {
                $_VALID[$varname] = strip_tags(trim($_REQUEST[$varname]));
                $_VALIDDB[$varname] = ($with_slashes)?"'".mysqli_real_escape_string($con, strip_tags(trim($_REQUEST[$varname])))."'":mysqli_real_escape_string($con, strip_tags(trim($_REQUEST[$varname])));
            } elseif ($param=="nullable") {
                $_VALIDDB[$varname] = "NULL";
            } else {
                $_MISSING[$varname] = $varname;
                if($with_slashes) {
                    $_VALIDDB[$varname] = "''";
                }
            }
            break;
        case "date":
            if(is_valid_date($_REQUEST[$varname])) {
                $_VALID[$varname] = date('Y-m-d', strtotime($_REQUEST[$varname]));
                $_VALIDDB[$varname] = ($with_slashes)?"'".date('Y-m-d', strtotime($_REQUEST[$varname]))."'":date('Y-m-d', strtotime($_REQUEST[$varname]));
            } elseif ($param=="nullable") {
                $_VALIDDB[$varname] = "NULL";
            } else {
                $_MISSING[$varname] = $varname;
                if($with_slashes) {
                    $_VALIDDB[$varname] = "''";
                }
            }
            break;
        case "path":
            if(is_valid_path($_REQUEST[$varname])) {
                $_VALID[$varname] = strip_tags(trim($_REQUEST[$varname]));
                $_VALIDDB[$varname] = ($with_slashes)?"'".mysqli_real_escape_string($con, strip_tags(trim($_REQUEST[$varname])))."'":mysqli_real_escape_string($con, strip_tags(trim($_REQUEST[$varname])));
            } elseif ($param=="nullable") {
                $_VALIDDB[$varname] = "NULL";
            } else {
                $_MISSING[$varname] = $varname;
                if($with_slashes) {
                    $_VALIDDB[$varname] = "''";
                }
            }
            break;
        case "email":
            if(is_valid_email($_REQUEST[$varname])) {
                $_VALID[$varname] = strip_tags(trim($_REQUEST[$varname]));
                $_VALIDDB[$varname] = ($with_slashes)?"'".mysqli_real_escape_string($con, strip_tags(trim(strtolower($_REQUEST[$varname]))))."'":mysqli_real_escape_string($con, strip_tags(trim(strtolower($_REQUEST[$varname]))));
            } elseif ($param=="nullable") {
                $_VALIDDB[$varname] = "NULL";
            } else {
                $_VALID[$varname] = "";
                $_MISSING[$varname] = $varname;
                if($with_slashes) {
                    $_VALIDDB[$varname] = "''";
                }
            }
            break;
        case "filename":
            if(is_valid_filename($_REQUEST[$varname])) {
                $_VALID[$varname] = strip_tags(trim($_REQUEST[$varname]));
                $_VALIDDB[$varname] = ($with_slashes)?"'".mysqli_real_escape_string($con, strip_tags(trim($_REQUEST[$varname])))."'":mysqli_real_escape_string($con, strip_tags(trim($_REQUEST[$varname])));
            } elseif ($param=="nullable") {
                $_VALIDDB[$varname] = "NULL";
            } else {
                $_MISSING[$varname] = $varname;
                if($with_slashes) {
                    $_VALIDDB[$varname] = "''";
                }
            }
            break;
        case "array":
            if(is_array($_REQUEST[$varname])) {
                $_VALID[$varname] = array();
                foreach($_REQUEST[$varname] as $key => $value) {
                    $key_n = (int) $key;
                    if (isset($_VALID[$varname][$key_n])) $_VALID[$varname][$key_n] = "";
                    elseif ($param=="nullable") $_VALIDDB[$varname][$key_n] = "NULL";
                    if ($_REQUEST[$varname][$key_n]) {
                        $_VALID[$varname][$key_n] = valid_string($value);
                        $_VALIDDB[$varname][$key_n] = ($with_slashes)?"'".valid_db_string($value)."'":valid_db_string($value);
                    } elseif ($param=="nullable") {
                        $_VALIDDB[$varname][$key_n] = "NULL";
                    } else {
                        $_MISSING[$varname] = $varname;
                        if($with_slashes) {
                            $_VALIDDB[$varname][$key_n] = "''";
                        }
                    }
                }
            }
            break;
        case "enum":
            if(in_array($_REQUEST[$varname],$set)) {
                $_VALID[$varname] = $_REQUEST[$varname];
                $_VALIDDB[$varname] = "'" . $_REQUEST[$varname] . "'";
            } elseif ($param=="nullable") {
                $_VALIDDB[$varname] = 'NULL';
            } else {
                $_MISSING[$varname] = $varname;
                if($with_slashes) {
                    $_VALIDDB[$varname] = "''";
                }
            }
            break;
        case "set":
            if (is_array($_REQUEST[$varname])) {
                $tmpA = array();
                foreach ($_REQUEST[$varname] as $var) {
                    if (in_array($var, $set)) {
                        $tmpA[] = $var;
                    }
                }
                $_VALID[$varname] = implode(',', $tmpA);
                $_VALIDDB[$varname] = "'" . $_VALID[$varname] . "'";
            } elseif ($param=="nullable") {
                $_VALIDDB[$varname] = 'NULL';
            } else {
                $_MISSING[$varname] = $varname;
                if($with_slashes) {
                    $_VALIDDB[$varname] = "''";
                }
            }
            break;
        case "media":
        case "blob":
            if(!empty($_FILES[$varname]['tmp_name'])){
                $_VALIDDB[$varname] = "NULL";
                $path_parts = pathinfo($_FILES[$varname]['name']);
                $allowedExtensions = array('jpg','pdf','png');
                $maxSize = 5000000;
                if ($_FILES[$varname]['extension'] > $maxSize) {
                    throw new Exception('Upload too big (max ' . $maxSize . ')');
                }
                else if (!in_array(strtolower($path_parts['extension']), $allowedExtensions)) {
                    throw new Exception('Upload file extension ' . $path_parts['extension'] . ' not allowed');
                } else {
                    $_VALID[$varname] = $_FILES[$varname]['tmp_name'];
                }
            } elseif ($_REQUEST['hidden_' . $varname]) {
                $_VALIDDB[$varname] = ($with_slashes)?"'".valid_db_string($_REQUEST['hidden_' . $varname])."'":valid_db_string($_REQUEST['hidden_' . $varname]);
            } elseif ($param=="nullable") {
                $_VALIDDB[$varname] = 'NULL';
            } else {
                $_MISSING[$varname] = $varname;
                if($with_slashes) {
                    $_VALIDDB[$varname] = "''";
                }
            }
            break;
    }
}
function valid_db_string($s) { // Achtung: nur Ausgabe in HTML mit htmlspecialchars oder htmlentities() mit ENT_QUOTES!
    return trim(my_sql($s));
}
function xss_db_string($s) { // Achtung: nur Ausgabe in HTML mit htmlspecialchars oder htmlentities() mit ENT_QUOTES!
    return trim(my_sql(strip_tags($s)));
}
function valid_string($s) {
    return $s;
    #return trim(preg_replace("/&amp;#([0-9]{1,4});/","&#$1$2;",htmlentities(strip_tags($s),ENT_QUOTES)));
    //return trim(htmlentities(strip_tags($s),ENT_QUOTES,"UTF-8"));
}
function valid_js_string($s) {
    return trim(htmlentities(strip_tags($s),ENT_QUOTES,"UTF-8"));
}
function valid_email_string($s) {
    return trim(htmlentities(strip_tags($s),ENT_QUOTES,"UTF-8"));
}

function valid_db_text($s) {
    $t = array("'",'<','>','&#39;','&amp;','&quot;');
    return trim(addslashes(str_replace($t,"",strip_tags($s))));
}

function xss_db_text($s) {
    return addslashes(sstrip_tags($s));
}

function valid_text($s) {
    #return preg_replace("/&amp;#([0-9]{1,4});/","&#$1$2;",htmlentities(strip_tags($s),ENT_QUOTES));
    return preg_replace("/&amp;#([0-9]{1,4});/","&#$1$2;",htmlentities(strip_tags($s),ENT_QUOTES,"UTF-8"));
}

function is_valid_url($uri) {
    require_once "validation/Validate.php";
    if(preg_match('/^http[s]{0,1}:\/\/[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[a-zA-Z0-9\.\/\-\_\?\&\%\=]{0,}$/i', $uri)&&strlen($uri)<100&&true===Validate::uri($uri)&&strpos("\n",$uri)===false) {
        return true;
    } else return false;
}

function is_valid_path($uri) {
    if(preg_match('/^[A-Za-z]{1}:\\[a-zA-Z0-9\.\-\_\/\\]{2,}|^[a-zA-Z0-9\.\-\_\\]{2,}|^[a-zA-Z0-9\.\-\_\/]{2,}/i', $uri)&&strlen($uri)<200&&strpos("\n",$uri)===false) {
        return true;
    } else return false;
}

function is_valid_query($query) {
    if(preg_match('/^[a-zA-Z0-9\.\/\-\_\?\&\%\=]{0,}/i', $query)&&strlen($query)<200&&strpos("\n",$query)===false) {
        return true;
    } else return false;
}

function is_valid_email($mail) {
    require_once "validation/Validate.php";
    if(preg_match('/^[a-zA-Z0-9\.\-\_]{2,}@[a-zA-Z0-9\.\-\_]+\.[a-zA-Z]{2,6}$/i', $mail)&&strlen($mail)<500&&true===Validate::email($mail)) {
        return true;
    } else return false;
}

function is_valid_filename($s,$allow_params=false) {
    if(preg_match('/^[a-zA-Z0-9\ \.\-\_'.(($allow_params)?'\=&\?':'').']+$/i', $s)&&strlen($s)<100) {
        return true;
    } else return false;
}

function is_valid_date($date) {
    if(strtotime($date)) {
        return true;
    } else return false;
}

function is_valid_ip($s,$allow_params=false) {
    if(preg_match('/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$/', $s)&&strlen($s)<100) {
        return true;
    } else return false;
}
?>