<?php
require_once '../include/config.inc.php';

require '../include/Smarty/libs/Smarty.class.php';

$smarty = new Smarty;

$smarty->setTemplateDir('../tpl/');
$smarty->setCompileDir('../include/Smarty/compile/');
$smarty->setConfigDir('../include/Smarty/configs/');
$smarty->setCacheDir('../include/Smarty/cache/');

$smarty->assign("Name", "DelphiScreenshotTestsuite", true);
$smarty->assign("sTime", date('Y-m-d H:i:s'));
$smarty->assign("message", isset($_GET['message']) ? $_GET['message'] : '');

$smarty->assign("Sprache", LANG);


