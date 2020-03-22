<?php
$modul="ajax";

require("../inc/req.php");

validate('serial','string');
validate('filename','filename');
validate('signature','string');
validate('hroses','string');

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
    $API = new APIv1($_VALID, $_SERVER['HTTP_ORIGIN']);
    echo json_encode($API->deploySql($_VALID));
} catch (Exception $e) {
    //error_log($e->getMessage());
    echo json_encode(Array('error' => $e->getMessage()));
}
