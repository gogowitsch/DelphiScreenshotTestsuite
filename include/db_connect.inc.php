<?php

(function () {
    /** @var string[] $arrSQLInstallQuery */
    /** @var int $iRev */
    require __DIR__ . '/sql_queries.inc.php';
    $QueryResult = db_connect("SELECT var_val FROM `config` WHERE var_name = 'db_revision'");

    $iAktuelleDbRevision = $QueryResult[0]['var_val'];
    if ($iAktuelleDbRevision < $iRev) {
        foreach ($arrSQLInstallQuery as $i => $sSql) {
            if ($i > $iAktuelleDbRevision) {
                db_connect($sSql);
            }
        }
    }
})();

/**
 * @param string $sSQL
 *
 * @global PDO $conn
 * @return false|array
 */
function db_connect($sSQL) {
    global $conn;

    $sDatabaseConfig = __DIR__ . '/../config/config.database.inc.php';
    try {
        if (empty($conn)) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "delphiscreenshottestsuite";

            // allow instances to override the default settings, e.g. specify a password
            if (file_exists($sDatabaseConfig)) {
                /** @noinspection PhpIncludeInspection */
                include $sDatabaseConfig;
            }

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        if (!$sSQL) {
            return false;
        }
        $stmt = $conn->query($sSQL);
        if (false !== stripos($sSQL, 'SELECT')) {
            // bei UPDATE und DELETE gibt es kein Ergebnis
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            return $stmt->fetchAll();
        }
    }
    catch (PDOException $e) {
        echo $sSQL . "<br><span style='color:red'>{$e->getMessage()}</span>";
        if (strpos($e->getMessage(), 'SQLSTATE[HY000]') !== false) {
            die("You can define your database configuration in the (optional) file <tt>$sDatabaseConfig</tt>.<br>\n");
        }
    }
    return false;
}
