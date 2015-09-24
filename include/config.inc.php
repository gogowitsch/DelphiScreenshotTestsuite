<?php

$lang = $_SERVER['SERVER_NAME']=='screenshot01-pc' ? 'de' : 'en';

if (!empty($_GET['l'])) $lang = $_GET['l'];
if (!empty($_GET['lang'])) $lang = $_GET['lang'];

define('LANG', $lang); //Sprache ändern 'en' or 'de'
