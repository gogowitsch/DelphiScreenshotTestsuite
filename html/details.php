<?php

require '../include/smarty.inc.php';
require '../include/screenshot.inc.php';

$sTestName = empty($_GET['sTestName']) ? '' : $_GET['sTestName'];
$aTest = getScreenshotStatus($sTestName);
$smarty->assign("aTest", $aTest);
$smarty->assign("project", isset($_GET['project']) ? $_GET['project'] : '');

$smarty->display('details.tpl');
