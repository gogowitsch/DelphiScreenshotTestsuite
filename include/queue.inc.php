<?php

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
    //Abschlossenes Projekt aus List löschen
    $project = mysql_real_escape_string($_GET['project']);
    $sSQL = "DELETE FROM `job_warteschlange` WHERE `project` = '$project';";
    db_connect($sSQL);

    //E-mail an Nutzer: Projekt wurde beendet
    sendMailToUser("DelphiScreenshotTestsuite", "Das Projekt " . $project . " wurde beendet.");

    //Ersten Eintrag aus Job-Tabelle laden um neues Projekt zu starten
    $sSQL = "SELECT `project` FROM `job_warteschlange` LIMIT 1;";
    $result = db_connect($sSQL);
    $sNextProject = $result[0]['project'];

    //Neues Projekt starten
    header("Location: run_project.php?run=1&project=$sNextProject");
    die;
}

function save_job($aProjects) {
    $_SESSION['email'] = $_POST['email'];

    $email = mysql_real_escape_string($_POST['email']);
    $project = mysql_real_escape_string($aProjects[0]['title']);

    //E-mail an Nutzer: Prüfen ob noch ein Job in Warteschlange vorliegt
    $sSQL = "SELECT * FROM `job_warteschlange`";
    $result = db_connect($sSQL);
    $sProject_number = count($result);

    //E-mail an Nutzer: bereits Projekte in der Warteschlange
    if (!empty($sProject_number)) {
        sendMailToUser("DelphiScreenshotTestsuite", "Das Projekt " . $project
                . " wurde erfolgreich in die Warteschlange gespeichert. " . "<br><br>"
                . "Ihr Projekt kann leider erst zu einem späteren Zeitpunkt gestartet werden ,da sich bereits Projekte in der Liste befinden.");
    }
    else {
        //E-mail an Nutzer: Projekt wurde gestartet
        sendMailToUser("DelphiScreenshotTestsuite", "Das Projekt " . $project . " wurde erfolgreich gestartet.");
    }
    $sSQL = "INSERT INTO `job_warteschlange` (`project`, `user_email`, `Datum`) VALUES('$project', '$email', NOW());";
    db_connect($sSQL);
}
