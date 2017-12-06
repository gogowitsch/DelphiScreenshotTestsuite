<?php

$iRev = 0;

$arrSQLInstallQuery[++$iRev] = "
CREATE TABLE IF NOT EXISTS `subscribers` (
    `project` VARCHAR(255),
    `email` VARCHAR(255),
    `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY);
    UPDATE `config` SET var_val = '$iRev' WHERE var_name = 'db_revision';";
assert($iRev === 1);
