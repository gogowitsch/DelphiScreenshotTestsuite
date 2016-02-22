<?php

require '../include/smarty.inc.php';
require '../include/projectstatus.inc.php';

$sDirectoryPath = "C:/xampp/htdocs/DelphiScreenshotTestsuite/html/RunningProcces/";
$sFilePath = '';

if (file_exists($sDirectoryPath) && glob($sDirectoryPath . '*.LOCK')) {
    $aDirectory = scandir($sDirectoryPath);
    $sFileName = !empty($aDirectory[2]) ? $aDirectory[2] : '';
    $sFilePath = $sDirectoryPath . $sFileName;

    if (is_file($sFilePath)) {
        $sFileTime = date("[F d Y H:i:s]", filemtime($sFilePath));
    }
}

$smarty->assign("bProccesRunning", is_file($sFilePath));
$smarty->assign("iFileTime", isset($sFileTime) ? $sFileTime : '');
$smarty->assign("sCurrentProcces", is_file($sFilePath) ? $sFileName : '');
$smarty->display('show_running_procces.tpl');
