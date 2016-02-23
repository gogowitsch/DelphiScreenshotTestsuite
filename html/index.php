<?php

require '../include/smarty.inc.php';
require '../include/screenshot.inc.php';
require '../include/projectstatus.inc.php';

$aTests = array();
$aProjects = array();
$iStatusSum = 0;
$aVeraltet = array();

getStatusOfAllProjects();

// Abgeschlossene Jobs löschen und neuen starten
if (!empty($_GET['job_done'])) {
    ProjectDone_RemoveFromQueue($iStatusSum, $aTests, $aNewTests);
    removeRunningTestFolder();
}

if (!empty($_POST['killJobs'])) {
    killRunningProcces();
}

$iVeraltet = countOutdatedFiles($aNewTests);
$iPercentage = ($iStatusSum / count($aTests)) * 100;

$smarty->assign("iPercentage", round($iPercentage));
$smarty->assign("iVeraltet", $iVeraltet);
$smarty->assign("aProjects", $aProjects);
$smarty->assign("bHasHiddenProjects", 0);
$smarty->assign("aTests", $aTests);
$smarty->assign("iStatusSum", $iStatusSum);
$smarty->assign("ini", isset($_GET['ini']));
$smarty->assign("show_all", isset($_GET['show_all']));
$smarty->assign("project", !empty($_GET['project']) ? $_GET['project'] : '');

$iProj = count($aProjects);
if ($iProj == 0)
    die("
    Für diesen Rechner <b style='color:blue'>$_SERVER[SERVER_NAME]</b> sind momentan keine Projekte vorgesehen. <br><br>
    Sie können die Liste der Projekte in <tt>" . dirname(dirname(__FILE__)) . "\include\projectstatus.inc.php</tt> bearbeiten.");

$smarty->display($iProj < 2 ? 'index.tpl' : 'project_list.tpl');
