<?php


// hier müssen alle Repos eingetragen werden, die dieses Skript verarbeiten kann:
if ($_REQUEST['repo'] == 'ringdat-online')
  chdir("C:\\xampp\\htdocs\\lvu\\tests\\PhantomJS");

// GIT verbieten Nutzer nach Eingaben zu fragen:
putenv("GIT_ASK_YESNO=false");

// Das folgende geht, weil ein Nutzer über den Cred Helper für den Nutzer des Webservers eingetragen wurde.
$iErrorLevel = 0;
$sDatei= "Alter_des_Masterbranches.txt";
if(!file_exists($sDatei)) $iErrorLevel = 2;
else
file_put_contents($sDatei, strftime('%c'));
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
    'Pham@quodata.de',
  );
  $sMsg .= "\nWeitere Details gibt es unter https://git04.quodata.de/it/$_REQUEST[repo].\n\n";
  $sMsg .= "Diese E-Mail ging an " . str_replace("@quodata.de", "", join(', ', $aRecip)) . ".\n";
  foreach($aRecip as $sTo) {
    mail($sTo, $sSubj, $sMsg, $sHeader);
  }
}
die($iErrorLevel);