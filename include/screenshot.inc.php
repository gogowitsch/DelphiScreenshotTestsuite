<?php

require_once '../include/config.inc.php';

function compareFiles($sFileSoll, $sFileIst, &$retval) {
    $sTime = date('Y-m-d H:i:s', filemtime($sFileIst));
    if (filesize($sFileSoll) != filesize($sFileIst) || file($sFileSoll) != file($sFileIst)) {
        $retval['desc'] = LANG=='de' ? "Es gibt Unterschiede" : "There are differences";
        $retval['status'] = 0;
        if (file_exists($sFileSoll . "2")) {
          $bIdentical = compareFiles($sFileSoll . "2", $sFileIst, $retval);
          $retval['desc'] .= ' [Alternative]';
          return $bIdentical;
        }
        return false;
    } else {
        $retval['desc'] = LANG=='de' ? "Bilder stimmen &uuml;berein" : "Screenshots are equal";
        $retval['status'] = 1;
        return true;
    }
}

function compareImages($image1, $image2) {
    $sCompare = '"C:\\Program Files\\ImageMagick-6.8.9-Q16\\compare.exe"';
    $sCmd = "$sCompare -metric RMSE \"$image1\" \"$image2\" NULL:";
    $response = `$sCmd 2>&1`;
    return $response === '0 (0)';
}

function compareAllTestFiles($project) {
    $path = "Bilder/$project";
    $files = glob("$path/*.png");
    foreach ($files as $sFileIst) {
        if(strpos($sFileIst, '-ist.png') !== false) {
            $sStem = substr($sFileIst, 0, -8);
            $sFileSoll = $sStem . '-soll.png';
            createDifferenceImage($sFileIst, $sFileSoll, $sStem);
        }
    }
}

function updateAllTestStatus($test, $projekt) {
    // Ist-Zustand als neuen Sollwert f�r alle gleichen Unterschiede abspeichern
    $path = "Bilder/$projekt";
    $sStem = substr($test, 0, -8);
    $differenceFile = "$sStem-difference.png";
    $files = glob("$path/*-difference.png");
    foreach ($files as $sFileDifference) {
        if (compareImages($differenceFile, $sFileDifference)) {
            $sStem = substr($sFileDifference, 0, -15);
            copy("$sStem-ist.png", "$sStem-soll.png");
        }
    }
}

function createDifferenceImage($sFileIst, $sFileSoll, $sStem) {
    $sCompare = '"C:\\Program Files\\ImageMagick-6.8.9-Q16\\compare.exe"';
    if (file_exists("$sStem-difference.png")) {
        $iTimeD = filemtime("$sStem-difference.png");
        $iTimeI = filemtime($sFileIst);
        $iTimeS = filemtime($sFileSoll);
        if ($iTimeD > $iTimeI && $iTimeD > $iTimeS) return '';

        // Unterschiede sind veraltet
        unlink("$sStem-difference.png");
    }
    $sCmd = "$sCompare -compose src \"$sFileIst\" \"$sFileSoll\" \"$sStem-difference.png\"";
    return `$sCmd 2>&1`;
}

function handleActions(&$retval) {
    if (isset($_REQUEST['done'])) {
      $bCheckedInIndexList = isset($_POST['check']) && in_array(urlencode($retval['name']), $_POST['check']);
      if ($_REQUEST['done'] == $retval['name'] || $bCheckedInIndexList) {
          $alt = empty($_REQUEST['alternative']) ? '' : '2';
          copy($retval['fileIst'], $retval['fileSoll'] . $alt);
      }
    }
    if (isset($_REQUEST['doneAll']) && ($_REQUEST['doneAll'] == $retval['name'] || (isset($_POST['check']) && in_array($retval['name'], $_POST['check'])))) {
        set_time_limit(600);
        compareAllTestFiles($_REQUEST['project']);
        updateAllTestStatus($_REQUEST['doneAll'], $_REQUEST['project']);
    }
    if (isset($_REQUEST['discard']) && ($_REQUEST['discard'] == $retval['name'] || (isset($_POST['check']) && in_array($retval['name'], $_POST['check'])))) {
        unlink($retval['fileIst']);
        $retval['desc'] = "Test wurde gelöscht";
        $retval['status'] = 1;
        return $retval;
    }

    if (isset($_REQUEST['soll_no_longer_needed']) && ($_REQUEST['soll_no_longer_needed'] == $retval['name'] || (isset($_POST['check']) && in_array($retval['name'], $_POST['check'])))) {
        unlink($retval['fileSoll']);
        $retval['desc'] = "Solldatei wurde gelöscht";
        $retval['status'] = 1;
        return $retval;
    }
}

function addRtfLink(&$retval) {
  if ($retval['ext']=='txt' || $retval['ext']=='rtf') {
    $sContent = file_get_contents($retval['fileIst']);
    if (substr($sContent, 0, 5) == '{\rtf') {
      $retval['sRtfLink'] = "<a href='rtf.php?file=".urlencode($retval['fileIst']) . "'>in Word öffnen</a>";
    }
  }
}


function getScreenshotStatus($sTestName = 'download-seite') {
    global $iExeTime, $sExePath;
    global $iConvertedBmpsDuringThisCall;

    $sStem = substr($sTestName, 0, -8);
    $sExt = substr($sTestName, -3);
    if (stristr($sExt, 'bmp')) {
      if (!file_exists("$sStem-ist.$sExt")) {
        header('Location: /details.php?'.substr(http_build_query($_GET), 0, -3).'png');
      }
    }

    $sFileIst = "$sStem-ist.$sExt";
    $sFileSoll = "$sStem-soll.$sExt";

    if (stristr($sExt, 'bmp') || stristr($sExt, 'pdf')) {
      require_once('../include/convertToPngIfNeeded.inc.php');
      set_time_limit(120);
      $sFileIst = convertToPngIfNeeded("$sStem-ist", $sExt);
      $sFileSoll = convertToPngIfNeeded("$sStem-soll", $sExt);
    }

    $retval = array();
    $retval['fileIst'] = $sFileIst;
    $retval['fileSoll'] = $sFileSoll;
    $retval['ext'] = strtolower($sExt);
    $retval['name'] = $sTestName;
    $retval['title'] = basename($sTestName);

    addRtfLink($retval);

    if (handleActions($retval)) return $retval;

    if (!file_exists($sFileSoll)) {
        $retval['desc'] =LANG=='de' ? "Soll-Datei existiert noch nicht" : "Currently no target state file";
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
