<?php

require_once __DIR__ . '/../include/queue.inc.php';

function startProjectTest($sProject, $sCmd) {
    global $sAhkCmd, $sAhkFolderPl;
    if (!empty($sCmd)) {
        aufLaufendeTestsPruefen($sCmd, $iStatus, $sOutput, ' && '.$sCmd);

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
        if ($iStatus === 0) {
            file_put_contents($sFileName, '');
        }
    }
    else {
        $_GET['message'] = 'Fuer dieses Projekt wurde keine Kommandozeile hinterlegt.';
    }
}

/**
 * @param string $sProject
 * @param string $p_sExePath wird für Ermittlung des Verfallsdatums verwendet, wird nicht zum Start verwendet
 * @param string $sCmd
 */
function getProjectStatus($sProject, $p_sExePath, $sCmd = '') {
    global $sExePath, $iExeTime, $aTests, $aProjects, $iStatusSum, $iLocalStatusSum, $aNewTests, $sDoneFile;

    if (!empty($_GET['project']) && $_GET['project'] != $sProject)
        return;

    $sExePath = $p_sExePath;
    $iExeTime = @filemtime($sExePath);

    global $conn;
    if (empty($_GET['project'])) {
        // die Startseite wird geladen
        $aResult = db_connect("SELECT * FROM `projects` " .
                              "WHERE `title` = " . $conn->quote($sProject));
        if (count($aResult)) {
            // Cache nutzen
            $aResult[0]['cmd'] = $sCmd;
            $aResult[0]['subscribers'] = getSubscribers($sProject);
            $aResult[0]['last_run'] = getLastRunTime($aResult[0]['title']);
            $aResult[0]['exe_time'] = $iExeTime;
            $aResult[0]['exe_path'] = $sExePath;
            $aProjects[] = $aResult[0];
            return;
        }
    }

    if (!empty($_GET['run'])) {
        startProjectTest($sProject, $sCmd);
    }

    $sDoneFile = getDoneFile($sProject);

    $sPicturePath = "$sProject/";
    $aNewTests = array();
    $iLocalStatusSum = 0;
    $aFiles = glob("Bilder/$sPicturePath*-ist.???");
    array_walk($aFiles, function($sFile) {
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

    $aProject = array(
        'title' => $sProject,
        'status' => $iLocalStatusSum == count($aNewTests) ? 1 : 0,
        'ratio' => $iLocalStatusSum . " / " . count($aNewTests),
        'cmd' => $sCmd,
        'subscribers' => getSubscribers($sProject),
        'last_run' => getLastRunTime($sProject),
        'exe_time' => $iExeTime,
        'exe_path' => $sExePath,
    );

    db_connect("INSERT into `projects` (`title`, `status`, `ratio`) VALUES (".
               $conn->quote($aProject['title']).", ".
               $conn->quote($aProject['status']).", ".
               $conn->quote($aProject['ratio']).")
        ON DUPLICATE KEY UPDATE `status`=VALUES(`status`), `ratio`=VALUES(`ratio`)");

    $aProjects[] = $aProject;

    $aTests = array_merge($aTests, $aNewTests);
    $iStatusSum += $iLocalStatusSum;
}

$sAhkCmd = '"C:\Program Files\AutoHotkey\AutoHotkey.exe" /ErrorStdOut';
$sAhkFolderPl = getenv('USERPROFILE') . '\Desktop\ScreenshotsPROLab';

function getProjectStatusPl($sProject, $sExePath) {
    global $sAhkCmd, $sAhkFolderPl;
    $sAhkScriptFile = $sAhkFolderPl . '\\' . "Test starten - $sProject.ahk";
    if (file_exists($sAhkScriptFile))
        getProjectStatus($sProject, $sExePath, "$sAhkCmd \"$sAhkScriptFile\"");
}

function getRdoProjectStatus($sDesign) {
    $sLvuTestPath = 'C:\xampp\htdocs\lvu\tests\PhantomJS';
    $sLvuGitRef = $sLvuTestPath . '\Alter_des_Branches-reviewed-code-for-screenshots.txt';
    $sGitCmds = "cd /d $sLvuTestPath && git checkout reviewed-code-for-screenshots && git pull";
    getProjectStatus("RingDat_Online.$sDesign", $sLvuGitRef, "$sGitCmds && ( fork_test.sh $sDesign || echo ok )");
}

function getStatusOfAllProjects() {
    global $aTests;
    $aTests = array();
    $sHost = strtolower(gethostname());
    if (in_array($sHost, array('screenshot01-pc'))) {
        getProjectStatusPl('PROLab_de', 'c:/daten/prolab_plus_de_AD\\PROLab_de.exe');
        getProjectStatusPl('PROLab_en', 'c:/daten/prolab_plus_en_AD\\PROLab_en.exe');
        getProjectStatusPl('PROLab_fr', 'c:/daten/prolab_plus_fr_AD\\PROLab_fr.exe');
        getProjectStatusPl('PROLab_es', 'c:/daten/prolab_plus_es_AD\\PROLab_es.exe');

        getProjectStatusPl('PROLab_Torte', 'c:/daten/prolab_Torte\\PROLab_de.exe');
        getProjectStatusPl('PROLab_RVTypKurz', 'c:/daten/prolab_RVTypKurz\\PROLab_de.exe');
        getProjectStatusPl('PROLab_FDA', 'c:/daten/prolab_FDA\\PROLab_en.exe');

        getProjectStatusPl('mqVAL_DE', 'c:/daten/mqVAL_DE\\mqVAL.exe');
        getProjectStatusPl('PROLab_Smart_DE', 'c:/daten/PROLab_Smart_DE_13528\\PROLabSmart.exe');
        getProjectStatusPl('RingDat_en', 'c:/daten/RingDat_EN\\RingDat4_en.exe');
        getProjectStatusPl('RingDat_de', 'c:/daten/RingDat_DE\\RingDat4_de.exe');
        getProjectStatusPl('PROLab_POD_EN', 'c:/daten/PROLab_POD_EN\\PROLabSmart.exe');
        getProjectStatusPl('PROLab_D2010', 'c:/daten/prolab_D2010\\PROLab_D2010.exe');
        getProjectStatusPl('CalcInterface_LPP', 'c:/daten/CalcInterface_LPP\\CalcInterface.exe');

        // InterVAL soll im Moment nicht in die Jobliste gespeichert werden, da noch kein job_done Parameter von InterVAL übergeben wird.
        getProjectStatus('InterVAL', 'c:/daten/InterVAL\\InterVAL.exe', "C:\\Daten\\InterVAL\\InterVAL.exe /create_test_images C:\\xampp\\htdocs\\DelphiScreenshotTestsuite\\html\\Bilder\\InterVAL");
    }
    if (in_array($sHost, array(
                'screenshot02-pc', 'screenshot01-pc',
                'noack-pc',
                'rot2-pc')) || strstr($sHost, 'blaeul')) {
        getRdoProjectStatus('Human');
        getRdoProjectStatus('IBBL');
        getRdoProjectStatus('InstitutEignungspruefung');
        getRdoProjectStatus('UBA-Wien');
        getRdoProjectStatus('Eurofins');
        getRdoProjectStatus('kwrwater.nl');
        getRdoProjectStatus('NIST-OWM');
        getRdoProjectStatus('NIST-MML');
        getRdoProjectStatus('RKI');
        getRdoProjectStatus('KIMW');
    }
    if (in_array($sHost, array('reinecke01-pc'))) {
        getProjectStatus('LPP.AOCS',
            'C:\Users\oscar.reinecke\lpp\.git\refs\heads\master',
            'cd C:\Users\oscar.reinecke\lpp\admin\tests\PhantomJS && ( fork_test.sh || echo ok )');
        getProjectStatus('RingDat_Online.NIST-MML',
            'C:\xampp.htdocs\rdo\.git\refs\heads\master',
            'cd C:\xampp\htdocs\rdo\tests\PhantomJS && ( fork_test.sh NIST-MML || echo ok )');
    }
    if (strstr($sHost, 'noack')) {
        getProjectStatus('LPP.AOCS',
            'C:\railo\tomcat\webapps\ROOT\.git\refs\heads\master',
            'cd C:\railo\tomcat\webapps\ROOT\admin\tests\PhantomJS && git pull && ( fork_test.sh || echo ok )');
    }
    if (gethostname() === 'Web02-fuer-BioVAL-Screenshottests') {
        getProjectStatus('BioVAL',
            'C:\xampp\htdocs\bioval.quodata.de\.git\refs\heads\master',
            'cd C:\xampp\htdocs\bioval.quodata.de\tests\PhantomJS && git pull && ( fork_test.sh || echo ok )');
    }
    if (stristr(gethostname(), 'OEQUASTA') || strstr($sHost, 'blaeul')) {
        getProjectStatus('OEQUASTA',
            'C:\WAMP\htdocs\oequasta\.git\refs\heads\reviewed-code-for-screenshots',
            'cd C:\WAMP\htdocs\OEQUASTA-Kollektivbildung && git pull && ' +
            'cd C:\WAMP\htdocs\oequasta\tests\PhantomJS && git pull && ( fork_test.sh || echo ok )');
    }
    if ($sHost === 'screenshot03-pc') {
        getProjectStatus('BVL-Webeingabe',
            'C:\xampp\htdocs\bvl-webeingabe\refs\heads\master',
            'cd C:\xampp\htdocs\bvl-webeingabe\tests\PhantomJS && start test.sh');
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
    removeRunningTestFolder();
    aufLaufendeTestsPruefen($sCmd, $iStatus, $sLastLine, 'killProcess');
    if ($iStatus)
        die("<h1>Fehler</h1>$sLastLine<br><tt>$sCmd</tt>");
}

/**
 * @param string $sCmd gibt die Kommandozeile zurück (für Fehlermeldungen)
 * @param int $iStatus Errorlevel (0 = Erfolg)
 * @param string $sOutput - Ausgabe von AHK
 * @param string $sAhkParam - weitere an AHK zu übergebende Parameter
 */
function aufLaufendeTestsPruefen(&$sCmd, &$iStatus, &$sOutput, $sAhkParam) {
    global $sAhkCmd, $sAhkFolderPl;

    $sCheckRunningTestsScript = $sAhkFolderPl . '/auf laufende Tests pruefen.ahk';
    if (!file_exists($sCheckRunningTestsScript)) {
        $sDesktop = dirname($sAhkFolderPl);
        $sCmd = "git clone https://Account-Zum-Pullen-Auf-Produktionsservern:xgtnuSNZ-2zXgNyGtcgj@git04.quodata.de/it/DelphiScreenshotTestsuite-AHK.git $sAhkFolderPl 2>&1";
        exec($sCmd, $aOutput, $iStatus);
        if ($iStatus) {
            $sOutput = join("\n", $aOutput);
            die($sOutput);
            return;
        }
    }
    $sCmd = "$sAhkCmd \"$sCheckRunningTestsScript\" $sAhkParam 2>&1";

    exec($sCmd, $aOutput, $iStatus);
    $sOutput = join("\n", $aOutput);
}

function getLastRunTime($sProject) {
    $sDoneFile = getDoneFile($sProject);
    if (!file_exists($sDoneFile)) return 0;

    return filemtime($sDoneFile);
}
