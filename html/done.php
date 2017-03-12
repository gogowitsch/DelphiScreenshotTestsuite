<?php

// wird von Taste A aufgerufen, wenn ein Testergebnis zum neuen Sollzustand erklärt wird

require '../include/screenshot.inc.php';
require '../include/projectstatus.inc.php';

getStatusOfAllProjects();

$sLanguage = empty($_GET['l']) ? '' : '&l=' . $_GET['l'];
$sMessage = '&message=' . urlencode(LANG == 'en' ?
    'The result was saved as the new target state.' :
    'Das Ergebnis wurde als neuer Sollwert abgelegt.');

header('Location: index.php' .
  '?project=' . urlencode($_REQUEST['project']) .
  $sLanguage .
  $sMessage);
