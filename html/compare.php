<?php

$sTestName = 'download-seite';
if (isset($_GET['sTestName']))
    $sTestName = str_replace('/', '\\', $_GET['sTestName']);
	
$sStem = substr($sTestName, 0, -8);
$sExt = substr($sTestName, -3);
$sFileIst = "$sStem-ist.$sExt";
$sFileSoll = "$sStem-soll.$sExt";

$sCompare = '"C:\\Program Files\\ImageMagick-6.8.9-Q16\\compare.exe"';
$sCmd = "$sCompare -compose src $sFileIst $sFileSoll Bilder\\difference.png";
$sRetVal = `$sCmd 2>&1`;

if (trim($sRetVal) == '') {
    header("Content-Type: image/png");
    readfile('Bilder/difference.png');
} else {
    echo "<tt>$sCmd</tt><br><b style='color:red'>$sRetVal </b>";
}
