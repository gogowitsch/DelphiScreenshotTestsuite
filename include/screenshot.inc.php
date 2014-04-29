<?php

function compareFiles($sFileSoll, $sFileIst, &$retval) {
    $sTime = date('Y-m-d H:i:s', filemtime($sFileIst));
    if (file($sFileSoll) != file($sFileIst)) {
        $retval['desc'] = "Es gibt Unterschiede.";
        $retval['status'] = 0;
    } else {
        $retval['desc'] = "Bilder stimmen &uuml;berein.";
        $retval['status'] = 1;
    }
}

function getScreenshotStatus($sTestName = 'download-seite') {
    global $iExeTime, $sExePath;

    $sFileIst = "$sTestName-ist.png";
    $sFileSoll = "$sTestName-soll.png";
    if ((isset($_GET['done']) && $_GET['done'] == $sTestName)|| (isset($_POST['check']) && in_array($sTestName, $_POST['check']))) {
        copy($sFileIst, $sFileSoll);
    }
    $retval = array();
    $retval['fileIst'] = $sFileIst;
    $retval['fileSoll'] = $sFileSoll;
    $retval['name'] = $sTestName;
    $retval['title'] = basename($sTestName);
    
    if (filemtime($sFileIst) < $iExeTime) {
        $retval['desc'] = "Ist-Datei kommt nicht von aktueller ".  basename($sExePath);
        $retval['status'] = 0;
    } else {
        if (!file_exists($sFileSoll)) {
            $retval['desc'] = "Soll-Datei existiert noch nicht.";
            $retval['status'] = 0;
            $sName = urlencode($sTestName);
        } else {
            compareFiles($sFileSoll, $sFileIst, $retval);
        }
    }
    return $retval;
}
