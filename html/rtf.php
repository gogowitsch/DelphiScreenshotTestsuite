<?php

$filepath = $_REQUEST['file'];

header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=$filepath.rtf");
header("Content-Type: text/rtf");
header("Content-Transfer-Encoding: binary");
header('Content-Length: ' . filesize($filepath));

readfile($filepath);