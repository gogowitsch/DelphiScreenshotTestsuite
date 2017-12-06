<?php

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
        if (!$sSQL) {
            return;
        }
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
