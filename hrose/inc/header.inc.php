<?php
ob_start();

$nav1= array();
$nav2= array();
$nav3= array();
$nav4= array();
$nav5= array();

/*** Find out where we are ***/

if (!isset($_SESSION['NAV']) || isset($_GET['rn'])) {
    $sql = "SELECT nav_id, to_nav_id, level FROM nav WHERE link='" . substr($_SERVER['PHP_SELF'], strlen(SUBDIR) + 1) . "'";
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($res);
    if ($row) $_SESSION['NAV'][$row['level']] = $row['nav_id'];
    $to_nav_id = $row['to_nav_id'];
    while (--$row['level'] >= 1) {
        $sql = "SELECT nav_id, to_nav_id, level FROM nav WHERE nav_id=" . $to_nav_id;
        $res = mysqli_query($con, $sql);
        $subRow = mysqli_fetch_array($res);
        $to_nav_id = $subRow['to_nav_id'];
        $_SESSION['NAV'][$row['level']] = $subRow['nav_id'];
    }

}



/** Change first level navigation ***/
if (isset($_GET['n1'])) {
    $_SESSION['NAV'][1] = (int)$_GET['n1'];
}

if (isset($_SESSION['NAV'])) {

    /*** Read the second level navigation ***/
    $sql = "SELECT nav_id, name, link, params FROM nav WHERE (gr_id IS NULL ";

    /*** Filter ***/
    if (isset($_SESSION['GROUP'])) $sql .= " OR gr_id IN (" . implode(',', $_SESSION['GROUP']) . ")";
    $sql .= ")";
    $sql .= " AND level = 1";
    /*** Order By ***/
    //$sql .= " ORDER BY name";
    $nav1 = getMemCache($sql);
    if (!$nav1) {
        $r = mysqli_query($con, $sql);
        if ($r) {
            while ($row = mysqli_fetch_array($r))
                $nav1[] = $row;
        }
        if ($memcache) setMemCache($sql, $nav1);
    }
}
if ($n1a) {
    foreach ($n1a as $link => $text) {
        $newNav1 = array('link' => $link, 'name' => $text);
        $nav1[] = $newNav1;
    }
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
    <title>
        <?php echo TITLE ?>
    </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php if (!isset($_SESSION['browserchecked'])) {
        $_SESSION['browserchecked'] = true;
        ?>
        <script type="text/javascript" src="<?php echo HTTP_SUB?>js/detectmobilebrowser.js"></script>
    <?php } ?>
    <script src="<?php echo HTTP_SUB?>js/jquery-1.7.2.min.js"></script>
    <?php if (1) { ?>
        <?php if ($modul == "int") { ?>
    <link rel="stylesheet" type="text/css" href="<?php echo HTTP_SUB?>../css/style.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo HTTP_SUB?>../css/jquery-ui.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo HTTP_SUB?>../css/jquery.autocomplete.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo HTTP_SUB?>../js/fancybox/jquery.fancybox-1.3.4.css"/>
    <?php }else { ?>
    <?php if (isset($_SESSION['m'])) { ?>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/mobile_styles.css">
    <?php } ?>
    <link rel="stylesheet" type="text/css" href="<?php echo HTTP_SUB?>css/style.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo HTTP_SUB?>css/jquery-ui.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo HTTP_SUB?>css/jquery.autocomplete.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo HTTP_SUB?>js/fancybox/jquery.fancybox-1.3.4.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo HTTP_SUB?>js/autosuggest/autoSuggest.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo HTTP_SUB?>js/tiptip/tipTip.css"/>
    <link rel="stylesheet" href="<?php echo HTTP_SUB?>css/validationEngine.jquery.css" type="text/css"/>
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo HTTP_SUB?>images/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo HTTP_SUB?>images/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo HTTP_SUB?>images/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo HTTP_SUB?>images/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo HTTP_SUB?>images/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo HTTP_SUB?>images/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo HTTP_SUB?>images/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo HTTP_SUB?>images/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo HTTP_SUB?>images/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo HTTP_SUB?>images/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo HTTP_SUB?>images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo HTTP_SUB?>images/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo HTTP_SUB?>images/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?php echo HTTP_SUB?>manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <?php } ?>

    <?php if (defined("include_js")) {
        echo constant("include_js");
    } ?>
        <script src="<?php echo HTTP_SUB?>js/fontsize.js" type="text/javascript"></script>
        <style type="text/css">#main {
                font-size: 83.01%;
            }</style>
        <script src="<?php echo HTTP_SUB?>js/jquery.validate.min.js"></script>
        <script src="<?php echo HTTP_SUB?>js/autosuggest/jquery.autoSuggest.minified.js"></script>
        <script src="<?php echo HTTP_SUB?>js/tiptip/jquery.tipTip.minified.js"></script>
        <script type="text/javascript" src="<?php echo HTTP_SUB?>js/main.js"></script>
    <?php }?>
</head>
<?php if (isset($_SESSION['m'])) { ?>
    <style>

    </style>
    <nav class="navbar navbar-default cnav">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><?php echo TITLE?></a>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">

                    <?php
                    if ($nav1) {
                        foreach ($nav1 as $row) {

                            if (isset($_GET['n2'])) {
                                $_SESSION['NAV'][2] = (int)$_GET['n2'];
                            }

                            if (isset($_SESSION['NAV'])) {

                                /*** Read the second level navigation ***/
                                $sql = "SELECT nav_id, name, link, params FROM nav WHERE";

                                /*** Filter ***/
                                if (isset($_SESSION['GROUP'])) $sql .= " gr_id IN (" . implode(',', $_SESSION['GROUP']) . ")";
                                $sql .= " AND to_nav_id = " . $row['nav_id'];
                                $sql .= " AND level = 2";
                                /*** Order By ***/
                                $sql .= " ORDER BY name";
                                $nav2 = getMemCache($sql);
                                if (!$nav2) {
                                    $r = mysqli_query($con, $sql);
                                    if ($r) {
                                        while ($rowa = mysqli_fetch_array($r))
                                            $nav2[] = $rowa;
                                    }
                                    if ($memcache) setMemCache($sql, $nav2);
                                }
                            }
                            if ($n2a) {
                                foreach ($n2a as $link => $text) {
                                    $newNav2 = array('link' => $link, 'name' => $text);
                                    $nav2[] = $newNav2;
                                }
                            }
                            if ($nav2) {
                                $ar = array();
                                foreach ($nav2 as $row2) {
                                    $ar[] = $row2;
                                    //echo '<a href="' . $row['link'] . (($row['params']) ? $row['params'] . '&amp;n2=' : '?n2=') . $row['nav_id'] . '" title="' . ss($row['name']) . '" class="' . (($_SESSION['NAV'][2] == $row['nav_id']) ? 'black' : 'grau') . '">' . ss($row['name']) . '</a>&nbsp;&nbsp;&nbsp;';
                                }
                            }
                            $errors = array_filter($ar);

                            if (!empty($errors)) {
                                echo '<li class="dropdown">
                                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">'.ss($row['name']).' <span class="caret"></span></a>
                                    <ul class="dropdown-menu">';
                                foreach ($ar as $r){
                                    echo '<li><a href="' . $r['link'] . (($r['params']) ? $r['params'] . '&amp;n2=' : '?n2=') . $r['nav_id'] . '">' .  ss($r['name']) . '</a></li>';
                                }
                                echo '</ul></li>';

                            }else{
                                echo '<li><a href="' . $row['link'] . (($row['params']) ? $row['params'] . '&amp;n2=' : '?n1=') . $row['nav_id'] . '" title="' . ss($row['name']) . '" >'.ss($row['name']) .' </a></li>';
                            }


                        }
                    }
                    if(isset($_SESSION['user_id'])){
                            echo '<li style="margin-top: 20px"><a href="logout.php"> Abmelden</a></li>';
                        }else{
                            echo '<li style="margin-top: 20px"><a href="login.php"> Anmelden</a></li>';
                        }
                    ?>



                <!--<ul class="nav navbar-nav navbar-right">

                    <li><a href="#"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
                </ul>-->
            </div>
        </div>
    </nav>
<?PHP }?>
<?php if (!isset($_SESSION['m'])) { ?>

<body>
<table style="font-size: 100.1%;<?php echo EXTERN ? ';width:auto;height:auto' : '' ?>" class="main" id="main"
       cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <?php if (!EXTERN) { ?>
        <td class="headleft">
            <a href="<?php echo HTTP_HOST?>"><img src="<?php echo LOGO?>" width="100" alt="<?php echo TITLE?>" title="<?php echo TITLE?>"
                 style="margin-left:-4px;padding-top:23px"></a><br><br></td>
        <td class="headright" align="center"><?php } else echo '<td align="center">' ?>
            <?php if ($_SERVER['PHP_SELF'] == '/bigredbutton.php' OR $_SERVER['PHP_SELF'] == '/red_button.php'):?>
            <!--<img src="<?php echo HTTP_SUB?>red_button/The-Big-Red-Button-Logo.png">-->
            <?php endif;?>
            <table border="0" id="headermsgtbl" style="width:100px; display:none">
                <tr>
                    <td colspan="3"><br><img id="headermsga" src="<?php echo HTTP_SUB?>css/images/a-10.gif"></td>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td align="center" nowrap="nowrap">
                        <div id="headermsg" class="headmsg grau">&nbsp;</div>
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td align="right" colspan="3"><img src="<?php echo HTTP_SUB?>css/images/b-10.gif" id="headermsgb" style="align:right">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <?php if (!EXTERN) { ?>
    <tr>
        <td class="nav1">
            <div id="nav1text">
                <?php
                if ($nav1) {
                    foreach ($nav1 as $row) {
                        echo '<a href="' . $row['link'] . (($row['params']) ? $row['params'] . '&amp;n2=' : '?n1=') . $row['nav_id'] . '" title="' . ss($row['name']) . '" class="' . (($_SESSION['NAV'][2] == $row['nav_id']) ? 'black' : 'grau') . '">' . ss($row['name']) . '</a><br>';
                    }
                }
                ?>

            </div>
            <img src="<?php echo HTTP_SUB?>css/images/Punktelinie_kurz.gif" class="shortline">
        </td>
        <td class="nav2">
            <div id="nav2text">
                <?php

                /** Change second level navigation ***/
                if (isset($_GET['n2'])) {
                    $_SESSION['NAV'][2] = (int)$_GET['n2'];
                }

                if (isset($_SESSION['NAV'])) {

                    /*** Read the second level navigation ***/
                    $sql = "SELECT nav_id, name, link, params FROM nav WHERE";

                    /*** Filter ***/
                    if (isset($_SESSION['GROUP'])) $sql .= " gr_id IN (" . implode(',', $_SESSION['GROUP']) . ")";
                    $sql .= " AND to_nav_id = " . $_SESSION['NAV'][1];
                    $sql .= " AND level = 2";
                    /*** Order By ***/
                    $sql .= " ORDER BY name";
                    $nav2 = getMemCache($sql);
                    if (!$nav2) {
                        $r = mysqli_query($con, $sql);
                        if ($r) {
                            while ($row = mysqli_fetch_array($r))
                                $nav2[] = $row;
                        }
                        if ($memcache) setMemCache($sql, $nav2);
                    }
                }
                if ($n2a) {
                    foreach ($n2a as $link => $text) {
                        $newNav2 = array('link' => $link, 'name' => $text);
                        $nav2[] = $newNav2;
                    }
                }
                if ($nav2) {
                    foreach ($nav2 as $row) {
                        echo '<a href="' . $row['link'] . (($row['params']) ? $row['params'] . '&amp;n2=' : '?n2=') . $row['nav_id'] . '" title="' . ss($row['name']) . '" class="' . (($_SESSION['NAV'][2] == $row['nav_id']) ? 'black' : 'grau') . '">' . ss($row['name']) . '</a>&nbsp;&nbsp;&nbsp;';
                    }
                }
                ?>

            </div>
            <!--<img src="<?php echo HTTP_SUB?>css/images/dot-lines2.png" class="line">-->
        </td>
    </tr>
    <tr>
        <td class="nav3">
            <table class="leftnav" cellpadding="0" cellspacing="0">
                <tbody>
                <tr>
                    <!--<td id="nav3text">
                      <a href="http://www.sdbp.ch/cms/front_content.php?idcat=30" title="Organigramm" class="black">Organigramm</a><br><br><font color="#999999"><a href="http://www.sdbp.ch/cms/upload/Organigramm_Sozialdienst_Nov2012.pdf" target="_blank" title="Organigramm">Organigramm</a> <br>
              Stand per 1. November 2012 <br>
              (PDF 16 kB) </font>-->


                    <td id="nav3textmap">
                        <!--Text...<br><br>-->
                        <div class="cursor">
                            <?php

                            /** Change third level navigation ***/
                            if (isset($_GET['n3'])) {
                                $_SESSION['NAV'][3] = (int)$_GET['n3'];
                            }

                            if (isset($_SESSION['NAV'])) {
                                /*** Read the second level navigation ***/
                                $sql = "SELECT nav_id, name, link, params FROM nav WHERE";

                                /*** Filter ***/
                                if (isset($_SESSION['GROUP'])) $sql .= " gr_id IN (" . implode(',', $_SESSION['GROUP']) . ")";
                                $sql .= " AND to_nav_id = " . $_SESSION['NAV'][2];
                                $sql .= " AND level = 3";
                                /*** Order By ***/
                                $sql .= " ORDER BY name";
                                $nav3 = getMemCache($sql);
                                if (!$nav3) {
                                    $r = mysqli_query($con, $sql);
                                    if ($r) {
                                        while ($row = mysqli_fetch_array($r))
                                            $nav3[] = $row;
                                    }
                                    if ($memcache) setMemCache($sql, $nav3);
                                }
                            }
                            if ($n3a) {
                                foreach ($n3a as $link => $text) {
                                    $newNav3 = array('link' => $link, 'name' => $text);
                                    $nav3[] = $newNav3;
                                }
                            }
                            if ($nav3) {
                                foreach ($nav3 as $row)
                                    echo '<a href="' . $row['link'] . (($row['params']) ? $row['params'] . '&amp;n3=' : '?n3=') . $row['nav_id'] . '" title="' . ss($row['name']) . '" class="' . (($_SESSION['NAV'][3] == $row['nav_id']) ? 'black' : 'grau') . '" onmouseover="this.style.color=\'#e23828\'" onmouseout="this.style.color=\'#767a7d\'">' . ss($row['name']) . '</a><br>';
                            }
                            ?>
                        </div>

                    </td>
                </tr>
                <tr>
                    <td id="nav5text" <?= $nav5top ? ' style="vertical-align: top"' : '' ?>>
                        <?php
                        $nav4 = array();
                        if ($_GET['n3']) {
                            /*** Read the fourth level navigation ***/
                            $sql = "SELECT nav_id, name, link, params FROM nav WHERE";

                            /*** Filter ***/
                            if (isset($_SESSION['GROUP'])) $sql .= " gr_id IN (" . implode(',', $_SESSION['GROUP']) . ")";
                            $sql .= " AND to_nav_id = " . $_GET['n3'];
                            $sql .= " AND level = 4";
                            /*** Order By ***/
                            $sql .= " ORDER BY name";
                            $nav4 = getMemCache($sql);
                            if (!$nav4) {
                                $r = mysqli_query($con, $sql);
                                if ($r) {
                                    while ($row = mysqli_fetch_array($r))
                                        $nav4[] = $row;
                                }
                                if ($memcache) setMemCache($sql, $nav4);
                            }

                        }
                        if ($n4a) {
                            foreach ($n4a as $link => $text) {
                                $newNav4 = array('link' => $link, 'name' => $text);
                                $nav4[] = $newNav4;
                            }
                        }
                        if ($nav4) {
                            foreach ($nav4 as $nav4Entry) {
                                echo '<a href="' . $nav4Entry['link'] . '" class="grau">' . $nav4Entry['name'] . '</a><br>';
                            }
                        }
                        ?>


                    </td>
                </tr>
                </tbody>
            </table>
        </td>
        <?php } ?>
        <td class="content"<?php echo EXTERN ? ' style="width:auto;height:auto"' : '' ?>>
            <div class="contentspace">&nbsp;</div>
            <?php } else { ?>

            <body>


            <?php }
            ?>

