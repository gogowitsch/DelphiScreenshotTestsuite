<?php

/* Diese Datei wird von footer.tpl als <iframe> eingebunden */

?><head>
<style>
  body {  font-family: Arial; margin:0; padding:0; overflow: hidden; }
</style>
<?php

require '../include/smarty.inc.php';
require '../include/screenshot.inc.php';
require '../include/projectstatus.inc.php';

$aTests = array();
$aProjects = array();
$iStatusSum = 0;

$_REQUEST['andConvertImages'] = 1;

getStatusOfAllProjects();

global $bNeedsFurtherConversions;
if (empty($bNeedsFurtherConversions)) die;

echo '<meta http-equiv="refresh" content="5; URL=convert_images.php" ></head>';

echo "Noch $bNeedsFurtherConversions Bilder zu konvertieren... <span title='$sScreenshotName'>[?]</span>";