<?php
require('functions.php');
// header("Content-type: text/plain"); // This is just here in case I need to look at the output in plain text.
/*if (!file_exists('test_scraper.log')) {
	echo 'No log found, running setup.<br />';
	first_run();
}*/
scrape();
?>