<?php

function ss($s, $v1 = null, $v2 = null, $v3 = null) {
    global $DE;
    if (!$s) {
        return '';
    }
    if (isset($DE[$s])) {
        $t = $DE[$s];
        if (!$v1) {
            // standard
            $sTranslated = $t;
        } elseif ($v3) {
            $sTranslated = sprintf($t, $v1, $v2, $v3);
        } elseif ($v2) {
            $sTranslated = sprintf($t, $v1, $v2);
        } else {
            $sTranslated = sprintf($t, $v1);
        }
    } else {
        $sTranslated = $s;
    }
    return html($sTranslated);
}


function sss($s, $v1 = null, $v2 = null, $v3 = null) {
    global $DE;
    if (!$s) {
        return '';
    }
    if (isset($DE[$s])) {
        $t = $DE[$s];
        if (!$v1) {
            // standard
            $sTranslated = $t;
        } elseif ($v3) {
            $sTranslated = sprintf($t, $v1, $v2, $v3);
        } elseif ($v2) {
            $sTranslated = sprintf($t, $v1, $v2);
        } else {
            $sTranslated = sprintf($t, $v1);
        }
    } else {
        $sTranslated = $s;
    }
    echo html($sTranslated);
}


?>