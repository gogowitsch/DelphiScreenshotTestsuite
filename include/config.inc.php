<?php

$lang = preg_match('/screenshot[0-9]+-pc/i', gethostname()) ? 'de' : 'en';

if (!empty($_GET['l'])) $lang = $_GET['l'];
if (!empty($_GET['lang'])) $lang = $_GET['lang'];

define('LANG', $lang); //Sprache ändern 'en' or 'de'
