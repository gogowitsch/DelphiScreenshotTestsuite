<?php

ob_start();

$sTestName = 'download-seite';
if (isset($_GET['sTestName']))
    $sTestName = str_replace('/', '\\', $_GET['sTestName']);

$sStem = substr($sTestName, 0, -8);
$sExt = substr($sTestName, -3);

require_once('../include/convertToPngIfNeeded.inc.php');
$sFileIst = convertToPngIfNeeded("$sStem-ist", $sExt);
$sFileSoll = convertToPngIfNeeded("$sStem-soll", $sExt);

// später: WinMerge HTML-Export des Vergleichs hier einbauen

require_once '../include/screenshot.inc.php';

$sRetVal = createDifferenceImage($sFileIst, $sFileSoll, $sStem);

if (trim($sRetVal) == '') {
    $sPhpWarnings = ob_get_clean();
    if (strlen($sPhpWarnings) < 4) $sPhpWarnings = ''; // nur ein BOM
    else echo "$sPhpWarnings;<br>File: $sStem-difference.png";
    if (!$sPhpWarnings) {
        header("Content-Type: image/png");
    }
    readfile("$sStem-difference.png");
} else {
    echo "<tt>$sCmd</tt><br><pre style='color:red'>$sRetVal </pre>";
}
