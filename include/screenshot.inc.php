<?php

function compareFiles($sFileSoll, $sFileIst, &$retval) {
    $sTime = date('Y-m-d H:i:s', filemtime($sFileIst));
    if (filesize($sFileSoll) != filesize($sFileIst) || file($sFileSoll) != file($sFileIst)) {
        $retval['desc'] = "Es gibt Unterschiede";
        $retval['status'] = 0;
    } else {
        $retval['desc'] = "Bilder stimmen &uuml;berein";
        $retval['status'] = 1;
    }
}

function getScreenshotStatus($sTestName = 'download-seite') {
    global $iExeTime, $sExePath;

    $sStem = substr($sTestName, 0, -8);
    $sExt = substr($sTestName, -3);

    $sFileIst = "$sStem-ist.$sExt";
    $sFileSoll = "$sStem-soll.$sExt";

    if (stristr($sExt, 'bmp')) {
      require_once('../include/convertToPngIfNeeded.inc.php');
      set_time_limit(120);
      $sFileIst = convertToPngIfNeeded("$sStem-ist", $sExt);
      $sFileSoll = convertToPngIfNeeded("$sStem-soll", $sExt);
    }

    if (isset($_REQUEST['done']) && ($_REQUEST['done'] == $sTestName || (isset($_POST['check']) && in_array($sTestName, $_POST['check'])))) {
        copy($sFileIst, $sFileSoll);
    }
    $retval = array();
    $retval['fileIst'] = $sFileIst;
    $retval['fileSoll'] = $sFileSoll;
    $retval['ext'] = $sExt;
    $retval['name'] = $sTestName;
    $retval['title'] = basename($sTestName);

    if (isset($_REQUEST['discard']) && ($_REQUEST['discard'] == $sTestName || (isset($_POST['check']) && in_array($sTestName, $_POST['check'])))) {
        unlink($sFileIst);
        $retval['desc'] = "Test wurde gelöscht";
        $retval['status'] = 1;
        return $retval;
    }

    if (!file_exists($sFileSoll)) {
        $retval['desc'] = "Soll-Datei existiert noch nicht";
        $retval['status'] = 0;
        $retval['sollTime'] = '';
        $sName = urlencode($sTestName);
    } else {
        $retval['sollTime'] = date(DATE_RSS, filemtime($sFileSoll));
        compareFiles($sFileSoll, $sFileIst, $retval);
    }
    $iIstTime = filemtime($sFileIst);
    $retval['istTime'] = date(DATE_RSS, $iIstTime);
    if ($iIstTime < $iExeTime) {
        $retval['desc'] .= "; Ist-Datei kommt nicht von aktueller ".  basename($sExePath);
        $retval['status'] = 0;
    }

    return $retval;
}
