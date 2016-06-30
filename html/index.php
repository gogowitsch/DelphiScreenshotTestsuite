<?php

require '../include/smarty.inc.php';
require '../include/screenshot.inc.php';
require '../include/projectstatus.inc.php';

$aTests = array();
$aProjects = array();
$iStatusSum = 0;
$aVeraltet = array();

getStatusOfAllProjects();

$iProj = count($aProjects);
if ($iProj == 1) {
    $aProject = reset($aProjects);
    $_GET['project'] = $aProject['title'];
}

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
    rename($sLockFile, $sDoneFile); // damit ist das Änderungsdatum des DoneFiles vom Start des Tests
    ProjectDone_RemoveFromQueue($iStatusSum, $aTests, $aNewTests);
    removeRunningTestFolder();
}

if (!empty($_POST['killJobs'])) {
    killRunningProcess();
}

$iPercentage = count($aTests) > 0 ? $iStatusSum / count($aTests) * 100 : 100;

$smarty->assign("iPercentage", round($iPercentage));
$smarty->assign("aProjects", $aProjects);
$smarty->assign("bHasHiddenProjects", 0);
$smarty->assign("aTests", $aTests);
$smarty->assign("iStatusSum", $iStatusSum);
$smarty->assign("ini", isset($_GET['ini']));
$smarty->assign("show_all", isset($_GET['show_all']));

if ($iProj == 0) {
    $sMsg = "Für diesen Rechner <b style='color:blue'>" . gethostname() . "</b> sind momentan keine Projekte vorgesehen.";
    if (!empty($_GET['project'])) $sMsg = "Das Projekt <b>$_GET[project]</b> wurde nicht gefunden.";
    die("<h1>Fehler</h1>$sMsg<br><br>
    Sie können die Liste der Projekte in <tt>" . dirname(dirname(__FILE__)) . "\include\projectstatus.inc.php</tt> bearbeiten.");
}
$smarty->display($iProj < 2 ? 'index.tpl' : 'project_list.tpl');
