<?php

/**
 * @param string $p_sExePath wird f�r Ermittlung des Verfallsdatums verwendet, wird nicht zum Start verwendet
 */
function getProjectStatus($sProject, $p_sExePath, $sCmd = '') {
    global $sExePath, $iExeTime, $aTests, $aProjects, $iStatusSum, $iLocalStatusSum, $aNewTests;
    global $smarty;

    if (isset($_GET['project']) && $_GET['project'] != $sProject)
        return;

    if (!empty($_GET['run'])) {
        if (!empty($sCmd)) {
            $_GET['message'] = "Kommandozeile '$sCmd' wurde ausgef&uuml;hrt. <pre style='color:red'>" . `$sCmd 2>&1` . "</pre>";
        }
        else {
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
        if ($aTest['ext'] == 'pdf' || $aTest['ext'] == 'bmp') {
            global $bNeedsFurtherConversions;
            if (empty($bNeedsFurtherConversions))
                $bNeedsFurtherConversions = 1;
            else
                $bNeedsFurtherConversions++;
        }
    });

    $aProjects[] = array(
        'title' => $sProject,
        'status' => $iLocalStatusSum == count($aNewTests) ? 1 : 0,
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
    $sCasperJS = 'cmd /c "cd C:\\xampp\\htdocs\\lvu && ' .
            #'c:\\progra~2\\git\\bin\\git checkout html/js/version.js && ' .
            'c:\\progra~2\\git\\bin\\git pull && ' .
            'cd tests\\PhantomJS && ' . $sAhkCmd;
    $sAhkFolderPl = 'C:\\Users\\Screenhot01\\Desktop\\ScreenshotsPROLab\\Test starten -';
	if ($_SERVER['SERVER_NAME'] == 'screenshot01-PC') {
    getProjectStatus('PROLab_de', 'c:/daten/prolab_plus_de_AD\\PROLab_de.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_de.ahk\"");
    getProjectStatus('PROLab_en', 'c:/daten/prolab_plus_en_AD\\PROLab_en.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_en.ahk\"");
    getProjectStatus('PROLab_fr', 'c:/daten/prolab_plus_fr_AD\\PROLab_fr.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_fr.ahk\"");
    getProjectStatus('mqVAL_DE', 'c:/daten/mqVAL_DE\\mqVAL.exe', "$sAhkCmd \"$sAhkFolderPl mqVAL_DE.ahk\"");
    getProjectStatus('PROLab_Smart_DE', 'c:/daten/PROLab_Smart_DE_13528\\PROLabSmart.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_Smart_DE.ahk\"");
    getProjectStatus('RingDat_en', 'c:/daten/RingDat_EN\\RingDat4_en.exe', "$sAhkCmd \"$sAhkFolderPl RingDat_EN.ahk\"");
    getProjectStatus('RingDat_de', 'c:/daten/RingDat_DE\\RingDat4_de.exe', "$sAhkCmd \"$sAhkFolderPl RingDat_DE.ahk\"");
    getProjectStatus('PROLab_POD_EN', 'c:/daten/PROLab_POD_EN\\PROLabSmart.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_POD_EN.ahk\"");
    getProjectStatus('PROLab_D2010', 'c:/daten/prolab_D2010\\PROLab_D2010.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_D2010.ahk\"");
	}
	if ($_SERVER['SERVER_NAME'] == 'screenshot02-pc') {
    getProjectStatus('RingDat_Online.Human', 'C:\\xampp\\htdocs\\lvu\\tests\\PhantomJS\Alter_des_Masterbranches.txt', $sCasperJS . 'casperjs_kickstart.ahk human"');
    getProjectStatus('RingDat_Online.IBBL', 'C:\\xampp\\htdocs\\lvu\\tests\\PhantomJS\Alter_des_Masterbranches.txt', $sCasperJS . 'casperjs_kickstart.ahk ibbl"');
    getProjectStatus('RingDat_Online.InstitutEignungspruefung', 'C:\\xampp\\htdocs\\lvu\\tests\\PhantomJS\Alter_des_Masterbranches.txt', $sCasperJS . 'casperjs_kickstart.ahk InstitutEignungspruefung"');
    getProjectStatus('RingDat_Online.UBA-Wien', 'C:\\xampp\\htdocs\\lvu\\tests\\PhantomJS\Alter_des_Masterbranches.txt', $sCasperJS . 'casperjs_kickstart.ahk UBA-Wien"');
    getProjectStatus('RingDat_Online.Eurofins', 'C:\\xampp\\htdocs\\lvu\\tests\\PhantomJS\Alter_des_Masterbranches.txt', $sCasperJS . 'casperjs_kickstart.ahk eurofins"');
	getProjectStatus('RingDat_Online.NIST-OWM', 'C:\\xampp\\htdocs\\lvu\\tests\\PhantomJS\Alter_des_Masterbranches.txt', $sCasperJS . 'casperjs_kickstart.ahk NIST-OWM"');

	}
    checkFurtherImageConversions();
}

function checkFurtherImageConversions() {
    global $bNeedsFurtherConversions, $smarty;
    if (empty($bNeedsFurtherConversions))
        return;
    if (empty($smarty))
        return;

    $smarty->assign("iframeFurtherImageConversions", $bNeedsFurtherConversions);
}
