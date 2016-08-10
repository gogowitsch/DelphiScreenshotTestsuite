<?php

require '../include/smarty.inc.php';
require '../include/projectstatus.inc.php';

$sDirectoryPath = "C:/xampp/htdocs/DelphiScreenshotTestsuite/html/RunningProcess/";
$sFilePath = '';

$smarty->assign("bProcessRunning", false);

if (file_exists($sDirectoryPath) && glob($sDirectoryPath . '*.LOCK')) {
    $aDirectory = scandir($sDirectoryPath);
    $aFilePath = glob($sDirectoryPath . '*.LOCK');
    $sFilePath = !empty($aFilePath[0]) ? $aFilePath[0] : '';
    $sFileName = basename($sFilePath);

    if (is_file($sFilePath)) {
        $sFileTime = date("[F d Y H:i:s]", filemtime($sFilePath));
    }

    $smarty->assign("bProcessRunning", true);
    $smarty->assign("iFileTime", $sFileTime);
    $smarty->assign("sCurrentProcess", $sFileName);
    $smarty->assign("project", substr($sFileName, 0, -5));
}

$smarty->display('show_running_process.tpl');
