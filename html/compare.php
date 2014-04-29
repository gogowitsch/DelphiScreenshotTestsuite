<?php

$sTestName = 'download-seite';
if (isset($_GET['sTestName']))
    $sTestName = $_GET['sTestName'];
$sCompare = '"C:\\Program Files\\ImageMagick-6.8.9-Q16\\compare.exe"';
$sCmd = "$sCompare -compose src $sTestName-ist.png $sTestName-soll.png difference.png";
$sRetVal = `$sCmd 2>&1`;

if (trim($sRetVal) == '') {
    header("Content-Type: image/png");
    readfile('difference.png');
} else {
    echo "<tt>$sCmd</tt><br><b style='color:red'>$sRetVal </b>";
}
