<?php
$modul="red_button";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to view red_button
GRGR(3);

require("inc/header.inc.php");

?>
        <div class="contentheadline"></div><div class="contenttext" style="text-align:center">
    <?php require 'red_button/index.php'?>;
</div>
<?php require 'inc/footer.inc.php';?>