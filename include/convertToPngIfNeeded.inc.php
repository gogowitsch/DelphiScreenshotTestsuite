<?php

/****
 * @return string Neuer Dateiname
 */
function convertToPngIfNeeded($sName, &$sExt) {
    global $iConvertedBmpsDuringThisCall;
    if (empty($iConvertedBmpsDuringThisCall))
        $iConvertedBmpsDuringThisCall = 1;
    else
        $iConvertedBmpsDuringThisCall++;

    if (stristr($sExt, 'png'))
        return $sName . '.png';
    if ($iConvertedBmpsDuringThisCall > 5)
        return $sName . '.' . $sExt;


    $sConvert = 'set path=%path%;C:\\Program Files\\ImageMagick-6.8.9-Q16;C:\\Program Files\\gs\\gs9.14\\bin && convert.exe';
    if (stristr($sExt, 'pdf')) {
        $sPath = dirname($sName);
        $sBaseName = basename($sName);
        $sCmd = "cd $sPath && ";
        // $sMagic = stristr($sExt, 'bmp') ? 'DIB:' : '';
        $sFile = "\"$sBaseName.$sExt\"";
        // echo $sMagic;
        $sCmd .= "$sConvert -density 75 -strip $sFile -append \"$sBaseName.png\"";

        #die( "$sCmd<hr>");
        $sRetVal = `$sCmd 2>&1`;
    } elseif (stristr($sExt, 'bmp')) {
        require_once('../include/ImageCreateFromBMP.inc.php');
        $res = ImageCreateFromBMP($sName . '.bmp');
        if (!is_resource($res))
            die("ImageCreateFromBMP($sName) failed with $res");
        imagepng($res, $sName . '.png');
        $sRetVal = '';
    } else {
        die("unexpected extension: convertToPngIfNeeded($sName, $sExt)");
    }
    if (file_exists($sName . '.png') && trim($sRetVal) == '') {
        @unlink("$sName.$sExt");
        $sExt = 'png';
        return "$sName.png";
    }

        if (!$sRetVal)
            return $sName . '.png';
    //die("<tt>$sCmd</tt><br><b style='color:red'>$sRetVal </b>");
}
