<!DOCTYPE HTML>
<html>
<head></head>
<body>

<?php

/**
	* Assignment 1
	*
	* This file analyzes the book inventory.
	*
	* @author Vincent Lum
	* @date 2016-11-06
	* @version 1.0
	*/

define('LOGGED_IN_COOKIE_NAME','loggedin');
define('LOGGED_IN_TIMESTAMP_COOKIE_NAME','loggedintime');

define('BOOKS_FILE_PATH','./books.txt');

define('BOOK_FILE_LINE_ELEMENTS_DELIMITER',' ');
define('BOOK_FILE_LINE_ELEMENTS_REGEX','/'.BOOK_FILE_LINE_ELEMENTS_DELIMITER.'+/s');
const BOOK_ELEMENTS_DELIMITER = array('by','published','born');

define('BOOK_FILE_MISSING_DATE_ERROR_FILE','./missing_year.txt');
define('BOOK_FILE_PUBLISHED_DATE_BEFORE_BIRTH_DATE_ERROR_FILE','./published_before_born.txt');

define('SPECIAL_BOOK_PREG_MATCH','/[0-9]{4}-[0-9]{2}-[0-9]{2}/');

define('INDEX_LINK','./index.php');
define('BROWSE_BOOKS_LINK','./browse.php');
define('LOGOUT_LINK','./logout.php');

$booksContents = array();
$specialBooks = array();

//Check if user is logged in, if not redirect to index.php
if( !isset($_COOKIE[LOGGED_IN_COOKIE_NAME]) || !isset($_COOKIE[LOGGED_IN_TIMESTAMP_COOKIE_NAME]) ){
	header('Location: ./index.php');
} else {
	foreach( file(BOOKS_FILE_PATH,FILE_IGNORE_NEW_LINES) as $line) {
		//Break book up into parts
		$bookFileElements = preg_split(BOOK_FILE_LINE_ELEMENTS_REGEX,$line);
    $tempBook = [];

		//Parse through book parts for delimiters and throw parts into the appropriate
		//array index
		foreach(BOOK_ELEMENTS_DELIMITER as $delimiter)	{
			$count = 0;
			$element = '';
			do {
				$nextWord = array_shift($bookFileElements);	
				if($nextWord == $delimiter)
					break;
				$element .= $nextWord . BOOK_FILE_LINE_ELEMENTS_DELIMITER;
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
		} else if (preg_match(SPECIAL_BOOK_PREG_MATCH,$book[0])) {
			array_push($specialBooks,$book);
		} else {
			array_push($booksContents,$book);
		}
	}

ECHO "<ul>
	<li>Which author wrote the most books?</li>
	<li>Which author was the oldest when they published their book?</li>
	<li>What is the longest book title?</li>
	<li>How many books were published before 1950?</li>
	</ul>";

$wroteMostBooks = array('','',0,0);
$oldestAuthor = array('','',0,0);
$longestTitle = '';
$booksWrittenBefore1950 = array();
$authors = array(array('','',0,0));

foreach ($booksContents as $book) {
//	var_dump($book);
	if(strlen($longestTitle) < strlen($book[0])){
		$longestTitle = $book[0];
	}
	if($book[1] < 1950)
		array_push($booksWrittenBefore1950,$book[0]);
	foreach($authors as $key => $author) {
		if ( $book[2] != $author[0] && $book[3] != $author[1]) {
			array_push($authors,array($book[2],$book[3],1,$book[1]-$book[4]));
		} else {
			if ( $book[1]-$book[4] > $author[3])
				$authors[$key][3] = $book[1] - $book[4];
			$authors[$key][2] += 1;
		}
	}
}

foreach($authors as $author) {
	if($author[2] > $wroteMostBooks[2]) 
		$wroteMostBooks = $author;
	if($author[3] > $oldestAuthor[3]) 
		$oldestAuthor = $author;
}

ECHO "
" . $wroteMostBooks[1] . " " . $wroteMostBooks[0] . " wrote the mmost books (" . $wroteMostBooks[2] . " books)<br />
" . $oldestAuthor[1] . " " . $oldestAuthor[0] . " was the oldest author (" . $oldestAuthor[3] . " years old)<br />
\"" . $longestTitle . "\" is the longest title (" . strlen($longestTitle) . " characters)<br />";
if(count($booksWrittenBefore1950) == 1) {
	ECHO "1 book was published before 1950 (" . $booksWrittenBefore1950[0] . ")";
} else {
	ECHO count($booksWrittenBefore1950) . " books were published before 1950 ("; 
	  $size = count($booksWrittenBefore1950) - 1;
		foreach($booksWrittenBefore1950 as $key => $bookTitle) {
			if($key != $size)
				ECHO $bookTitle . ', ';
			else
				ECHO $bookTitle;
		}
	ECHO ")";
}

ECHO "<h3>" . count($specialBooks) . " special books have been identified!</h3> 
	<ol>";
foreach( $specialBooks as $book ) {
	ECHO "<li>" . $book[0] . "</li>";
}
ECHO "</ol>";

ECHO "
		<ul>
			<li><a href='" . INDEX_LINK . "'>Go Back</a></li>
			<li><a href='" . BROWSE_BOOKS_LINK . "'>Browse books in store</a></li>
			<li><a href='" . LOGOUT_LINK . "'>Logout</a></li>
		</ul>
";


}?>

</body>
</html>
