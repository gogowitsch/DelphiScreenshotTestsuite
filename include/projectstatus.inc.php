<?php

require_once '../include/queue.inc.php';
require_once '../include/subscribers.inc.php';

function startProjectTest($sProject, $sCmd) {
    global $sAhkCmd, $sAhkFolderPl;
    if (!empty($sCmd)) {
        $sCheckRunningTestsScript = $sAhkFolderPl . '/auf laufende Tests pruefen.ahk';
        if (file_exists($sCheckRunningTestsScript))
            $sCmd = "$sAhkCmd \"$sCheckRunningTestsScript\" && $sCmd";
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
        if ($iStatus === 0) {
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

    db_connect("CREATE TABLE IF NOT EXISTS `projects` ( ".
               "`title` VARCHAR(255),".
               "`status` BOOLEAN,".
               "`ratio` VARCHAR(255))");

    ob_start();
    db_connect("ALTER TABLE `projects` ADD `ID` INT AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`ID`)");
    db_connect("ALTER TABLE `projects` ADD COLUMN `duration` VARCHAR(32) NULL DEFAULT '';");
    db_connect("ALTER TABLE `projects` ADD UNIQUE(`title`);");
    ob_end_clean();

    if (!empty($_GET['project']) && $_GET['project'] != $sProject)
        return;

    global $conn;
    if (empty($_GET['project'])) {
        // die Startseite wird geladen
        $aResult = db_connect("SELECT * FROM `projects` " .
                              "WHERE `title` = " . $conn->quote($sProject));
        if (count($aResult)) {
            // Cache nutzen
            $aResult[0]['cmd'] = $sCmd;
            $aResult[0]['subscribers'] = getSubscribers($sProject);
            $aProjects[] = $aResult[0];
            return;
        }
    }

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

    $aProject = array(
        'title' => $sProject,
        'status' => $iLocalStatusSum == count($aNewTests) ? 1 : 0,
        'ratio' => $iLocalStatusSum . " / " . count($aNewTests),
        'cmd' => $sCmd,
        'subscribers' => getSubscribers($sProject)
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
    $sGitCmds = "cd /d $sLvuTestPath && git pull";
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
        getRdoProjectStatus('NIST-OWM');
        getRdoProjectStatus('NIST-MML');
        getRdoProjectStatus('RKI');
    }
    if (in_array($sHost, array('reinecke01-pc'))) {
        getProjectStatus('LPP.AOCS',
            'C:\Users\oscar.reinecke\lpp\.git\refs\heads\master',
            'cd C:\Users\oscar.reinecke\lpp\admin\tests\PhantomJS && ( fork_test.sh || echo ok )');
        getProjectStatus('RingDat_Online.NIST-MML',
            'C:\xampp.htdocs\rdo\.git\refs\heads\master',
            'cd C:\xampp\htdocs\rdo\tests\PhantomJS && ( fork_test.sh NIST-MML || echo ok )');
    }
    if (in_array($sHost, array('noack-kopie01-pc', 'noack-pc'))) {
        getProjectStatus('LPP.AOCS',
            'C:\railo\tomcat\webapps\ROOT\.git\refs\heads\master',
            'cd C:\railo\tomcat\webapps\ROOT\admin\tests\PhantomJS && git pull && ( fork_test.sh || echo ok )');
    }
    if (gethostname() === 'Web02-fuer-BioVAL-Screenshottests') {
        getProjectStatus('BioVAL',
            'C:\xampp\htdocs\bioval.quodata.de\.git\refs\heads\master',
            'cd C:\xampp\htdocs\bioval.quodata.de\tests\PhantomJS && git pull && ( fork_test.sh || echo ok )');
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
    global $sAhkCmd, $sAhkFolderPl;
    $sCheckRunningTestsScript = $sAhkFolderPl . '/auf laufende Tests pruefen.ahk';
    $sCmd = "$sAhkCmd \"$sCheckRunningTestsScript\" killProcess";

    removeRunningTestFolder();
    $sLastLine = exec($sCmd, $aOutput, $iStatus);
    if ($iStatus)
        die("<h1>Fehler</h1>$sLastLine<br><tt>$sCmd</tt>");
}
