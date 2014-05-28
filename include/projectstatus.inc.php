<?php

function getProjectStatus($sProject, $p_sExePath) {
	global $sExePath, $iExeTime, $aTests, $aProjects, $iStatusSum, $iLocalStatusSum, $aNewTests;

  if (isset($_GET['project']) && $_GET['project']!=$sProject) return;

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
  );
  $aTests = array_merge($aTests, $aNewTests);
  $iStatusSum += $iLocalStatusSum;
}

function getStatusOfAllProjects() {
  global $aTests;
  $aTests = array();
  getProjectStatus('PROLab_de', '\\\\delphicompiler0\\prolab_plus_de_AD\\PROLab_de.exe');
  getProjectStatus('PROLab_en', '\\\\delphicompiler0\\prolab_plus_en_AD\\PROLab_en.exe');
}
