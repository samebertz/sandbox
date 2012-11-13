<?php
// Use of subpatterns could eliminate redundancy. Something to look into.


$regex = array(
'physics' => array(

	'url' => 'http://www.physics.umn.edu/events/calendar/spa.all/future',

	// These two are to iterate by day and by event, and use
	// the comments as the end of the regex, which is nice.
	'select_day' => '@\<div class\="cal_day"\>.*?\<\!\-\- cal_day .*?\-\-\>@s',
	'select_item' => '@\<div class\="cal_item"\>.*?\<\!\-\- cal item \-\-\>@s',
	//

	'get_div_date' => '@\<div class\="cal_day_title"\>.*?\<\/div\>@s',
	'extract_date' => '@\<div class\="cal_day_title"\>(.*?)\<\/div\>@s',
		// Backref to '$1' in preg_replace, or can use
		// preg_match($pattern, $subject, $array);
		// $array = $array[1];
		// instead of the extract regex, since
		// the subpattern is what we want in this case.
	'get_div_time' => '@\<div class\="cal_time"\>.*?\<\/div\>@s',
	'extract_time' => '@\<div class\="cal_time"\>(.*?)&nbsp;(.*?)\:\<\/div\>@s',
		// Backref to '$1', '$2' in preg_replace,
		// to get around the stupid '&nbsp;' character,
		// or can just use the extract regex in preg_match,
		// and append $array[1] and $array[2].
	'get_div_location' => '@\<div class\="cal_title"\>.*?\<\/div\>@s',
	'extract_location' => '@\<div class\="cal_title"\>.*?in (.*?)&nbsp;(.*?)\<\/div\>@s',
		// Backref to '$1', '$2' in preg_replace,
		// since location is combined with title,
		// or see NOTE 1.
// -->  // Also, it is possible to fix the space issue,
		// by stealing the space after 'in', and
		// reversing the order of the subpatterns.
	'get_div_title' => '@\<div class\="cal_title"\>.*?\<\/div\>@s',
	'extract_title' => '@\<div class\="cal_title"\>.*?\<b\>(.*?)\<\/b\>.*?\<\/div\>@s',
		// Backref to '$1' in preg_replace,
		// since the title div has extra crap in it,
		// or see NOTE 1.
	'get_div_subject' => '@\<div class\="cal_subject"\>.*?\<\/div\>@s',
	'extract_subject' => '@\<div class\="cal_subject"\>.*?&nbsp;(.*?)\<\/div\>@s',
		// Backref to '$1' in preg_replace,
		// or can just use the extract regex in preg_match,
		// and take the subpattern from $array[1].
	'get_div_speaker' => '@\<div class\="cal_speaker"\>.*?\<\/div\>@s',
	'extract_speaker' => '@\<div class\="cal_speaker"\>.*?&nbsp;(.*?)\<\/div\>@s'
		// Backref to '$1' in preg_replace,
		// or can just use the extract regex in preg_match,
		// and take the subpattern from $array[1].


	// NOTE 1:
	// Since the location and title are contained in the same div,
	// you can use a single extract regex in preg_match,
	// and select the appropriate subpatterns for each value.
	// Like this:
	// $pattern = '@\<div class\="cal_title"\>.*?\<b\>(.*?)\<\/b\>.*?in (.*?)&nbsp;(.*?)\<\/div\>@s';
	// preg_match($pattern, $subject, $array);
	// $title = $array[1];
	// $location = $array[2] . $array[3];

),

'csom' => array(

	'url' => 'http://www.csom.umn.edu/events/monthly?mode=monthly&startDate=',
	// Must append start date to URL, can use
	// $date = getdate();
	// $csom['url'] .= $date['mon'].'/'.$date['mday'].'/'.$date['year'];

	// The day and event iterators.
	'select_day' => '@\<h2\>.*?\<\/ol\>@s',
	'select_item' => '@\<li\>.*?\<\/li\>@s',
	// It is possible, as mentioned below,
	// to use a more efficient method for
	// extracting the date, by using a subpattern
	// in the day iterator, like this:
	// $regex = '@\<h2\>(.*?)\<\/h2\>.*?\<\/ol\>@s';
	// Then, preg_match_all to iterate by day,
	// and for each day, extract the date info
	// from the subpattern in $array[1].

	'get_div_date' => '@\<h2\>.*?\<\/h2\>@s',
	'extract_date' => '@\<h2\>(.*?)\<\/h2\>@s',
		// Backref to $1 in preg_replace,
		// or can just use the extract regex in preg_match
		// and take the subpattern from $array[1].
// -->	// These are provided for ease of abstraction.
		// However, due to the format of this calendar,
		// it is possible to extract the date from
		// the first subpattern of the day selector regex.
		// This could reduce redundant pattern matching.
	'get_div_time' => '@\<div class\="event_time"\>.*?\<\/div\>@s',
	'extract_time' => '@\<div class\="event_time"\>(.*?)\<\/div\>@s',
		// Backref to $1 in preg_replace,
		// or can just use the extract regex in preg_match
		// and take the subpattern from $array[1].
	'get_div_location' => '@\<p\>.*?Location:.*?\<\/p\>@s',
	'extract_location' => '@\<p\>.*?Location: (.*?)\<\/p\>@s',
		// Backref to $1 in preg_replace,
		// or can just use the extract regex in preg_match,
		// and take the subpattern from $array[1].
	'get_div_title' => '@\<h3\>\<a.*?\>.*?\<\/a\>\<\/h3\>@s',
	'extract_title' => '@\<h3\>\<a.*?\>(.*?)\<\/a\>\<\/h3\>@s',
		// Backref to $1 in preg_replace,
		// or can just use the extract regex in preg_match,
		// and take the subpattern from $array[1].
	'get_div_subject' => '@\<p\>.*?\<\/p\>.*?\<p\>.*?Sponsored.*?\<\/p\>@s',
	'extract_subject' => '@\<p\>.*?(Sponsored.*?):(.*?)\<\/p\>@s',
		// Backref to $1 in preg_replace,
		// or can just use the extract regex in preg_match,
		// and take the subpattern from $array[1].
	'get_div_speaker' => '@@s',
	'extract_speaker' => '@@s'
		// Carlson's events do not list a speaker,
		// but these are provided for ease of abstraction.
		// Hopefully they dont do anything!

)
);
print_r($regex);
?>