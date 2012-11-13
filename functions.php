<?php
// Requirements are required.
// This is the file containing the regular expressions.
require('regex_file.php');

// This is the main scrape routine.
function scrape() {
	// This loops through each site, and is the first dimension of the regex array.
	for ($site = 0; $site < 2; $site++) { 
		// This gets the HTML from the url stored in with the regular expressions.
		$html = file_get_contents(get_regex($site, 0));

		$days = array(); // ARRAYS!
		preg_match_all(get_regex($site, 1), $html, $days); // Populate ARRAYS!
		$days = $days[0]; // Fix ARRAYS! since preg functions are broken.

		// This iterates over each day.
		foreach ($days as $day) {

			$events = array(); // ARRAYS!
			preg_match_all(get_regex($site, 2), $day, $events); // Populate ARRAYS!
			$events = $events[0]; // Fix ARRAYS!

			// This iterates over each event in a day.
			foreach ($events as $event) {

				$data = array(); // ARRAYS!
				// This loops through each type of data and extracts it from the current event.
				for ($type = 3; $type < 9; $type++) {

					$datum = array(); // ARRAYS!
					$scope = ($type == 3) ? $day : $event; // This handles the fact that dates are stored outside of the event, and only occur once for each day.
					preg_match(get_regex($site, $type), $scope, $datum); // Populate ARRAYS!

					if (count($datum) == 0) { // Case to catch data not available.
						$datum[1] = 'N/A';
					} elseif (count($datum) > 1) { // Case to catch multiple subpatterns.
						// This is the solution to my abstraction problem described in regex_file.php, which just concatenates all subpatterns, so that the regex determines what data is actually extracted, and can omit unwanted characters.
						for ($subpattern = 2; $subpattern < count($datum); $subpattern++) { 
							$datum[1] .= $datum[$subpattern];
						}
					}
					$data = $datum[1] . '<br />'; // This is here to make sure a string is extracted, and also handles the case of exactly 1 subpattern. I know $data is unnecessary right now, but I have plans for using it to hold data before inserting into a database.
				}
				print_r($data); // Just a temporary debug printout so I know this is working.
			}
		}
	}
}

?>