<?php
$modul="tech";

require("inc/req.php");
require("inc/header.inc.php");

//RR(2);

?>
<div class="contentheadline"><?php sss('Features')?></div>
<br>
<div class="contenttext">
  <table cellspacing=0 cellpadding=0 class="bw">
  <tr>
    <td><b><?php sss('General')?></b></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><?php sss('Nice design')?></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><?php sss('Fast · Using various forms of caching')?></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><?php sss('Secure · No XSS, SQL-Injection and Parameter Manipulation')?></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><?php sss('Five level deep navigation')?></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><?php sss('Filterable & sortable lists')?></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><?php sss('XLS export')?></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><?php sss('Inline edit')?></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><?php sss('Auto suggest for search fields')?></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  </table><br>
  <table cellspacing=0 cellpadding=0 class="bw">
  <tr>
    <td><b><?php sss('User Management')?></b></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><a href="gr.php"><?php sss('User group managment')?></a></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><?php sss('Rights management on group level')?></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><?php sss('Rights management on user level')?></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><a href="log.php"><?php sss('Changelog')?></a></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  </table><br>
  <table cellspacing=0 cellpadding=0 class="bw">
  <tr>
    <td><b><?php sss('Multilanguage support')?></b></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><?php sss('Support of multiple languages')?></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><?php sss('E-Mail notification when untranslated string is used')?></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  </table><br>
  <table cellspacing=0 cellpadding=0 class="bw">
  <tr>
    <td><b><?php sss('Administration & Development')?></b></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><a href="settings.php"><?php sss('Settings Page')?></a></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><a href="sql.php"><?php sss('SQL management and execution')?></a></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><?php sss('Selenium Tests')?></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  <tr class="dotted">
    <td><a href="red_button/"><?php sss('Big Red Button Source Code Generator')?></a></td><td>&nbsp;</td>
    <td class="formright">
    </td></tr>
  </table>
</div>

<?php
require("inc/footer.inc.php");
?>
