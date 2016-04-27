<?php

$iErrorLevel = 0;
$data = json_decode(file_get_contents('php://input'), true);

// hier müssen alle Repos eingetragen werden, die dieses Skript verarbeiten kann:
if (basename($data['repository']['url']) == 'rdo.git')
    chdir("C:\\xampp\\htdocs\\lvu\\tests\\PhantomJS");

$sBranch = basename($data['ref']); // Beispielwert ist 'refs/heads/master'
$sDatei = "Alter_des_Branches-$sBranch.txt";
if (true || $sBranch == 'reviewed-code-for-screenshots') {
    file_put_contents($sDatei, strftime('%c'));
    file_put_contents($sDatei, print_r($data, true), FILE_APPEND);
    $iStamp = 0;
    foreach ($data['commits'] as $aCommit) {
        $iStamp = max($iStamp, strtotime($aCommit['timestamp']));
    }
    if ($iStamp) {
        // wenn der Rechner nicht beim Push/Merge verfügbar ist, wird
        // der Hook periodisch wiederholt. Was für die Testaktualität zählt,
        // ist die Commit-Zeit $iStamp, nicht die Ausführungszeit des Skriptes
        touch($sDatei, $iStamp);
        $sCmd = 'curl.exe -o - "http://localhost/run_project.php?project=RingDat_Online.IBBL&run=1"';
        $result = system("$sCmd 2>&1", $iErrorLevel);
        file_put_contents($sDatei, $result, FILE_APPEND);
        #sleep(5);
        #`curl.exe -o - "http://localhost/run_project.php?project=RingDat_Online.InstitutEignungspruefung&run=1"`;
    }
}


if ($iErrorLevel > 0) {
    $sSubj = basename(__FILE__) . ': error';
    $sMsg = "An error occured while running the file " .
            __FILE__ . ":\n\n" .
            $sCmd . "\n\n" . $result . "\n\n" .
            "Repo: $_REQUEST[repo] - " . `echo %cd%`;

    $sHeader = "From: " . basename(__FILE__) . '@quodata.de';
    $aRecip = array(
        'Blaeul@quodata.de',
        'Oertel@quodata.de',
        'Sgorzaly@quodata.de',
        'Oscar.Reinecke@quodata.de',
    );
    $sMsg .= "\nWeitere Details gibt es unter https://git04.quodata.de/it/$_REQUEST[repo].\n\n";
    $sMsg .= "Diese E-Mail ging an " . str_replace("@quodata.de", "", join(', ', $aRecip)) . ".\n";
    foreach ($aRecip as $sTo) {
        mail($sTo, $sSubj, $sMsg, $sHeader);
    }
}
die($iErrorLevel);
