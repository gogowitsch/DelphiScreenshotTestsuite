<?php

require '../include/smarty.inc.php';
require '../include/screenshot.inc.php';
require '../include/projectstatus.inc.php';
require '../include/queue.inc.php';

session_start();

$aTests = array();
$aProjects = array();
$iStatusSum = 0;

getStatusOfAllProjects();

// Abgeschlossene Jobs löschen und neuen starten
if (!empty($_GET['job_done'])) {
    check_queue();
}

//Job in in Job-Liste(DB) schreiben
$bEmail = false;
if (!empty($_POST["email"])) {
    save_job($aProjects);
    $bEmail = true;
}

$sEmail = "";
if (!empty($_SESSION['email'])) {
    $sEmail = $_SESSION['email'];
}

$smarty->assign("sEmail", $sEmail);
$smarty->assign("bEmail", $bEmail);
$smarty->assign("aProjects", $aProjects);
$smarty->assign("bHasHiddenProjects", 0);
$smarty->assign("aTests", $aTests);
$smarty->assign("iStatusSum", $iStatusSum);
$smarty->assign("ini", isset($_GET['ini']));
$smarty->assign("show_all", isset($_GET['show_all']));
$smarty->assign("project", isset($_GET['project']) ? $_GET['project'] : '');

$smarty->display(count($aProjects) < 2 ? 'index.tpl' : 'project_list.tpl');
