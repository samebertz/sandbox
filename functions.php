<?php

function make_query($array) {
	// print_r($array);
	$columns = '';
	$values = '';
	$i = 1;
	foreach ($array as $key => $value) {
		
		if ($i == 1) {
			$columns .= $key;
			$values .= '\''.$value.'\'';
		} else {
			$columns .= ', '.$key;
			$values .= ', \''.$value.'\'';
		}
		
		// $columns .= ($i == 1) ? $key : ', ' . $key;
		// $values .= ($i == 1) ? $value : ', ' . $value;
		$i++;
	}
	// print_r($columns."\n".$values."\n");
	$query_base_1 = 'INSERT INTO test_table ( ';
	$query_base_2 = ' ) VALUES ( ';
	$query_base_3 = ' )';
	$result = $query_base_1 . $columns . $query_base_2 . $values . $query_base_3;
	// print_r($result."\n");
	return $result;
}

function make_regex_parse($name) {
	$regex_base_1 = '@\<div class\="cal_';
	$regex_base_2 = '"\>(.*?)\<\/div\>@s';
	return $regex_base_1 . $name . $regex_base_2;
}

function make_regex_extract($info) {
	$regex_base_1 = '@\<div class\="cal_';
	$regex_base_2 = '\<\/div\>@s';
	return $regex_base_1 . $info . $regex_base_2;
}

/*function first_run() {
	$create_db = new PDO('mysql:host=localhost','root','rootpassword');
	file_put_contents('scraper.log', 'Attempting to create database ... ' . "\t", FILE_APPEND);
	$create_db->query('CREATE DATABASE IF NOT EXISTS events_db');
	file_put_contents('scraper.log', 'Success' . "\n", FILE_APPEND);
	$create_table = new PDO('mysql:dbname=events_db;host=localhost','root','rootpassword');
	file_put_contents('scraper.log', 'Attempting to create table ... ' . "\t", FILE_APPEND);
	$create_table->query('CREATE TABLE IF NOT EXISTS events_table (date DATE, time TIME, location varchar(255), subject varchar(255), speaker varchar(255))');
	file_put_contents('scraper.log', 'Success' . "\n", FILE_APPEND);
}*/

function first_run() { //test
	$create_db = new PDO('mysql:host=localhost','root','rootpassword');
	file_put_contents('test_scraper.log', 'Attempting to create database ... ' . "\t", FILE_APPEND);
	$create_db->query('CREATE DATABASE IF NOT EXISTS test_db');
	file_put_contents('test_scraper.log', 'Success' . "\n", FILE_APPEND);
	$create_table = new PDO('mysql:dbname=test_db;host=localhost','root','rootpassword');
	file_put_contents('test_scraper.log', 'Attempting to create table ... ' . "\t", FILE_APPEND);
	$create_table->query('CREATE TABLE IF NOT EXISTS test_table (date DATE, time TIME, location varchar(255), title varchar(255), subject varchar(255), speaker varchar(255))');
	file_put_contents('test_scraper.log', 'Success' . "\n", FILE_APPEND);
	$log = file_get_contents('test_scraper.log');
	print_r(nl2br($log));
}

function scrape($loc) { //test
	$html = file_get_contents($loc);
	$regex_days = '@\<div class\="cal_day"\>.*?\<\!\-\- cal_day .*?\-\-\>@s';
	$days = array();
	preg_match_all($regex_days, $html, $days);
	$days = $days[0];
	$db = new PDO('mysql:dbname=test_db;host=localhost','root','rootpassword');
	$table = 'test_table';
	$db->query('DROP TABLE IF EXISTS test_table');
	$db->query('CREATE TABLE IF NOT EXISTS test_table (date DATE, time TIME, location varchar(255), title varchar(255), subject varchar(255), speaker varchar(255))');

	foreach ($days as $day) {
		$date = populate(/*$db, $table, */$day, make_regex_parse('day_title'), make_regex_extract('day_title"\>(.*?)()'), '$1', '$2', 'date');
		$regex_items = '@\<div class\="cal_item"\>.*?\<\!\-\- cal item \-\-\>@s';
		$items = array();
		preg_match_all($regex_items, $day, $items);
		$items = $items[0];
		foreach ($items as $item) {
			$time = populate(/*$db, $table, */$item, make_regex_parse('time'), make_regex_extract('time"\>(.*?)&nbsp;(.*?)\:'), '$1', '$2', 'time');
			$location = populate(/*$db, $table, */$item, make_regex_parse('title'), make_regex_extract('title"\>(.*?)in (.*?)&nbsp;(.*?)'), '$2', '$3', 'location');
			$title = populate(/*$db, $table, */$item, make_regex_parse('title'), make_regex_extract('title"\>(.*?)\<b\>(.*?)()\<\/b\>(.*?)'), '$2', '$3', 'title');
			$subject = populate(/*$db, $table, */$item, make_regex_parse('subject'), make_regex_extract('subject"\>(.*?)&nbsp;(.*?)()'), '$2', '$3', 'subject');
			$speaker = populate(/*$db, $table, */$item, make_regex_parse('speaker'), make_regex_extract('speaker"\>(.*?)&nbsp;(.*?)()'), '$2', '$3', 'speaker');
			$insert = $db->prepare(make_query(array('date' => $date, 'time' => $time, 'location' => $location, 'title' => $title, 'subject' => $subject, 'speaker' => $speaker)));
			$insert->execute();
			$insert->closeCursor();
		}
	}
	$query = 'SELECT * FROM test_table';
	foreach ($db->query($query) as $row) {
		print $row['date'] . "\t";
		print $row['time'] . "\t";
		print $row['location'] . "\t";
		print $row['title'] . "\t";
		print $row['subject'] . "\t";
		print $row['speaker'] . "\n";
	}
}

function populate(/*$db, $table, */$item, $regex_parse, $regex_extract, $b1, $b2, $col) {
	$array = array();
	preg_match($regex_parse, $item, $array);
	// print_r($array);
	$array = (count($array) == 0) ? 'N/A' : $array[0];
	$source = preg_replace($regex_extract, $b1 . $b2, $array, 1);
	if ($col == 'date') {
		$temp = new DateTime($source);
		$data = $temp->format('Y-m-d');
	} elseif ($col == 'time') {
		$temp = new DateTime($source);
		$data = $temp->format('H:i');
	} else {
		$data = $source;
	}
	return $data;
	// $query = $db->prepare(make_query($col));
	// $query->execute(array($data));
	// $query->closeCursor();
}

/*// $sqlserver = new mysqli();
// $sqlserver->mysqli_connect('localhost', 'root', 'rootpassword');
$dsnc = 'mysql:host=localhost';
$user = 'root';
$pass = 'rootpassword';
$create = new PDO($dsnc, $user, $pass);
$statementc = $create->query('CREATE DATABASE IF NOT EXISTS testdb');
$statementc->closeCursor();
$dsn = 'mysql:dbname=testdb;host=localhost';
$db = new PDO($dsn, $user, $pass);
$statement = $db->query('CREATE TABLE IF NOT EXISTS testtable ( col_1 int, col_2 text )');
$statement = $db->query('INSERT INTO testtable VALUES (5, "big week")');
$statement = $db->query('SELECT * FROM testtable');
$obj = $statement->fetchAll();
print_r($obj);



// $html = file_get_contents("http://samebertz.com/umn-cal.html");
$html = file_get_contents("\\xampp\\htdocs\\test.html");
// $regexp = '@\<div\ class\=\"cal\_day\"\>.*?\<\!\-\-\ cal\_day\ \(unless\)\ \-\-\>@s';
$regexp0 = '~';
$regexp1 = '\<div class\=\"cal\_day\"\>';
$regexp2 = '.*?';
$regexp3 = '\<\!\-\- cal\_day \(unless\) \-\-\>';
$regexp_ = '~s';
$regexp = $regexp0 . $regexp1 . $regexp2 . $regexp3 . $regexp_;
$days = array();
$events = array();
$items = array();
preg_match_all($regexp, $html, $days);
$days = $days[0];
// print_r($days);
foreach ($days as $day) {
	// echo $day;
	preg_match_all('~\<div class\=\"cal\_item\"\>.*?\<\!\-\- cal item \-\-\>~s', $day, $events);
	$events = $events[0];
	print_r($events);
	foreach ($events as $event) {
		# code...
	}
	foreach ($events as $event) {
		# code...
	}
	foreach ($events as $event) {
		# code...
	}
}




// $html_dom = new DOMDocument();
// $html_dom->loadHTML($html);
// $array = array();
// foreach ($html_dom->getElementsByTagName('*') as $element) {
// 	$array[] = $element->textContent;
// }
// print_r($array);

/*function getLinksFromText($raw) {
	$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

	$matches = array();
	preg_match_all($reg_exUrl, $raw, $matches);
	return $matches[0];
}
function get_img_src_from_html($html) {
    $regexp = '#<\s*img [^\>]*src\s*=\s*(["\'])(.*?)\1#im';
    $links  = array();
    if (preg_match_all($regexp, $html, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $link = $match[2];
            $valid = validate_link($link);
            if ( $valid )
            array_push($links, $valid);
        }
    }
    return $links;
}*/





/*	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri = 'https://';
	} else {
		$uri = 'http://';
	}
	$uri .= $_SERVER['HTTP_HOST'];
	header('Location: '.$uri.'/xampp/');
	exit;*/
?>