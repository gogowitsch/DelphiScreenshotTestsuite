<?php

/* Dateizweck:
 *
 * Projekten können permanente Abonnenten zugewiesen werden die bei
 * jedem job_done=1 informiert werden.
 */

/** @return array */
function getSubscribers($project) {
    global $conn;

    $project = $conn->quote($project);
    return db_connect("SELECT * FROM subscribers WHERE $project LIKE project");
}

function stripDomainIfQuoData($sEmailAddress) {
    return str_replace('@quodata.de', '', $sEmailAddress);
}
