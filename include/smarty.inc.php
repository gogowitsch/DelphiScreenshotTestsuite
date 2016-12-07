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

$project = isset($_GET['project']) ? $_GET['project'] : '';

// ADD MORE REPOSITORIES HERE!!!
if (preg_match('/^RingDat_Online\./', $project))
    $gitLabURL = "https://git04.quodata.de/it/rdo";
elseif ($project === "BioVAL")
    $gitLabURL = "https://git04.quodata.de/it/bioval-im-web";
elseif ($project === "LPP.AOCS")
    $gitLabURL = "https://git04.quodata.de/it/lpp";
elseif ($project === "OEQUASTA")
    $gitLabURL = "https://git04.quodata.de/it/oequasta";

if (isset($gitLabURL))
    $smarty->assign('newGitLabIssueURL', "$gitLabURL/issues/new");
