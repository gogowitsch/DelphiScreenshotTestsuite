<?php

require '../include/screenshot.inc.php';
require '../include/projectstatus.inc.php';

getStatusOfAllProjects();

// Job in in Job-Liste(DB) schreiben
if (!empty($_POST["action"])) {
    save_job($aProjects);
}

header('Location: index.php' .
  '?project=' . urlencode($_REQUEST['project']) .
  '&message=' . urlencode($_GET['message']));
