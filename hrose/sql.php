<?php
$modul="sql";
require("inc/req.php");
$n4a['sql.php?autoexec'] = ss('Autoexec');
require("inc/header.inc.php");

/*** Rights ***/
// For Technicians only
RR(2);

?>
<div class="contentheadline"><?php sss('Features')?></div>
<br>
<div class="contenttext">
<?php

include_once INC_ROOT . 'ssh.inc.php';
$path = "inc/sql/";
$handle=opendir ($path);

if (!file_exists(INC_ROOT . 'serial.txt')) {
    if(LOG) {
        error_log('serial does not exist');
    }
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 20; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    $serial = $randomString;
    if(LOG) {
        error_log('setze ' . INC_ROOT . 'serial.txt');
    }
    file_put_contents(INC_ROOT . 'serial.txt', $serial);
} else {
    if(LOG) {
        error_log('read serial');
    }
    $serial = file_get_contents(INC_ROOT . 'serial.txt');
}

$sql = "SELECT 1 FROM setting WHERE id='PRIVATE_KEY'";
$res = mysqli_query($con, $sql);

if ($res AND !mysqli_num_rows($res)) {
    if(LOG) {
        error_log('private key not found');
    }
    generateKeys();
}

while ($datei = readdir ($handle)) {
    if ($datei != '.'
        AND $datei != '..'
        AND $datei != '.svn'
        AND $datei != 'readme.txt'
        AND $datei != 'serials.txt'
        AND $datei != 'hroses'
        AND (strpos($datei, '.sql') === false)
    ) {
        $j++;
        if(LOG) {
            error_log('***   ' . $datei . '   ***');
        }
        unset($sql);
        $autoExec = false;
        $hrose = false;
        $hroseRestriction = false;
        $date = false;
        $doneHrose = array();
        $alreadyDone = false;
        $executed = false;
        $single = false;
        include($path . $datei);


        $apiError = false;
        if ($hrose) {
            if(LOG) {
                error_log('hroses restriction given');
            }
            if (!in_array($serial, $hrose)) {
                if(LOG) {
                    error_log('this system is not concerned');
                }
                $hroseRestriction = true;
            } else {

                $autoExec = true;
                if(LOG) {
                    error_log('loading deploy status file...');
                }
                $hroseFile = $path . 'hroses/' . $datei . '.hrose';
                if (file_exists($hroseFile)) {
                    $doneHrose = unserialize(file_get_contents($hroseFile));
                    if ($doneHrose AND in_array($serial, $doneHrose)) {
                        if(LOG) {
                            error_log('sql already executed');
                        }
                        $alreadyDone = true;
                    } else {
                        if(LOG) {
                            error_log('not yet executed');
                        }
                    }
                } else {
                    if(LOG) {
                        error_log('file not found. not yet executed');
                    }
                }
            }
        }

        if($autoExec AND LOG) {
            error_log('auto execution');
        }

        if (!$hroseRestriction) {
            if(LOG) {
                error_log('not restricted for this client');
            }

            $status = '';
            if($hrose AND !$alreadyDone) {
                $status = ' (waiting for you)';
            } else if ($hrose) {
                $status = ' (waiting for others)';
            }
            if($status AND LOG) {
                error_log($status);
            }

            ?>

    <table cellspacing=0 cellpadding=0 class="bw">
        <tr style="cursor:pointer" onclick="$('tr .dotted').hide(); $('.file<?php echo $j ?>').show()">
            <td><b><?php echo ucfirst(substr($datei, 0, -4)) ?></b><?php echo $status?></td>
            <td>&nbsp;</td>
            <td class="formright">
            </td>
        </tr>
            <?php
            $datetime1 = new DateTime();
            $datetime2 = new DateTime($date);
            $interval = $datetime1->diff($datetime2);
            $days = (int)$interval->format('%a');
            $skip = false;
            $error = array();
            if ($date AND ($days > SQL_AUTOEXEC_DAYS)) {
                if(LOG) {
                    error_log('too old');
                }
                $error[] = 'too old';
            }

            if ($single) {
                if(LOG) {
                    error_log('single execution');
                }
                if (count($sql) > 1) {
                    if(LOG) {
                        error_log('multiple statements for single execution found');
                    }
                    $error[] = 'multiple statements for single execution found';
                }
                if (!$hrose) {
                    if(LOG) {
                        error_log('no $hrose option set');
                    }
                    $error[] = 'no $hrose option set';

                }
                if ($alreadyDone) {
                    if(LOG) {
                        error_log('skipping because already done');
                    }
                    $skip = true;
                }
            }

            if ($error OR $skip) {
                if($error AND LOG) {
                    error_log('errors: ' . implode('. ', $error));
                }
                echo '<tr><td><span class="red">' . implode('<br>', $error) . '</span></td></tr>';
            } else {
                foreach ($sql as $sqlKey => $s) {
                    $i++;
                    echo '<tr class="dotted file' . $j . '" style="display:none">';
                    echo '<td>' . $s . '&nbsp;&nbsp;
                <br><div id="' . $j . '_' . $i . '_r"><img style="cursor:pointer" src="' . HTTP_HOST . 'css/icon/playback_play_icon&16.png" onclick="$(\'#' . $j . '_' . $i . '_r\').load(\'' . HTTP_HOST . 'a/exec_sql.php?f=' . html($datei) . '&amp;i=' . $sqlKey . '\');">
                </div></td></tr>';
                    if (isset($_REQUEST['autoexec']) AND $autoExec) {
                        if(LOG) {
                            error_log('execute');
                        }
                        $executed = true;
                        if (!mysqli_multi_query($con, $s)) {
                            $errorMsg = mysqli_error($con);
                            if (strpos($errorMsg, 'Duplicate') !== false OR
                                strpos($errorMsg, 'already exists') !== false) {
                                $executed = false;
                                echo '<tr><td><span class="yellow">Duplicate</span></td></tr>';
                            } elseif ($alreadyDone) {
                                echo '<tr><td><span class="yellow">OK again</span></td></tr>';
                            } else {
                                $executed = false;
                                echo '<tr><td><span class="red">' . mysqli_error($con) . '</span></td></tr>';
                            }
                        } elseif ($alreadyDone) {
                            echo '<tr><td><span class="yellow">OK again</span></td></tr>';
                        } else {
                            echo '<tr><td><span class="">OK</span></td></tr>';
                        }
                    }
                }
                if(!$executed) {
                    if(LOG) {
                        error_log('one or more errors');
                    }
                }
            }


            if($hrose AND ($executed OR !$single)) {
                if(LOG) {
                    error_log('calling home');
                }
                // CURL
                $signature = getDigitalSignature($serial . $datei);
                sort($hrose);
                $postdata = array(
                    "serial" => $serial,
                    "hroses" => json_encode($hrose),
                    "filename" => $datei,
                    "signature" => $signature
                );
                $ch = curl_init(MASTER_HROSE . '/a/hrose_sql_deploy.php');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                $decoded = json_decode($result);
                $decodedArr = json_decode($result,true);
                curl_close($ch);

                if (isset($decoded->error)) {
                    if(LOG) {
                        error_log('api error '. $decoded->error);
                    }
                    $apiError = $decoded->error;
                    echo '<span class="red">API error: '.$apiError.'</span>';
                } else if ($result->isError) {
                    echo '<span class="red">';
                    print_r($result);
                    echo '</span>';
                } else {
                    $all = ($decodedArr['data'] == $hrose);

                    if ($all) {
                        if(LOG) {
                            error_log('hroses complete');
                        }
                        unlink($path . $datei);
                        unlink($path . 'hroses/' . $datei . '.hrose');
                    } else {
                        if(LOG) {
                            error_log('hroses incomplete');
                        }
                        file_put_contents($hroseFile, serialize($decodedArr['data']));
                    }
                }
            } else {
                if(LOG) {
                    error_log('not executed or no hroses');
                }
            }
                ?>
    </table><br>
        <?php }
    }
}

closedir($handle);
?>
</div>
<div id="arbitrary">
<textarea name=arbitrary id=arbitrary_sql></textarea><img style="cursor:pointer" src="css/icon/playback_play_icon&16.png" onclick="$('#arbitrary').load('<?=HTTP_HOST?>/a/exec_sql.php?arbitrary',{'sql': $('#arbitrary_sql').val()});">
</div>


<?php //exec_sql(\''.html($datei).'\','.$j.','.$i.')
require("inc/footer.inc.php");
?>
<script type="text/javascript">
function exec_sql(file,fileCnt,i) {
    var url = '<?php echo HTTP_HOST?>a/exec_sql.php?f='+file+'&i='+i;
    $.get(url, function(data) {
        $('#'+fileCnt+'_'+i+'_r').html(data);
    });
}
</script>