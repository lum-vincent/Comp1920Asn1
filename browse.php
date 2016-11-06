<?php
define('LOGGED_IN_COOKIE_NAME','loggedin');
define('LOGGED_IN_TIMESTAMP_COOKIE_NAME','loggedintime');

if( !isset($_COOKIE[LOGGED_IN_COOKIE_NAME]) || !isset($_COOKIE[LOGGED_IN_TIME_COOKIE_NAME])){
	header('Location: ./index.php');


?>
