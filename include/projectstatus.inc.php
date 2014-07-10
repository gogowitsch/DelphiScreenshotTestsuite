<?php

function getProjectStatus($sProject, $p_sExePath, $sCmd = '') {
  global $sExePath, $iExeTime, $aTests, $aProjects, $iStatusSum, $iLocalStatusSum, $aNewTests;
  global $smarty;

  if (isset($_GET['project']) && $_GET['project']!=$sProject) return;

  if (!empty($_GET['run'])) {
    if (!empty($sCmd)) {
      $_GET['message'] = "Kommandozeile '$sCmd' wurde ausgef&uuml;hrt. " . `$sCmd`;
    } else {
      $_GET['message'] = 'Fuer dieses Projekt wurde keine Kommandozeile hinterlegt.';
    }
  }

  $sPicturePath = "$sProject/";
  $sExePath = $p_sExePath;
  $iExeTime = filemtime($sExePath);
  $aNewTests = array();
  $iLocalStatusSum = 0;
  array_walk(glob("Bilder/$sPicturePath*-ist.???"), function($sFile) {
    global $aNewTests, $iLocalStatusSum;
    $aNewTests[] = $aTest = getScreenshotStatus($sFile);
    $iLocalStatusSum += $aTest['status'];
  });

  $aProjects[]  = array(
    'title' => $sProject,
    'status' => $iLocalStatusSum==count($aNewTests) ? 1 : 0,
    'ratio' => $iLocalStatusSum . " / " . count($aNewTests),
    'cmd' => $sCmd
  );
  $aTests = array_merge($aTests, $aNewTests);
  $iStatusSum += $iLocalStatusSum;
}

function getStatusOfAllProjects() {
  global $aTests;
  $aTests = array();
  $sAhkCmd = '"C:\\Program Files\\AutoHotkey\\AutoHotkey.exe" /ErrorStdOut ';
  $sAhkFolderPl = 'C:\\Users\\Screenhot01\\Desktop\\ScreenshotsPROLab\\Test starten -';
  getProjectStatus('PROLab_de', 'c:/daten/prolab_plus_de_AD\\PROLab_de.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_de.ahk\"");
  getProjectStatus('PROLab_en', 'c:/daten/prolab_plus_en_AD\\PROLab_en.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_en.ahk\"");
  getProjectStatus('mqVAL_DE', 'c:/daten/mqVAL_DE\\mqVAL.exe', "$sAhkCmd \"$sAhkFolderPl mqVAL_DE.ahk\"");
  getProjectStatus('PROLab_Smart_DE', 'c:/daten/PROLab_Smart_DE_13528\\PROLabSmart.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_Smart_DE.ahk\"");
  // getProjectStatus('PROLab_Smart_EN', 'c:/daten/PROLab_Smart_EN\\PROLabSmart.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_Smart_EN.ahk\"");
}
// `start calc`;