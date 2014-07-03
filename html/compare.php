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

$sCompare = '"C:\\Program Files\\ImageMagick-6.8.9-Q16\\compare.exe"';
if (file_exists('Bilder/difference.png')) {
    unlink('Bilder/difference.png');
}
$sCmd = "$sCompare -compose src \"$sFileIst\" \"$sFileSoll\" Bilder\\difference.png";
$sRetVal = `$sCmd 2>&1`;

if (trim($sRetVal) == '') {
    $sPhpWarnings = ob_get_flush();
    if (!$sPhpWarnings) header("Content-Type: image/png");
    readfile('Bilder/difference.png');
} else {
    echo "<tt>$sCmd</tt><br><pre style='color:red'>$sRetVal </pre>";
}
