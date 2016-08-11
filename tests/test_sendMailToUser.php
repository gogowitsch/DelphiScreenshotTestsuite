<?php
chdir('../html');
require '../include/queue.inc.php';

sendMailToUser('blaeul@quodata.de', 'Subject', 'Message');