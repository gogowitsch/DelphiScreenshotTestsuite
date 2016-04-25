<?php

require_once '../include/queue.inc.php';

function startProjectTest($sProject, $sCmd) {
    if (!empty($sCmd)) {
        exec("( $sCmd ) 2>&1", $aOutput, $iStatus);
        $sOutput = join("\n", $aOutput);
        $sColor = $iStatus ? 'red' : 'green';
        $_GET['message'] = "Kommandozeile '$sCmd' wurde ausgef&uuml;hrt " .
                "(Rückgabewert $iStatus). " .
                "<pre style='color:$sColor'>$sOutput</pre>";

        // Create directory (current design) and LOCK-File (running process)
        $sRunningProcessFolderPl = dirname(__FILE__) . '/../html/RunningProcess/';
        $sFileName = $sRunningProcessFolderPl . $sProject . '.LOCK';

        if (!file_exists($sRunningProcessFolderPl)) {
            mkdir($sRunningProcessFolderPl, 0777, true);
        }
        if ($iStatus === 0 && !file_exists($sFileName)) {
            file_put_contents($sFileName, '');
        }
    }
    else {
        $_GET['message'] = 'Fuer dieses Projekt wurde keine Kommandozeile hinterlegt.';
    }
}

/**
 * @param string $p_sExePath wird für Ermittlung des Verfallsdatums verwendet, wird nicht zum Start verwendet
 */
function getProjectStatus($sProject, $p_sExePath, $sCmd = '') {
    global $sExePath, $iExeTime, $aTests, $aProjects, $iStatusSum, $iLocalStatusSum, $aNewTests, $sDoneFile;

    if (!empty($_GET['project']) && $_GET['project'] != $sProject)
        return;

    if (!empty($_GET['run'])) {
        startProjectTest($sProject, $sCmd);
    }

    $sDoneFile = "C:/xampp/htdocs/DelphiScreenshotTestsuite/html/FinishedProcess/$sProject.DONE";

    $sPicturePath = "$sProject/";
    $sExePath = $p_sExePath;
    $iExeTime = filemtime($sExePath);
    $aNewTests = array();
    $iLocalStatusSum = 0;
    array_walk(glob("Bilder/$sPicturePath*-ist.???"), function($sFile) {
        global $aNewTests, $iLocalStatusSum, $sScreenshotName;

        $aNewTests[] = $aTest = getScreenshotStatus($sFile);
        $iLocalStatusSum += $aTest['status'];
        if ($aTest['ext'] == 'pdf' || $aTest['ext'] == 'bmp') {
            global $bNeedsFurtherConversions;
            if (empty($bNeedsFurtherConversions))
                $bNeedsFurtherConversions = 1;
            else
                $bNeedsFurtherConversions++;

            $sScreenshotName = $aTest['name'];
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
    $sCasperJS = 'cmd /c "cd /d C:\\xampp\\htdocs\\lvu && ' .
            'git pull && ' .
            'cd tests\\PhantomJS && ' . $sAhkCmd;
    $sAhkFolderPl = getenv('USERPROFILE') . '\\Desktop\\ScreenshotsPROLab\\Test starten -';
    $sHost = strtolower(gethostname());
    if (in_array($sHost, array('localhost', 'reinecke01-pc'))) {
        getProjectStatus('RingDat_de', 'c:/daten/RingDat_DE\\RingDat4_de.exe', "$sAhkCmd \"$sAhkFolderPl RingDat_DE.ahk\"");
    }
    if (in_array($sHost, array('screenshot01-pc'))) {
        getProjectStatus('PROLab_de', 'c:/daten/prolab_plus_de_AD\\PROLab_de.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_de.ahk\"");
        getProjectStatus('PROLab_en', 'c:/daten/prolab_plus_en_AD\\PROLab_en.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_en.ahk\"");
        getProjectStatus('PROLab_fr', 'c:/daten/prolab_plus_fr_AD\\PROLab_fr.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_fr.ahk\"");
        getProjectStatus('PROLab_es', 'c:/daten/prolab_plus_es_AD\\PROLab_es.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_es.ahk\"");

        getProjectStatus('PROLab_Torte', 'c:/daten/prolab_Torte\\PROLab_de.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_Torte.ahk\"");
        getProjectStatus('PROLab_RVTypKurz', 'c:/daten/prolab_RVTypKurz\\PROLab_de.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_RVTypKurz.ahk\"");

        getProjectStatus('mqVAL_DE', 'c:/daten/mqVAL_DE\\mqVAL.exe', "$sAhkCmd \"$sAhkFolderPl mqVAL_DE.ahk\"");
        getProjectStatus('PROLab_Smart_DE', 'c:/daten/PROLab_Smart_DE_13528\\PROLabSmart.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_Smart_DE.ahk\"");
        getProjectStatus('RingDat_en', 'c:/daten/RingDat_EN\\RingDat4_en.exe', "$sAhkCmd \"$sAhkFolderPl RingDat_EN.ahk\"");
        getProjectStatus('RingDat_de', 'c:/daten/RingDat_DE\\RingDat4_de.exe', "$sAhkCmd \"$sAhkFolderPl RingDat_DE.ahk\"");
        getProjectStatus('PROLab_POD_EN', 'c:/daten/PROLab_POD_EN\\PROLabSmart.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_POD_EN.ahk\"");
        getProjectStatus('PROLab_D2010', 'c:/daten/prolab_D2010\\PROLab_D2010.exe', "$sAhkCmd \"$sAhkFolderPl PROLab_D2010.ahk\"");
        getProjectStatus('CalcInterface_LPP', 'c:/daten/CalcInterface_LPP\\CalcInterface.exe', "$sAhkCmd \"$sAhkFolderPl CalcInterface_LPP.ahk\"");

        // InterVAL soll im Moment nicht in die Jobliste gespeichert werden, da noch kein job_done Parameter von InterVAL übergeben wird.
        getProjectStatus('InterVAL', 'c:/daten/InterVAL\\InterVAL.exe', "C:\\Daten\\InterVAL\\InterVAL.exe /create_test_images C:\\xampp\\htdocs\\DelphiScreenshotTestsuite\\html\\Bilder\\InterVAL");
    }
    if (in_array($sHost, array(
                'screenshot02-pc', 'screenshot01-pc',
                'noack-pc',
                'rot2-pc')) || strstr($sHost, 'blaeul')) {
        getProjectStatus('RingDat_Online.Human', 'C:\\xampp\\htdocs\\lvu\\tests\\PhantomJS\Alter_des_Branches-reviewed-code-for-screenshots.txt', $sCasperJS . 'casperjs_kickstart.ahk human"');
        getProjectStatus('RingDat_Online.IBBL', 'C:\\xampp\\htdocs\\lvu\\tests\\PhantomJS\Alter_des_Branches-reviewed-code-for-screenshots.txt', $sCasperJS . 'casperjs_kickstart.ahk ibbl"');
        getProjectStatus('RingDat_Online.InstitutEignungspruefung', 'C:\\xampp\\htdocs\\lvu\\tests\\PhantomJS\Alter_des_Branches-reviewed-code-for-screenshots.txt', $sCasperJS . 'casperjs_kickstart.ahk InstitutEignungspruefung"');
        getProjectStatus('RingDat_Online.UBA-Wien', 'C:\\xampp\\htdocs\\lvu\\tests\\PhantomJS\Alter_des_Branches-reviewed-code-for-screenshots.txt', $sCasperJS . 'casperjs_kickstart.ahk UBA-Wien"');
        getProjectStatus('RingDat_Online.Eurofins', 'C:\\xampp\\htdocs\\lvu\\tests\\PhantomJS\Alter_des_Branches-reviewed-code-for-screenshots.txt', $sCasperJS . 'casperjs_kickstart.ahk eurofins"');
        getProjectStatus('RingDat_Online.NIST-OWM', 'C:\\xampp\\htdocs\\lvu\\tests\\PhantomJS\Alter_des_Branches-reviewed-code-for-screenshots.txt', $sCasperJS . 'casperjs_kickstart.ahk NIST-OWM"');
        getProjectStatus('RingDat_Online.NIST-MML',
            'C:\xampp\htdocs\lvu\.git\refs\heads\reviewed-code-for-screenshots',
            'cd C:\xampp\htdocs\lvu\tests\PhantomJS && git pull && ( fork_test.sh NIST-MML || echo ok )');
    }
    if (in_array($sHost, array( 'reinecke01-pc' ))) {
        getProjectStatus('LPP.AOCS',
            'C:\Users\oscar.reinecke\lpp\.git\refs\heads\master',
            'cd C:\Users\oscar.reinecke\lpp\admin\tests\PhantomJS && ( fork_test.sh || echo ok )');
        getProjectStatus('RingDat_Online.NIST-MML',
            'C:\xampp.htdocs\rdo\.git\refs\heads\master',
            'cd C:\xampp\htdocs\rdo\tests\PhantomJS && ( fork_test.sh NIST-MML || echo ok )');
    }
    if (in_array($sHost, array( 'noack-kopie01-pc' ))) {
        getProjectStatus('LPP.AOCS',
            'C:\railo\tomcat\webapps\ROOT\.git\refs\heads\master',
            'cd C:\railo\tomcat\webapps\ROOT\admin\tests\PhantomJS && git pull && ( fork_test.sh || echo ok )');
    }

    checkFurtherImageConversions();
}

function checkFurtherImageConversions() {
    global $bNeedsFurtherConversions, $sScreenshotName, $smarty;
    if (empty($bNeedsFurtherConversions))
        return;
    if (empty($smarty))
        return;

    $smarty->assign("iframeFurtherImageConversions", $bNeedsFurtherConversions);
    $smarty->assign("sScreenshotName", $sScreenshotName);
}

function removeRunningTestFolder() {
    $sRunningProcessFolerPl = '"C:\\xampp\\htdocs\\DelphiScreenshotTestsuite\\html\\RunningProcess" /s /q';
    $sCmd = "rmdir " . $sRunningProcessFolerPl;
    exec($sCmd);
}

function killRunningProcess() {
    $sAhkCmd = '"C:\\Program Files\\AutoHotkey\\AutoHotkey.exe" /ErrorStdOut ';
    $sAhkFolderPl = getenv('USERPROFILE') . '\\Desktop\\ScreenshotsPROLab\\';
    $sCmd = "$sAhkCmd \"$sAhkFolderPl" . "auf laufende Tests pruefen.ahk\"" . " KillProcess";

    removeRunningTestFolder();
    exec($sCmd);
}
