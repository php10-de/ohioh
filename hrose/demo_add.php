<?php
require(__DIR__ . "/inc/req.php");

$sql = "INSERT INTO `fall` (`fall_id`, `transfer_date`) VALUES ('1', now())";
//mysqli_query($con, $sql);


for ($i = 0; $i < 100; $i++) {
    $sql = "INSERT INTO `fall_data` (`fall_id`, `lat`, `lon`, `accuracy`, `timestamp`) 
VALUES ('1', 49.".rand(0,999).", 10.".rand(0,999).", " . rand(0,30) . "0, '" . date("Y-m-d H:i:s", strtotime("-".$i." minute")) . "');";
    mysqli_query($con, $sql);
}

header('Location: https://location-sender.com/wirvsvirus/4.php');