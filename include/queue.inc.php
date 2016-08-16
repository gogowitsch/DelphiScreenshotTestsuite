<?php

db_connect("CREATE TABLE IF NOT EXISTS
`job_warteschlange` (
  `project` varchar(255) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `Datum` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY
) ENGINE=InnoDB DEFAULT CHARSET=latin1; ");

db_connect("CREATE TABLE IF NOT EXISTS
`comments` (
  `project` varchar(255) DEFAULT NULL,
  `test` varchar(255) NOT NULL DEFAULT '',
  `comment` varchar(255) DEFAULT NULL,
  `time` datetime DEFAULT NULL
);");

function sendMailToUser($sMailTo, $subject, $message) {
    $path = 'PHPMailer/class.phpmailer.php';
    if (file_exists("../$path")) {
        // TODO: auch class smtp laden
        require_once("../$path");
    }
    else {
        echo `echo %cd%`;
        require_once("../../lvu/$path");
    }

    $mail = new PHPMailer(true);
    $mail->IsSMTP();

    try {
        //Smtp-Settings
        $mail->Host = "web-exch.quodata.de";
        if (file_exists('smtp-settings.inc.php'))
            include 'smtp-settings.inc.php';
        $mail->SMTPAuth = !empty($mail->Password);
        $mail->CharSet = "UTF-8";
        $mail->IsHTML(true);
        $mail->SMTPDebug = 0;

        //E-mail-Inhalt
        $mail->From = "oscar.reinecke@quodata.de";
        $mail->FromName = "DelphiScreenshotTestsuite";
        $mail->AddAddress($sMailTo);
        $mail->Subject = $subject;
        $mail->Body = "$message<br><br><hr><small>Weitere Informationen über die DelphiScreenshotTestsuite finden Sie unter: "
                . "<a href='https://wiki.quodata.de/?title=DelphiScreenshotTestsuite'>wiki.quodata.de/?title=DelphiScreenshotTestsuite</a>.</small>";
        if (!$mail->Send())
            die("Check your mail-Einstellungen!");
    }
    catch (phpmailerException $e) {
        echo "<h1>PHPMailer Exception: </h1><br>" . $e->getMessage();
    }
}

function db_connect($sSQL) {
    global $conn;
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "delphiscreenshottestsuite";

    try {
        if (empty($conn)) {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        if (!$sSQL)
            return;
        $stmt = $conn->prepare($sSQL);
        $stmt->execute();
        if (stristr($sSQL, 'SELECT')) {
            // bei UPDATE und DELETE gibt es kein Ergebnis
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            return $stmt->fetchAll();
        }
    }
    catch (PDOException $e) {
        echo $sSQL . "<br>" . $e->getMessage();
    }
}

// Count all outdated files
function countOutdatedFiles($aNewTests) {
    $aVeraltet = array();
    foreach ($aNewTests as $key => $value) {
        if (strpos($value['desc'], 'Ist-Datei kommt nicht von aktueller Alter_des_Masterbranches') !== false) {
            array_push($aVeraltet, $value['desc']);
        }
    }
    $iVeraltet = count($aVeraltet);

    return $iVeraltet;
}

function addToListOfEmailAddresses($sProject, $sMail, &$aMailAddresses) {
    if (strpos($_GET['project'], $sProject) === false)
        return;
    $bNotInList = array_search(strtolower($sMail), array_map('strtolower', $aMailAddresses)) === false;
    if ($bNotInList) {
        $aMailAddresses[]['user_email'] = $sMail;
    }
}

function ProjectDone_RemoveFromQueue($iStatusSum, $aTests, $aNewTests) {
    $aMailAddresses = getEmailsFromQueue();

    $iPercentage = ($iStatusSum / count($aTests)) * 100;

    $project = $_GET['project'];
    // E-mail an Nutzer: Projekt wurde beendet
    $hostname = gethostname();
    $sSubject = "[DelphiScreenshotTestsuite] $project abgeschlossen";
    $sLink = "<a href='http://$hostname/DelphiScreenshotTestsuite/html/index.php?project=$project'>$project</a>";

    $sBody = "Der Test des Projektes $sLink wurde abgeschlossen.<br><br>"
            . "Testergebnisse:<br>"
            . "<span style='background-color: #99ff99'>" . round($iPercentage) . ' %' . " erfolgreich.</span ><br>";
    $sBody .= "<small>Diese E-Mail wurde automatisch von " . __FILE__ . " auf $hostname erstellt.</small> ";

    // E-Mail an Projekt-Verantwortlichen senden
    addToListOfEmailAddresses('RingDat_Online', 'Oscar.Reinecke@quodata.de', $aMailAddresses);
    addToListOfEmailAddresses('CalcInterface', 'blaeul@quodata.de', $aMailAddresses);
    addToListOfEmailAddresses('PROLab', 'helling@quodata.de', $aMailAddresses);

    $sRecipients = '';
    foreach ($aMailAddresses as $aMailAddress) {
        $sRecipients .= ", " . $aMailAddress['user_email'];
    }
    $sRecipients = "Sie ging an folgende Empfänger: " . substr($sRecipients, 2);
    $sBody .= "<span style='background-color: #c6fed1; font-weight: bold'>$sRecipients</span ><br>";
    foreach ($aMailAddresses as $aMailAddress) {
        sendMailToUser($aMailAddress['user_email'], $sSubject, $sBody);
    }

    removeProjectFromQueue();

    startNextProject();
}

function ProjectKilled_RemoveFromQueue() {
    $project = $_GET['project'];
    // E-mail an Nutzer: Test wurde abgebrochen
    $hostname = gethostname();
    $sSubject = "[DelphiScreenshotTestsuite] $project abgebrochen!!";
    $sLink = "<a href='http://$hostname/DelphiScreenshotTestsuite/html/index.php?project=$project'>$project</a>";

    $sBody = "<span style='background-color:#FF9999'>Der Test des Projektes $sLink wurde abgebrochen</span>.<br><br>";
    $sBody .= "<small>Diese E-Mail wurde automatisch von " . __FILE__ . " auf $hostname erstellt.</small>";

    foreach (getEmailsFromQueue() as $sMailAddress) {
        sendMailToUser($sMailAddress['user_email'], $sSubject, $sBody);
    }

    removeProjectFromQueue();

    killRunningProcess();

    // Even if we kill all mintty's and bash'es at once, I guess bash still
    // manages to curl /index.php?job_done=1&etc, which, at the end, launches
    // an extra CasperJS terminal window. This happens only on screenshot01-pc.
    // I inserted a tiny delay to allow startProjectTest() to detect the existing
    // PhantomJS process. Otherwise PhantomJS wouldn't have started yet and we
    // ended up with two CasperJS terminal windows.
    sleep(5);

    startNextProject();
}

function getEmailsFromQueue() {
    global $conn;

    db_connect('');
    $project = $conn->quote($_GET['project']);
    $sSQL = "SELECT DISTINCT user_email
            FROM `job_warteschlange` WHERE `project` = $project
            AND user_email <> '';";
    return db_connect($sSQL);
}

function removeProjectFromQueue() {
    global $conn;
    $project = $conn->quote($_GET['project']);

    // Abschlossenes Projekt aus List löschen
    $sSQL = "DELETE FROM `job_warteschlange` WHERE `project` = $project;";
    db_connect($sSQL);
}

function startNextProject() {
    global $conn;

    // Ersten Eintrag aus Job-Tabelle laden um neues Projekt zu starten
    $sSQL = "SELECT `project` FROM `job_warteschlange` LIMIT 1;";
    $result = db_connect($sSQL);

    if (!empty($result[0]['project'])) {
        $sNextProject = $result[0]['project'];

        // nächstes Projekt starten
        header("Location: run_project.php?run=1&project=$sNextProject");
        die;
    }
}

function save_job() {
    global $conn;
    db_connect('');

    /* InterVAL soll im Moment nicht in die Jobliste gespeichert werden,
     * da noch kein job_done Parameter von InterVAL übergeben wird.
     */
    if ($_GET['project'] == "InterVAL") {
        return;
    }

    $sEmail = empty($_POST['email']) ? '' : $_POST['email'];
    if ($sEmail)
        $_SESSION['email'] = $sEmail;

    $sSafeEmail = $conn->quote($sEmail);


    $project = $conn->quote($_GET['project']);

    $sSQL = "INSERT INTO `job_warteschlange` (`project`, `user_email`, `Datum`) VALUES ($project, $sSafeEmail, NOW());";
    db_connect($sSQL);

    // ID-Spalte hinzufügen, damit Tabelle in phpMyAdmin bearbeitbar wird
    $aHasId = db_connect("SHOW COLUMNS FROM `job_warteschlange` LIKE 'ID'");
    if (empty($aHasId))
        db_connect("ALTER TABLE `job_warteschlange` ADD `ID` INT AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`ID`)");
}

function queued() {
    global $conn;
    $project = $conn->quote($_GET['project']);
    return count(db_connect("SELECT * from `job_warteschlange` WHERE `project` = $project;"));
}

function save_comment($aTest) {
    global $conn;
    db_connect('');

    // Screenshot-Kommentar aus Datenbank laden
    $aComment = load_comment_data($aTest);

    $sComment = empty($_POST['textarea']) ? '' : $_POST['textarea'];
    $sSafeComment = $conn->quote($sComment);
    $aTest = $conn->quote($aTest['title']);
    $project = $conn->quote($_GET['project']);

    if (!empty($_POST['textarea'])) {
        // Screenshot-Kommentar aktualisieren
        if (!empty($aComment[0]['comment'])) {
            $sSQL = "UPDATE `comments` SET `comment` = $sSafeComment, `time` = NOW() WHERE `project` = $project AND `test` = $aTest";
            db_connect($sSQL);
        }
        else {
            // Screenshot-Kommentar einfügen
            $sSQL = "INSERT INTO `comments` (`comment`, `test`, `project`, `time`) VALUES ($sSafeComment, $aTest, $project, NOW())";
            db_connect($sSQL);
        }
    }
    else {
        $sSQL = "DELETE FROM `comments` WHERE `test` = $aTest";
        db_connect($sSQL);
    }
}

function load_comment_data($aTest) {
    global $conn;
    db_connect('');

    $project = $conn->quote($_GET['project']);
    $aTest = $conn->quote($aTest['title']);

    // Screenshot-Kommentar aus DB laden
    $sSQL = "SELECT * FROM `comments` WHERE `project` = $project AND `test` = $aTest";
    $aComment = db_connect($sSQL);

    return $aComment;
}

session_start();

if (!empty($smarty)) {
    $sEmail = "";
    if (!empty($_SESSION['email'])) {
        $sEmail = $_SESSION['email'];
    }

    $smarty->assign("sEmail", $sEmail);
}