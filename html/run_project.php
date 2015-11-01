<?php

require '../include/screenshot.inc.php';
require '../include/projectstatus.inc.php';

// Job in in Job-Liste(DB) schreiben
save_job();

// Session hier wieder schließen, da sonst andere PHP-Skripte, die auf dieselbe Session zugreifen wollen während der Test gestartet wird (unter Umständen für mehrere Minuten)
// das SESSION-Array existiert weiter
session_write_close();

getStatusOfAllProjects();

header('Location: index.php' .
        '?project=' . urlencode($_REQUEST['project']) .
        '&message=' . urlencode($_GET['message']));
