<?php

$aLangs = array('_de', '_fr', '_es', '_en');

// Designwechsel-"Flaggen" hinzu
$aDesigns = array(
  'BVL',
  'Elkem.no',
  'Eurofins',
  'Human',
  'IBBL',
  'InstitutEignungspruefung',
  'NIST-MML',
  'NIST-OWM',
  'RKI',
  'Rijkswaterstaat',
  'UBA-Wien',
  'IQAS');

$aLangs = array_merge($aLangs, $aDesigns);



require '../include/smarty.inc.php';
require '../include/screenshot.inc.php';

$sTestName = empty($_GET['sTestName']) ? '' : $_GET['sTestName'];
$aTest = getScreenshotStatus($sTestName);
$smarty->assign("aTest", $aTest);
$sProject = !empty($_GET['project']) ? $_GET['project'] : '';
$smarty->assign("project", $sProject);

// Sprachwechsel-Flaggen berechnen
$aFlags = array();
foreach($aLangs as $sLoopLang) {
  $bHasLang = strstr($sProject, $sLoopLang);
  if ($bHasLang) {
    $sProjLang = $sLoopLang;
    break;
  }
}
if ($bHasLang) {
  foreach($aLangs as $sLoopLang) {
    $sAlternativeTestName = str_replace("$sProjLang/", "$sLoopLang/", $sTestName);
    $sAlternativeTestName = str_replace("_$sProjLang", "_$sLoopLang", $sAlternativeTestName);
    $sAlternativeTestName = str_replace(".$sProjLang", ".$sLoopLang", $sAlternativeTestName);
    $sAlternativeTestName = str_replace("/$sProjLang", "/$sLoopLang", $sAlternativeTestName);
    if ($sTestName == $sAlternativeTestName ||
      $sProjLang == $sLoopLang ||
      !file_exists($sAlternativeTestName)) {
      continue;
    }
    #echo "$sAlternativeTestName ($sLoopLang) - $sProjLang<hr>";
    $sAltProj = str_replace($sProjLang, $sLoopLang, $sProject);
    $sLink = str_replace('_', ' ', $sLoopLang);
    $aFlags[] = "<a
      title='Diesen Test gibt es auch für dasselbe Projekt in $sLoopLang.'
      href='details.php?project=$sAltProj&sTestName=" . urlencode($sAlternativeTestName) . "'>$sLink</a>";
  }
}

$smarty->assign("aFlags", $aFlags);

$smarty->display('details.tpl');
