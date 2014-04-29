<?php

require '../include/smarty.inc.php';
require '../include/screenshot.inc.php';


$aTest = getScreenshotStatus($_GET['sTestName']);
$smarty->assign("aTest", $aTest);

$smarty->display('details.tpl');
