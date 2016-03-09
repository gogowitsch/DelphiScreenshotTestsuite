<?php
//require_once  '../html/index.php';
require '../include/screenshot.inc.php';
require '../include/projectstatus.inc.php';

require_once '../include/queue.inc.php';
getStatusOfAllProjects();

// Verwaiste Test-Comment records aus Datenbank entfernen
function delete_old_comments($aTests) {
    global $conn;
    db_connect('');

    $sSQL = "SELECT `test` FROM `comments`";
    $aTests_with_comments = db_connect($sSQL);

    if (!empty($aTests_with_comments)) {
        $aTitle = array();

        if (!empty($aTests)) {
            foreach ($aTests as $keys => $val) {
                array_push($aTitle, $aTests[$keys]['title']);
            }
        }

        foreach ($aTests_with_comments as $key => $value) {
            if (!in_array($value['test'], $aTitle)) {
                $sSafeTest = $conn->quote($value['test']);
                $sSQL = "DELETE FROM `comments` WHERE `test` = $sSafeTest";
                db_connect($sSQL);
            }
        }
    }
}

delete_old_comments($aTests);
