<?php

require '../include/queue.inc.php';

if (!empty($_POST)) {
    $subject = 'Test-E-Mail';
    $message = 'Der Mailversand war erfolgreich.';
    sendMailToUser($_POST['address'], $subject, $message);
    die('Mailversand wurde versucht - bitte im Posteingang schauen, ob es geklappt hat.');
}
?>
<form method=POST>
    Mail-Adresse: <input type=email name=address><br>
    <input type=submit>    
</form>