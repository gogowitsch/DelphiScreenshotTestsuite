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

    $sStem = substr($sTestName, 0, -8);
    $sExt = substr($sTestName, -3);
    $sFileIst = "$sStem-ist.$sExt";
    $sFileSoll = "$sStem-soll.$sExt";
    if (isset($_REQUEST['done']) && ($_GET['done'] == $sTestName || (isset($_POST['check']) && in_array($sTestName, $_POST['check'])))) {
        copy($sFileIst, $sFileSoll);
    }
    $retval = array();
    $retval['fileIst'] = $sFileIst;
    $retval['fileSoll'] = $sFileSoll;
    $retval['ext'] = $sExt;
    $retval['name'] = $sTestName;
    $retval['title'] = basename($sTestName);

    if (isset($_REQUEST['discard']) && ($_GET['discard'] == $sTestName || (isset($_POST['check']) && in_array($sTestName, $_POST['check'])))) {
        unlink($sFileIst);
        $retval['desc'] = "Test wurde gelöscht";
        $retval['status'] = 0;
        return $retval;
    }

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
