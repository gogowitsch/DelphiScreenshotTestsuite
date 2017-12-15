<?php
require __DIR__ . '/../include/screenshot.inc.php';
require __DIR__ . '/../include/projectstatus.inc.php';

getStatusOfAllProjects();

// Verwaiste Test-Comment records aus Datenbank entfernen
function delete_old_comments($aTests) {
    global $conn;

    $sSQL = "SELECT `test` FROM `comments`";
    $aTests_with_comments = db_connect($sSQL);

    if (!empty($aTests_with_comments)) {
        $aTitle = [];

        if (!empty($aTests)) {
            foreach ($aTests as $keys => $val) {
                array_push($aTitle, $val['title']);
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
