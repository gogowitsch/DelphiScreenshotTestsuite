<?php

require '../include/smarty.inc.php';
require '../include/projectstatus.inc.php';

$sDirectoryPath = "C:/xampp/htdocs/DelphiScreenshotTestsuite/html/RunningProcces/";
$sFilePath = '';

if (file_exists($sDirectoryPath) && glob($sDirectoryPath . '*.LOCK')) {
    $aDirectory = scandir($sDirectoryPath);
    $aFilePath = glob($sDirectoryPath . '*.LOCK');
    $sFilePath = !empty($aFilePath[0]) ? $aFilePath[0] : '';
    $sFileName = basename($sFilePath);

    if (is_file($sFilePath)) {
        $sFileTime = date("[F d Y H:i:s]", filemtime($sFilePath));
    }
}

$smarty->assign("bProccesRunning", is_file($sFilePath));
$smarty->assign("iFileTime", isset($sFileTime) ? $sFileTime : '');
$smarty->assign("sCurrentProcces", is_file($sFilePath) ? $sFileName : '');
$smarty->display('show_running_procces.tpl');
