<?php

$modul = "ajax";

require("../inc/req.php");
validate('f', 'filename');
validate('i', 'int');

// Technicians only
RR(2);
$i = $_VALID['i'];
$path = DOC_ROOT . "inc/sql/";
$error = false;
if (isset($_GET['arbitrary'])) {
    $statements = explode(';', stripslashes($_REQUEST['sql']));
} else {
    if (!file_exists($path . $_VALID['f']) || !is_file($path . $_VALID['f'])) {
        echo '<span class="red">' . ss('File ' . $path . $_VALID['f'] . ' does not exist.') . '</span>';
    } else {
        include $path . $_VALID['f'];
        $statements[] = $sql[$i];
    }
}

if ($hrose) {
    $serial = file_get_contents($serial);
    if (!in_array($serial,$hrose)) {
        $error[] = ss('not allowed for you');
    } else {
        $hroseFile = $path . 'hroses/' . $_VALID['f'] . '.hrose';
        if (file_exists($hroseFile)) {
            $doneUser = unserialize(file_get_contents($hroseFile));
            if (in_array($serial, $doneHrose)) {
                $alreadyDone = true;
            }
        }
    }
}

$executed = true;
foreach ($statements as $statement) {
    if (!mysqli_multi_query($con, $statement)) {
        $error[] = mysqli_error($con);
        echo '<span class="red">' . implode('<br>', $error) . '</span>';
        $executed = false;
    } elseif ($alreadyDone) {
        echo '<span class="green">OK again</span>';
    } else {
        echo '<span class="green">OK</span>';
    }
}

?>