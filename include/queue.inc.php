<?php

// benötigt MySQL-Datenbank, zu Erstellen mit
//~   CREATE DATABASE IF NOT EXISTS `delphiscreenshottestsuite`;
//~   USE delphiscreenshottestsuite;
//~   CREATE TABLE IF NOT EXISTS `job_warteschlange` (
//~     `project` varchar(255) DEFAULT NULL,
//~     `user_email` varchar(255) DEFAULT NULL,
//~     `Datum` datetime NULL DEFAULT CURRENT_TIMESTAMP,
//~     `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY
//~   ) ENGINE=InnoDB DEFAULT CHARSET=latin1;


function sendMailToUser($subject, $message) {
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
        if (file_exists('smtp-settings.inc.php'))
            include 'smtp-settings.inc.php';
        $mail->SMTPAuth = !empty($mail->Password);
        $mail->CharSet = "UTF-8";
        $mail->IsHTML(true);
        $mail->SMTPDebug = 2;

        //E-mail-Inhalt
        $mail->From = "peter.oertel@quodata.de";
        $mail->AddAddress($sMailTo);
        $mail->Subject = $subject;
        $mail->Body = "$message<br><br><hr><small>Weitere Informationen über die DelphiScreenshotTestsuite finden Sie unter:"
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

function ProjectDone_RemoveFromQueue() {
    global $conn;

    // Abschlossenes Projekt aus List löschen
    db_connect('');
    $project = $conn->quote($_GET['project']);
    $sSQL = "SELECT user_email FROM `job_warteschlange` WHERE `project` = $project;";
    $aMail = db_connect($sSQL);
    $sSQL = "DELETE FROM `job_warteschlange` WHERE `project` = $project;";
    db_connect($sSQL);

    // E-mail an Nutzer: Projekt wurde beendet
    if (!empty($aMail[0]['user_email'])) {
        $sServername = $_SERVER['SERVER_NAME'];
        $sSubject = "[DelphiScreenshotTestsuite] $project abgeschlossen";
        $sBody = "Diese E-Mail wurde automatisch von " . __FILE__ . " auf $sServername erstellt.<br><br>"
                . "Der Test des Projektes <a href='http://$sServername/?project=$project'>$project</a> wurde abgeschlossen.";
        sendMailToUser($sSubject, $sBody, $aMail[0]['user_email']);
    }

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
    $sEmail = empty($_POST['email']) ? '' : $_POST['email'];
    if ($sEmail)
        $_SESSION['email'] = $sEmail;

    db_connect('');
    $sSafeEmail = $conn->quote($sEmail);
    $project = $conn->quote($_GET['project']);

    $sSQL = "INSERT INTO `job_warteschlange` (`project`, `user_email`, `Datum`) VALUES ($project, $sSafeEmail, NOW());";
    db_connect($sSQL);

    // ID-Spalte hinzufügen, damit Tabelle in phpMyAdmin bearbeitbar wird
    $aHasId = db_connect("SHOW COLUMNS FROM `job_warteschlange` LIKE 'ID'");
    if (empty($aHasId))
        db_connect("ALTER TABLE `job_warteschlange` ADD `ID` INT AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`ID`)");
}

session_start();

if (!empty($smarty)) {
    $sEmail = "";
    if (!empty($_SESSION['email'])) {
        $sEmail = $_SESSION['email'];
    }

    $smarty->assign("sEmail", $sEmail);
}
