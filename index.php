<?php
require('functions.php');
header("Content-type: text/plain");
if (!file_exists('test_scraper.log')) {
	echo 'No log found, running setup.<br />';
	first_run();
}
scrape('http://www.physics.umn.edu/events/calendar/spa.all/future');
?>