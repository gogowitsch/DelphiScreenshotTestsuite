<?php

/**
 * If the GET/POST parameter 'job_done' is present, all files are converted to
 * PNG. Otherwise only a few, to allow requests to the browser to finish
 * quickly.
 *
 * @global int $iConvertedBmpsDuringThisCall
 *
 * @param string $sName Path to file, without file extension
 * @param string $sExt
 *
 * @return string Neuer Dateiname
 */
function convertToPngIfNeeded($sName, &$sExt) {
    if (!file_exists("$sName.$sExt")) {
        return '';
    }
    global $iConvertedBmpsDuringThisCall;
    if (empty($iConvertedBmpsDuringThisCall)) {
        $iConvertedBmpsDuringThisCall = 1;
    }
    else {
        $iConvertedBmpsDuringThisCall++;
    }

    if (stristr($sExt, 'png')) {
        return $sName . '.png';
    }

    if ($iConvertedBmpsDuringThisCall > 3 && empty($_REQUEST['job_done'])) {
        return $sName . '.' . $sExt;
    }

    if (!file_exists("../include/path.ini")) {
        $sUrl = 'http://screenshot01-pc/DelphiScreenshotTestsuite/include/path.ini';
        die("You must add include/path.ini,
          e.g. modified version from <a href='$sUrl' target=path>$sUrl</a>.");
    }

    $sRetVal = '';
    if (in_array(strtolower($sExt), ['jpg', 'peg', 'pdf'], true)) {
        $sPath = str_replace("/", "\\", dirname($sName));
        $sBaseName = basename($sName);
        $sCmd = "cd $sPath && ";
        $sFile = "\"$sBaseName.$sExt\"";
        $sConvert = get_convert_cmd();
        $sCmd .= "$sConvert -density 75 -strip $sFile -append \"$sBaseName.png\"";

        // Bevor convert.exe immer wieder versucht, ein leeres
        // PDF umzuwandeln, wird es vorsorglich entfernt.
        if (filesize("$sName.$sExt") === 0) {
            unlink("$sName.$sExt");
            return '';
        }

        $sRetVal = `$sCmd 2>&1`;
        if (stristr($sRetVal, "Error: /syntaxerror") && stristr(file_get_contents("$sName.$sExt"), "<br")) {
            // es handelt sich um eine HTML-Datei mit PDF-Erweiterung - das produziert zeige_seite_as_PDF.php manchmal.
            rename("$sName.$sExt", "$sName.html");
            $sExt = 'html';
            $sCmd = "cd $sPath & " . 'phantomjs c:\xampp\htdocs\lvu\html\js\rasterize.js "' . "$sBaseName.$sExt" . '" "' . $sBaseName . '.png"';
            $sRetVal = `$sCmd 2>&1`;
        }
    }
    elseif (stristr($sExt, 'bmp')) {
        require_once '../include/ImageCreateFromBMP.inc.php';
        $res = ImageCreateFromBMP($sName . '.bmp');
        if (!is_resource($res)) {
            die("ImageCreateFromBMP($sName) failed with $res");
        }
        imagepng($res, $sName . '.png');
    }
    else {
        die("unexpected extension: convertToPngIfNeeded($sName, $sExt)");
    }
    if (file_exists($sName . '.png') && trim($sRetVal) == '') {
        @unlink("$sName.$sExt");
        $sExt = 'png';
        return "$sName.png";
    }

    if (!$sRetVal) {
        // Success! File was converted.
        return $sName . '.png';
    }
    echo "<h1>Fehler 547 bei $sBaseName</h1>";
    echo "<details open=true><summary>Command line</summary><tt>$sCmd</tt><br><b style='color:red'>$sRetVal </b></details>";
    echo '<details><summary>dir</summary>' . nl2br(`cd $sPath && dir "$sBaseName.*"`) . '</details>';

    echo '<details><summary>File content</summary>';
    readfile("$sPath/$sBaseName.$sExt");
    echo '</details>';
    return '';
}

function get_convert_cmd() {
    $aIni = parse_ini_file("../include/path.ini");

    if (empty($aIni['CONVERT_PATH'])) {
        die("CONVERT_PATH not set: You must specity the path to Imagemagick's convert.exe in include/path.ini");
    }
    else {
        $sConvertPath = $aIni['CONVERT_PATH'];
    }

    if (empty($aIni['GS_PATH'])) {
        die("GS_PATH not set: You must specity the path to ghostscript's gs.exe in include/path.ini");
    }
    else {
        $sGsPath = $aIni['GS_PATH'];
    }

    return "set path=%path%;$sGsPath; && \"$sConvertPath\\convert.exe\"";
}
