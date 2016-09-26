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

if (!empty($_GET['project'])) {
    $project = $_GET['project'];
    $smarty->assign("project", $project);
    $sLockFile = "C:/xampp/htdocs/DelphiScreenshotTestsuite/html/RunningProcess/$project.LOCK";
    if (file_exists($sLockFile))
        $smarty->assign("started", date("[F d Y H:i:s]", filemtime($sLockFile)));
    $smarty->assign("queued", queued());
} else
    $smarty->assign("project", '');

// Abgeschlossene Jobs löschen und neuen starten
if (!empty($_GET['job_done'])) {
    $sDoneFile = getDoneFile($project);
    rename($sLockFile, $sDoneFile); // damit ist das Änderungsdatum des DoneFiles vom Start des Tests

    $seconds = time() - filemtime($sDoneFile);
    $duration = array();
    while (count($duration) < 3) {
        $duration[] = sprintf("%'.02d", $seconds % 60);
        $seconds /= 60;
    }
    $duration = implode(array_reverse($duration), ':');
    db_connect("UPDATE `projects` SET `duration` = '$duration' WHERE `title` = '$project';");

    ProjectDone_RemoveFromQueue($iStatusSum, $aTests, $aNewTests);
    removeRunningTestFolder();
}

if (!empty($_POST['killJobs'])) {
    $_GET['project'] = $_POST['project'];
    ProjectKilled_RemoveFromQueue();
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
$smarty->display(empty($_GET['project']) ?  'project_list.tpl' : 'index.tpl');
