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
    if ($iConvertedBmpsDuringThisCall > 3 && empty($_REQUEST['job_done']))
        return $sName . '.' . $sExt;


    if (!file_exists("../include/path.ini")) {
        $sUrl = 'http://screenshot01-pc/DelphiScreenshotTestsuite/include/path.ini';
        die("You must add include/path.ini,
          e.g. modified version from <a href='$sUrl' target=path>$sUrl</a>.");
    }

    $aIni = parse_ini_file("../include/path.ini");

    if (empty($aIni['CONVERT_PATH']))
        die("CONVERT_PATH not set: You must specity the path to Imagemagick's convert.exe in include/path.ini");
    else $sConvertPath = $aIni['CONVERT_PATH'];

    if (empty($aIni['GS_PATH']))
        die("GS_PATH not set: You must specity the path to ghostscript's gs.exe in include/path.ini");
    else $sGsPath = $aIni['GS_PATH'];

    $sConvert = "set path=%path%;$sGsPath; && \"$sConvertPath\\convert.exe\"";

    $sRetVal = '';

    if (stristr($sExt, 'pdf')) {
        $sPath = dirname($sName);
        $sBaseName = basename($sName);
        $sCmd = "cd $sPath && ";
        // $sMagic = stristr($sExt, 'bmp') ? 'DIB:' : '';
        $sFile = "\"$sBaseName.$sExt\"";
        // echo $sMagic;
        $sCmd .= "$sConvert -density 75 -strip $sFile -append \"$sBaseName.png\"";

        #die( "$sCmd<hr>");
        // Bevor convert.exe immer wieder versucht, ein leeres
        // PDF umzuwandeln, wird es vorsorglich entfernt.
        if ( filesize("$sName.$sExt") === 0 )
            unlink("$sName.$sExt");
        else
            $sRetVal = `$sCmd 2>&1`;
    } elseif (stristr($sExt, 'bmp')) {
        require_once('../include/ImageCreateFromBMP.inc.php');
        $res = ImageCreateFromBMP($sName . '.bmp');
        if (!is_resource($res))
            die("ImageCreateFromBMP($sName) failed with $res");
        imagepng($res, $sName . '.png');
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
