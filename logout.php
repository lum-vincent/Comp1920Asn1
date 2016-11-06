<?php

/**
	* Assignment 1
	*
	* This file handles user authentication and acts as a welcome screen
	*
	* @author Vincent Lum
	* @date 2016-11-06
	* @version 1.0
	*/

define('LOGGED_IN_COOKIE_NAME','loggedin');
define('LOGGED_IN_TIMESTAMP_COOKIE_NAME','loggedintime');

setcookie(LOGGED_IN_COOKIE_NAME,'',time()-1);
setcookie(LOGGED_IN_TIMESTAMP_COOKIE_NAME,'',time()-1);

header('Location: index.php');

?>
