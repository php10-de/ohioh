<?php if(!isset($_SESSION['m'])) {?>  
      </td>
    </tr>
<?php if (!EXTERN) { ?>
    <tr>
      <td class="footleft">&nbsp;</td>
      <td class="footnav">
      <img src="<?php echo HTTP_SUB?>css/images/dot-lines2.png" class="line">
      <div id="foottext">
      <a href="/index.php" title="<?php sss('Home')?>" class="grau"><?php sss('Home')?></a> 
      <?php if (isset($_SESSION['logedin'])) { ?><a href="logout.php" title="<?php sss('Logout')?>" class="grau"><?php sss('Logout')?></a> <?php }?>
      <a href="contact.php" title="<?php sss('Kontakt')?>" class="grau"><?php sss('Kontakt')?></a>


      <a href="javascript:window.print()" class="grau"><?php sss('Drucken')?></a> <span class="fontscale"><a href="javascript:smaller(1.2);" id="Aklein">A</a> <a href="javascript:bigger(1.2);" id="Agross">A</a></span>
      </div>
      </td>
    </tr>
<?php } ?>
  </tbody></table><br>
<?php

if (isset($_GET['ok'])) {
    $headerMsg = $_GET['ok'];
}

if (!isset($headerMsg)) {$headerMsg = null;}
if (!isset($headerError)) {$headerError = null;}

if ($headerMsg) {
    $color = 'green';
} elseif ($headerError) {
    $color = 'red';
    $headerMsg = $headerError;

}

if ($headerMsg || $headerError) {
    $cnt = strlen($headerMsg);
    $fontSize = '1.5em';
    $imgSize = '20';
    if ($cnt > 120) {
        $l = $cnt/3;
        $fontSize = '0.95em';
        $imgSize = '10';
        $lbPos = strpos($headerMsg, ' ', $l);
        $msg1 = substr($headerMsg, 0, $lbPos);
        $lbPos2 = strpos($headerMsg, ' ', ($l*2));
        $msg2 = substr($headerMsg, $lbPos, $l+1);
        $msg3 = substr($headerMsg, $lbPos2);
        $headerMsg = $msg1 . '<br>' . $msg2 . '<br>' . $msg3;
    } else if ($cnt > 45) {
        $fontSize = '1.22em';
        $imgSize = '15';
        $lbPos = strpos($headerMsg, ' ', ($cnt/2));
        $msg1 = substr($headerMsg, 0, $lbPos);
        $msg2 = substr($headerMsg, $lbPos);
        $headerMsg = $msg1 . '<br>' . $msg2;
    }
}
if (isset($homeMsg)) {
    $headerMsg = $homeMsg;
}
if ($headerMsg) {
    ?>
    <script>
        $(document).ready(function(){

            $('#headermsgtbl').show();
            $('#headermsg').css('font-size', '<?php echo $fontSize?>');
            $("#headermsg").html('<?php echo $headerMsg?>');
            <?php
            if (!$homeMsg) {
                if ($color == 'red') {
                    echo '$("#headermsga").attr(\'src\',\''.HTTP_SUB.'css/images/a-red-'.$imgSize.'.gif\');
$("#headermsgb").attr(\'src\',\''.HTTP_SUB.'css/images/b-red-'.$imgSize.'.gif\');';
                } else {
                    echo '$("#headermsga").attr(\'src\',\''.HTTP_SUB.'css/images/a-'.$imgSize.'.gif\');
$("#headermsgb").attr(\'src\',\''.HTTP_SUB.'css/images/b-'.$imgSize.'.gif\');';
                }
            } else {
                echo '$("#headermsga").attr(\'src\',\''.HTTP_SUB.'css/images/a-red-10.gif\');
$("#headermsgb").attr(\'src\',\''.HTTP_SUB.'css/images/b-10.gif\');';
            }?>
        });
    </script>
<?php } ?>


</body></html>
      
<?php } else { ?>

</body></html>
<?php }
if (file_exists('cache_end.php')) {
    require_once('cache_end.php');
}
?>

