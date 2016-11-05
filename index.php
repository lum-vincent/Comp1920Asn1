<html>
	<head></head>
	<body>
<?php

define('PASSWORD_FILE_PATH','./passwords.txt');
define('PASSWORD_FILE_DELIMITER',',');
define('INVALID_LOGIN_FILE_PATH','./invalid_logins.txt');

define('USERNAME_INPUT_FIELD_NAME','username');
define('PASSWORD_INPUT_FIELD_NAME','password');
define('REMEMBER_ME_CHECKBOX_FIELD_NAME','remember');
define('REMEMBER_ME_CHECKBOX_FIELD_VALUE','remember');

define('USERNAME_FILTER_PREG','/<|>/');

define('LOGGED_IN_COOKIE_NAME','loggedin');
define('LOGGED_IN_REMEMBER_ME_COOKIE_DURATION',20*60);
define('LOGGED_IN_TIMESTAMP_COOKIE_NAME','loggedintime');

define('BROWSE_BOOKS_LINK','./browse.php');
define('ANALYTICS_LINK','');
define('LOGOUT_LINK','./logout.php');

function getPost($String) {
	if(isset($_POST[$String])){
		return trim($_POST[$String]); //should use htmlentities to recode html tags but that would defeat the < > check =/
	} else {
		return NULL;
	}
}

if( !isset($_COOKIE[LOGGED_IN_TIMESTAMP_COOKIE_NAME]) ) { //There is no timestamp cookie. User session is closed.
	$found = false;

	if( $_SERVER['REQUEST_METHOD'] == 'POST') { //If index.php was posted to validate username, password, and remember me.
		$username = getPost(USERNAME_INPUT_FIELD_NAME);
		$password = getPost(PASSWORD_INPUT_FIELD_NAME);
		$rememberMe = getPost(REMEMBER_ME_CHECKBOX_FIELD_NAME);

		if ( preg_match(USERNAME_FILTER_PREG,$username)) {
			ECHO('<strong>Do Not Log In</strong>');
		} else {

			foreach(file(PASSWORD_FILE_PATH) as $line) {
				$line_contents = preg_split('/' . PASSWORD_FILE_DELIMITER . '/',$line);
				if(trim($line_contents[0]) == $username) {
					if(trim($line_contents[1]) == $password) {
						$found = true;
					} 
				}
			}

			if($found) {
				if($rememberMe == REMEMBER_ME_CHECKBOX_FIELD_VALUE) {
					setCookie(LOGGED_IN_COOKIE_NAME,$username,time() + LOGGED_IN_REMEMBER_ME_COOKIE_DURATION);
				} else {
					setCookie(LOGGED_IN_COOKIE_NAME,$username);
				}
				setCookie(LOGGED_IN_TIMESTAMP_COOKIE_NAME,time());
				header("Location: ./index.php");
			} else {
				$invalid_logins_fp = fopen('invalid_logins.txt','a');
				fwrite($invalid_logins_fp,$username.','.$password.','.$_SERVER['REMOTE_ADDR'].PHP_EOL);
				fclose($invalid_logins_fp);

				if(isset($_COOKIE[LOGGED_IN_COOKIE_NAME]))
					setcookie(LOGGED_IN_COOKIE_NAME,'',time()-1);
				if(isset($_COOKIE[LOGGED_IN_TIMESTAMP_COOKIE_NAME]))
					setcookie(LOGGED_IN_TIMESTAMP_COOKIE_NAME,'',time()-1);
				ECHO '<strong>Invalid login! Details have been logged.</strong><br />';
			}
		}	
	}
	if(!$found) {
		ECHO " 
		<form name='loginwindow' action='index.php' method='post'>																
			<fieldset class='loginwindow'>
			<label>Username</label>
				<input type=text name='" . USERNAME_INPUT_FIELD_NAME . "' ";
		if(isset($_COOKIE[LOGGED_IN_COOKIE_NAME]))
			ECHO " value=" . $_COOKIE[LOGGED_IN_COOKIE_NAME];
		ECHO "	
				>
				<br>
				<label>Password</label>
				<input type=password name='" . PASSWORD_INPUT_FIELD_NAME . "'>
				<br>
				<label>Remember me</label>
				<input type=checkbox name='" . REMEMBER_ME_CHECKBOX_FIELD_NAME . "' value='" . REMEMBER_ME_CHECKBOX_FIELD_VALUE . "' ";
		if(isset($_COOKIE[LOGGED_IN_COOKIE_NAME]))
			ECHO "checked = 'checked'";
		ECHO "
				>
				<br>
				<input type=submit value='login'>
			</fieldset>
		</form>
		";
	}
} else {  //There is a timestamp cookie. User session is open.
	ECHO "
		<p>Welcome " . $_COOKIE[LOGGED_IN_COOKIE_NAME] . "!</p>
		<p>You have been logged in for " . (time() - $_COOKIE[LOGGED_IN_TIMESTAMP_COOKIE_NAME]) . " seconds.</p>
		<ul>
			<li><a href='" . BROWSE_BOOKS_LINK . "'>Browse books in store</a></li>
			<li><a href='" . ANALYTICS_LINK . "'>Analytics</a></li>
			<li><a href='" . LOGOUT_LINK . "'>Logout</a></li>
		</ul>
		";
}

?>


	</body>
</html>
