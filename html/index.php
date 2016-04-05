<?php

require '../include/smarty.inc.php';
require '../include/screenshot.inc.php';
require '../include/projectstatus.inc.php';

$aTests = array();
$aProjects = array();
$iStatusSum = 0;
$aVeraltet = array();

getStatusOfAllProjects();

if (!empty($_GET['project'])) {
    $project = $_GET['project'];
    $smarty->assign("project", $project);
    $sLockFile = "C:/xampp/htdocs/DelphiScreenshotTestsuite/html/RunningProcess/$project.LOCK";
    if (file_exists($sLockFile))
        $smarty->assign("started", date("[F d Y H:i:s]", filemtime($sLockFile)));
} else
    $smarty->assign("project", '');

// Abgeschlossene Jobs löschen und neuen starten
if (!empty($_GET['job_done'])) {
    $sDoneFolder = "C:/xampp/htdocs/DelphiScreenshotTestsuite/html/FinishedProcess";
    if (!file_exists($sDoneFolder))
        mkdir($sDoneFolder, 0777, true);
    $sDoneFile = "$sDoneFolder/$project.DONE";
    rename($sLockFile, $sDoneFile);
    ProjectDone_RemoveFromQueue($iStatusSum, $aTests, $aNewTests);
    removeRunningTestFolder();
}

if (!empty($_POST['killJobs'])) {
    killRunningProcess();
}

$iVeraltet = countOutdatedFiles($aNewTests);
$iPercentage = count($aTests) > 0 ? $iStatusSum / count($aTests) * 100 : 100;

$smarty->assign("iPercentage", round($iPercentage));
$smarty->assign("iVeraltet", $iVeraltet);
$smarty->assign("aProjects", $aProjects);
$smarty->assign("bHasHiddenProjects", 0);
$smarty->assign("aTests", $aTests);
$smarty->assign("iStatusSum", $iStatusSum);
$smarty->assign("ini", isset($_GET['ini']));
$smarty->assign("show_all", isset($_GET['show_all']));

$iProj = count($aProjects);
if ($iProj == 0)
    die("
    Für diesen Rechner <b style='color:blue'>" . gethostname() . "</b> sind momentan keine Projekte vorgesehen. <br><br>
    Sie können die Liste der Projekte in <tt>" . dirname(dirname(__FILE__)) . "\include\projectstatus.inc.php</tt> bearbeiten.");

$smarty->display($iProj < 2 ? 'index.tpl' : 'project_list.tpl');
