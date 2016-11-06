<?php
define('LOGGED_IN_COOKIE_NAME','loggedin');
define('LOGGED_IN_TIMESTAMP_COOKIE_NAME','loggedintime');

define('BOOKS_FILE_PATH','./books.txt');

define('BOOK_FILE_LINE_ELEMENTS_DELIMITER','/ +/s');
const BOOK_FILE_ELEMENTS_DELIMITER = array('by','published','born');

define('BOOK_FILE_MISSING_DATE_ERROR_FILE','./missing_year.txt');
define('BOOK_FILE_PUBLISHED_DATE_BEFORE_BIRTH_DATE_ERROR_FILE','./published_before_born.txt');

$booksContents = array( 
	array(
		('<strong>Title</strong>'),
		('<strong>Published</strong>'),
		('<strong>Last Name</strong>'),
		('<strong>First Name</strong>'),
		('<strong>Birth Year</strong>')
	)
);

if( !isset($_COOKIE[LOGGED_IN_COOKIE_NAME]) || !isset($_COOKIE[LOGGED_IN_TIMESTAMP_COOKIE_NAME]) ){
	header('Location: ./index.php');
} else {
	ECHO 'browse';
	foreach( file(BOOKS_FILE_PATH,FILE_IGNORE_NEW_LINES) as $line) {
		$bookFileElements = preg_split(BOOK_FILE_LINE_ELEMENTS_DELIMITER,$line);
    $tempBook = [];

		foreach(BOOK_FILE_ELEMENTS_DELIMITER as $delimiter)	{
			$count = 0;
			$element = '';
			do {
				$nextWord = array_shift($bookFileElements);	
				if($nextWord == $delimiter)
					break;
				$element .= $nextWord . ' ';
				$count += 1;
			} while($count < 100 && !empty($bookFileElements));
			$tempBook[] = trim($element);
		}
		$tempBook[] = trim(array_shift($bookFileElements) . ' '); //add last eleement
		$book = array( //reconstruct array
			$tempBook[0],
			$tempBook[2],
			preg_split('/ /',$tempBook[1])[1],
			preg_split('/ /',$tempBook[1])[0],
			$tempBook[3]
			);
		if ( $book[1] == '' || $book[4] == '' ) { //No publishing date or birth date
			$fp = fopen(BOOK_FILE_MISSING_DATE_ERROR_FILE,'a+');
			fwrite($fp,$line.PHP_EOL);
			fclose($fp);
		} else if ( $book[1] < $book[4] ) { //published date before birth date
	    $fp = fopen(BOOK_FILE_PUBLISHED_DATE_BEFORE_BIRTH_DATE_ERROR_FILE,'a+');
			fwrite($fp,$line.PHP_EOL);
			fclose($fp);
		} else {
			array_push($booksContents,$book);
		}
	}


	ECHO '<table>'; //Start table
		foreach($booksContents as $line) {
			ECHO '<tr>'; //Start next row
			foreach($line as $element){
				echo '<td>'.$element.'</td>';
			}
			ECHO '</tr>'; //End row
		}
	ECHO '</table>'; //End table
}

?>
