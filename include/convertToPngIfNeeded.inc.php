<?php

function convertToPngIfNeeded($sName, &$sExt) {
  if (stristr($sExt, 'png')) return $sName . '.png';

  if (file_exists($sName . '.png')) unlink($sName . '.png');

  if (stristr($sExt, 'bmp')) {
    require_once('../include/ImageCreateFromBMP.inc.php');
    $res=ImageCreateFromBMP($sName . '.bmp');
    if (!is_resource($res)) die('ImageCreateFromBMP failed with ' . $res);
    imagepng($res, $sName . '.png');
    $sRetVal ='';
  } else {
    $sConvert = '"C:\\Program Files\\ImageMagick-6.8.9-Q16\\convert.exe"';
    $sPath = dirname($sName);
    $sBaseName = basename($sName);
    $sCmd = "cd $sPath && ";
    $sMagic = stristr($sExt, 'bmp') ? 'DIB:' : '';
    $sFile = "\"$sMagic$sBaseName.$sExt\"";
    echo $sMagic;
    $sCmd .= "$sConvert $sFile \"$sBaseName.png\"";
  #die( "$sCmd<hr>");
    $sRetVal = `$sCmd 2>&1`;
  }
  if (file_exists($sName . '.png') && trim($sRetVal) == '') {
    unlink("$sName.$sExt");
    $sExt = 'png';
    return "$sName.png";
  }

  die("<tt>$sCmd</tt><br><b style='color:red'>$sRetVal </b>");
}
