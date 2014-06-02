<?php

require '../include/screenshot.inc.php';
require '../include/projectstatus.inc.php';

getStatusOfAllProjects();

header('Location: index.php' .
  '?project=' . urlencode($_REQUEST['project']) .
  '&message=' . urlencode('Der Ist-Zustand wurde verworfen. Falls es bereits einen Sollzustand gab, wird dieser ab jetzt ignoriert, existiert aber weiter.'));