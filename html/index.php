<?php

require '../include/smarty.inc.php';
require '../include/screenshot.inc.php';
require '../include/projectstatus.inc.php';

$aTests = array();
$aProjects = array();
$iStatusSum = 0;

getProjectStatus('PROLab_de', '\\\\delphicompiler0\\prolab_plus_de_AD\\PROLab_de.exe');
getProjectStatus('PROLab_en', '\\\\delphicompiler0\\prolab_plus_en_AD\\PROLab_en.exe');

$smarty->assign("aProjects", $aProjects);
$smarty->assign("bHasHiddenProjects", 0);
$smarty->assign("aTests", $aTests);
$smarty->assign("iStatusSum", $iStatusSum);
$smarty->assign("ini", isset($_GET['ini']));
$smarty->assign("show_all", isset($_GET['show_all']));


$smarty->display(count($aProjects) < 2 ? 'index.tpl' : 'project_list.tpl');
