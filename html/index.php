<?php

require '../include/smarty.inc.php';
require '../include/screenshot.inc.php';

$sExePath = 'c:\\LaufwerkD\\Daten\\Projekte\\prolab2002-ads\\System\\PROLab.exe';
$iExeTime = filemtime($sExePath);

$aTests = array();
$iStatusSum = 0;
array_walk(glob('Bilder/*-ist.png'), function($sFile) {
    global $aTests, $iStatusSum;
    $aTests[] = $aTest = getScreenshotStatus(substr($sFile, 0, -8));;
    $iStatusSum += $aTest['status'];
});


$smarty->assign("sTime", date('Y-m-d H:i:s'));
$smarty->assign("Name", "DelphiScreenshotTestsuite", true);
$smarty->assign("aTests", $aTests);
$smarty->assign("iStatusSum", $iStatusSum);

$smarty->display('index.tpl');
