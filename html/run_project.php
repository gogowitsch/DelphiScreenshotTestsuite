<?php

require '../include/screenshot.inc.php';
require '../include/projectstatus.inc.php';

getStatusOfAllProjects();

header('Location: index.php' .
  '?project=' . urlencode($_REQUEST['project']) .
  '&message=' . urlencode($_GET['message']));