<?php

$lang = preg_match('/screenshot[0-9]+-pc/i', $_SERVER['SERVER_NAME']) ? 'de' : 'en';

if (!empty($_GET['l'])) $lang = $_GET['l'];
if (!empty($_GET['lang'])) $lang = $_GET['lang'];

define('LANG', $lang); //Sprache ändern 'en' or 'de'
