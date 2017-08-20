<?php

require_once '../include/subscribers.inc.php';

function sendMailToUser($sMailTo, $subject, $message) {
    $path = 'PHPMailer/class.phpmailer.php';
    if (!file_exists("../$path")) {
        `echo %cd%`;
        $path = "../lvu/$path";
    }
    require_once "../$path";
    require_once dirname("../$path") . '/class.SMTP.php';

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

    try {
        if (empty($conn)) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "delphiscreenshottestsuite";

            // allow instances to override the default settings, e.g. specify a password
            $sDatabaseConfig = __DIR__ . '/../config/config.database.inc.php';
            if (file_exists($sDatabaseConfig)) {
                include $sDatabaseConfig;
            }

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
        echo $sSQL . "<br><span style='color:red'>{$e->getMessage()}</span>";
        if (strpos($e->getMessage(), 'SQLSTATE[HY000]') !== FALSE) {
            die("You can define your database configuration in the (optional) file <tt>$sDatabaseConfig</tt>.<br>\n");
        }
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

function getDoneFile($project) {
    $sDoneFolder = "C:/xampp/htdocs/DelphiScreenshotTestsuite/html/FinishedProcess";
    if (!file_exists($sDoneFolder))
        mkdir($sDoneFolder, 0777, true);
    return "$sDoneFolder/$project.DONE";
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


    $aEmails = array($sEmail);

    foreach(getSubscribers($_GET['project']) as $subscriber)
        $aEmails[] = $subscriber['email'];

    $project = $conn->quote($_GET['project']);

    foreach($aEmails as $sEmail) {
        $sEmail = $conn->quote($sEmail);
        $sSQL = "INSERT INTO `job_warteschlange` (`project`, `user_email`, `Datum`) VALUES ($project, $sEmail, NOW());";
        db_connect($sSQL);
    }

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
