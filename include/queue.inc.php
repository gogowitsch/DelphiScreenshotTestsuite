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
    $path = '../PHPMailer/class.phpmailer.php';
    if (file_exists($path))
        require_once($path);
    else
        require_once("../$path");

    $mail = new PHPMailer(true);
    $mail->IsSMTP();
    $sMailTo = mysql_real_escape_string($_POST['email']);

    try {
        //Smtp-Settings
        include_once '/smtp-settings.inc.php';
        $mail->SMTPAuth = true;
        $mail->CharSet = "UTF-8";
        $mail->IsHTML(true);
        $mail->SMTPDebug = 2;

        //E-mail-Inhalt
        $mail->From = "peter.oertel@quodata.de";
        $mail->AddAddress($sMailTo);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->Send();
    }
    catch (phpmailerException $e) {
        echo "<h1>PHPMailer Exception: </h1><br>" . $e->getMessage();
    }
}

function db_connect($sSQL) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "delphiscreenshottestsuite";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare($sSQL);
        $stmt->execute();

        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
    }
    catch (PDOException $e) {
        echo $sSQL . "<br>" . $e->getMessage();
    }
    $conn = null;

    return $result;
}

function check_queue() {
    // Abschlossenes Projekt aus List löschen
    $project = mysql_real_escape_string($_GET['project']);
    $sSQL = "DELETE FROM `job_warteschlange` WHERE `project` = '$project';";
    db_connect($sSQL);

    // E-mail an Nutzer: Projekt wurde beendet
    $sServername = $_SERVER['SERVER_NAME'];
    sendMailToUser("DelphiScreenshotTestsuite", "Diese E-Mail wurde automatisch von $sServername erstellt.<br><br>"
            . "Das Projekt https://localhost/DelphiScreenshotTestsuite/html/index.php?project=$project wurde beendet."
            . "<br><br>Weitere Informationen über die DelphiScreenshotTestsuite finden Sie unter:"
            . "https://wiki.quodata.de/index.php?title=DelphiScreenshotTestsuite"
    );

    //Ersten Eintrag aus Job-Tabelle laden um neues Projekt zu starten
    $sSQL = "SELECT `project` FROM `job_warteschlange` LIMIT 1;";
    $result = db_connect($sSQL);
    $sNextProject = $result[0]['project'];

    //Neues Projekt starten
    header("Location: run_project.php?run=1&project=$sNextProject");
    die;
}

function save_job($aProjects) {
    $sEmail = empty($_POST['email']) ? '' : $_POST['email'];
    $_SESSION['email'] = $sEmail;

    $sSafeEmail = mysql_real_escape_string($sEmail);
    $project = mysql_real_escape_string($aProjects[0]['title']);

    // E-mail an Nutzer: Prüfen ob noch ein Job in Warteschlange vorliegt
    $sSQL = "SELECT * FROM `job_warteschlange`";
    $result = db_connect($sSQL);
    $sProject_number = count($result);
    $sServername = $_SERVER['SERVER_NAME'];

    // E-mail an Nutzer: bereits Projekte in der Warteschlange
    if (!empty($sProject_number)) {
        $sServername = $_SERVER['SERVER_NAME'];
        sendMailToUser("DelphiScreenshotTestsuite", "Diese E-Mail wurde automatisch von $sServername erstellt.<br><br>"
                . "Das Projekt https://$sServername/?project=$project"
                . " wurde erfolgreich in die Warteschlange aufgenommen. " . "<br><br>"
                . "Leider kann Ihr Projekt erst zu einem späteren Zeitpunkt gestartet werden, da sich bereits Projekte in der Liste befinden."
                . "<br><br>Weitere Informationen über die DelphiScreenshotTestsuite finden Sie unter:"
                . "<br><br>https://wiki.quodata.de/index.php?title=DelphiScreenshotTestsuite");
    }
    else {
        //E-mail an Nutzer: Projekt wurde gestartet
        sendMailToUser("DelphiScreenshotTestsuite", "Diese E-Mail wurde automatisch von $sServername erstellt.<br><br>" . "Das Projekt "
                . "http://$sServername/?project=$project wurde erfolgreich gestartet."
                . "<br><br>Weitere Informationen über die DelphiScreenshotTestsuite finden Sie unter:"
                . "<br><br>https://wiki.quodata.de/index.php?title=DelphiScreenshotTestsuite");
    }
    $sSQL = "INSERT INTO `job_warteschlange` (`project`, `user_email`, `Datum`) VALUES('$project', '$sSafeEmail', NOW());";
    db_connect($sSQL);
}

if (!empty($smarty)) {
    session_start();

    $sEmail = "";
    if (!empty($_SESSION['email'])) {
        $sEmail = $_SESSION['email'];
    }

    $smarty->assign("sEmail", $sEmail);
}
