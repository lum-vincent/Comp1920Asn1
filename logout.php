<?php

define('LOGGED_IN_COOKIE_NAME','loggedin');
define('LOGGED_IN_TIMESTAMP_COOKIE_NAME','loggedintime');

//unset($_COOKIE[LOGGED_IN_COOKIE_NAME]);
//unset($_COOKIE[LOGGED_IN_TIMESTAMP_COOKIE_NAME]);

setcookie(LOGGED_IN_COOKIE_NAME,'',time()-1);
setcookie(LOGGED_IN_TIMESTAMP_COOKIE_NAME,'',time()-1);

header('Location: index.php');

?>
