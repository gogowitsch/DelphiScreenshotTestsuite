<?php

$aLangs = array('_de', '_fr', '_es', '_en');

require '../include/smarty.inc.php';
require '../include/screenshot.inc.php';

$sTestName = empty($_GET['sTestName']) ? '' : $_GET['sTestName'];
$aTest = getScreenshotStatus($sTestName);
$smarty->assign("aTest", $aTest);
$sProject = isset($_GET['project']) ? $_GET['project'] : '';
$smarty->assign("project", $sProject);
$sProjLang = substr($sProject, -3);

// Sprachwechsel-Flaggen berechnen
$aFlags = array();
if (in_array($sProjLang, $aLangs)) {
  foreach($aLangs as $sLoopLang) {
    $sAlternativeTestName = str_replace("$sProjLang/", "$sLoopLang/", $sTestName);
    #die($sAlternativeTestName);
    if ($sProjLang != $sLoopLang && file_exists($sAlternativeTestName)) {
      $sAltProj = str_replace($sProjLang, $sLoopLang, $sProject);
      $aFlags[] = "<a 
        title='Diesen Test gibt es auch für dasselbe Projekt in der Sprache $sLoopLang.'
        href='details.php?project=$sAltProj&sTestName=" . urlencode($sAlternativeTestName) . "'>$sLoopLang</a>";
    }
  }
}
$smarty->assign("aFlags", $aFlags);

$smarty->display('details.tpl');
