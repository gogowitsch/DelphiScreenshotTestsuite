<?php

// Dateizweck: wird von PHPUnit via UploadScreenshotTrait aufgerufen

require __DIR__ . '/../include/db_connect.inc.php';

if (empty($_REQUEST['project'])) {
    die('You need to specify a project in the request.');
}

/** @var PDO $conn */
$sWhere = "WHERE `title` = " . $conn->quote($_REQUEST['project']);
$aResult = db_connect("SELECT * FROM `projects` " . $sWhere);

if (!$aResult) {
    echo 'Project not found in DB: ' . $_REQUEST['project'] . "\n";
    $aProjects = array_column(db_connect("SELECT * FROM `projects` "), 'title');
    die('Here are the valid projects: ' . join(', ', $aProjects));
}

if (empty($_FILES['screenshot_file'])) {
    die('Screenshot missing.');
}

$aFile = $_FILES['screenshot_file'];
$sStem = substr($aFile['name'], 0, -4);
$sExt = substr($aFile['name'], -3);
$sDestination = 'Bilder/' . $_REQUEST['project'] . '/' . $sStem . '-ist.' . $sExt;
if (!move_uploaded_file($aFile['tmp_name'], $sDestination)) {
    die('Could not move screenshot.');
}

$aOutput = ['file' => $sStem . '-ist.' . $sExt];
echo json_encode($aOutput + [
        'success' => 1,
        'message' => 'The screenshot was saved.',
    ]);
