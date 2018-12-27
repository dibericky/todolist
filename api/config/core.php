<?php
// show error reporting
error_reporting(E_ALL);
 
// set your default time-zone
date_default_timezone_set('Asia/Manila');
 
// variables used for jwt
$root = $_SERVER['DOCUMENT_ROOT'];
$backend = $root.'api';
$lib = $_SERVER['DOCUMENT_ROOT'].'vendor/';
?>