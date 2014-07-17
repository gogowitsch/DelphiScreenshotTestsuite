<?php

#die(print_r($_SERVER,true));
$lang = $_SERVER['SERVER_NAME']=='screenshot01-pc' ? 'de' : 'en';
define('LANG', $lang); //Sprache ändern 'en' or 'de'
