<?php

$lang = preg_match('/screenshot[0-9]+-pc/i', gethostname()) ? 'de' : 'en';

if (!empty($_GET['l'])) $lang = $_GET['l'];

define('LANG', $lang); //Sprache ändern 'en' or 'de'
