<?php
// I started writing this file just to keep track of all the regular expressions that I am using, but then I realized I could use it to abstract all of the patterns, which would reduce the number of long string mucking up my actual code. I have (hopefully) succesfully gotten all of the patterns in here, but due to a number of decisions I have to make about how to abstract them, I haven't actually done it yet. I am terrible at making decisions. So, this is definitely a WIP.


// Here are some of the ways I have though to abstract this:
// First, I was just going to have some arrays in this file, with the regular expressions attached to nice concise-but-descriptive and unique keys, so I could just reference the array I wanted, and the key I wanted. Then, I read some peoples' thoughts on global variables, and decided that this probably wasn't the best approach. (For those of you interested, the thoughts on global variables are here: http://stackoverflow.com/questions/5166087/php-global-in-functions)
// Second, I thought of just creating a function for each array, which would accept a key, and return the right regex. This solves the issues of scope, and provides a nice level of abstraction. The problem with this is that I would need a way to also return the backreferences for subpattern matching, and I havent quite figured out how to do that.
// Third, after writing this file and all the comments, I realized that I could further abstract the process. I could have only a single function, which would accept two parameters, the first being the key to the site specific array, the second being the key to the actual expression needed. This method suffers the same difficulty as the second way.
// Between these three, I would prefer to  implement the third method, but I need to figure out how to get around the backreferencing problem. One inelegant solution would be to just include a second (blank) subpattern in the regexs with only one subpattern, and always concatenate the backreference to $1 and $2.
// Eureka! Just as I finished writing that last sentence about the inelegant solution, I realized that if I implement the strategy outlined below, regarding the use of subpatterns and extracting the data from the matched array, I could just iterate through said array and concatenate all of the subpatterns together, which eliminates the need for backrefs altogether. It sounds so simple now, I can't believe it took me this long to connect the dots. I obviously have yet to implement this, but at least I know now what I am going to do.
	// EDIT: [11-13-2012] I partially implemented this in a terrible form. It will eventually need some fixing.


// Use of subpatterns could eliminate redundancy. Something to look into.
// Looked into somewhat. I put in some notes that describe ways to take advantage of subpatterns.
// Also, after commenting all of these, and adding the reular expressions for Carlson, I realize that I could probably get away with a single regular expression for extracting info. All I would need is the extract ecpression, since it is almost identical, and works as a matcher, and then I can just extract the stuff I need from the subpatterns.


// ---------------------------------- //
// Here is the new, WIP, abstracted version of the regular expression array.
function get_regex($array, $key) {
	$regex = array(
		array(
			'http://www.physics.umn.edu/events/calendar/spa.all/future',
			'@\<div class\="cal_day"\>.*?\<\!\-\- cal_day .*?\-\-\>@s',
			'@\<div class\="cal_item"\>.*?\<\!\-\- cal item \-\-\>@s',
			'@\<div class\="cal_day_title"\>(.*?)\<\/div\>@s',
			'@\<div class\="cal_time"\>(.*?)&nbsp;(.*?)\:\<\/div\>@s',
			'@\<div class\="cal_title"\>.*?in (.*?)&nbsp;(.*?)\<\/div\>@s',
			'@\<div class\="cal_title"\>.*?\<b\>(.*?)\<\/b\>.*?\<\/div\>@s',
			'@\<div class\="cal_subject"\>.*?&nbsp;(.*?)\<\/div\>@s',
			'@\<div class\="cal_speaker"\>.*?&nbsp;(.*?)\<\/div\>@s'
		),
		array(
			'http://www.csom.umn.edu/events/monthly?mode=monthly&startDate='.(new DateTime('now'))->format('m/d/Y'),
			'@\<h2\>.*?\<\/ol\>@s',
			'@\<li\>.*?\<\/li\>@s',
			'@\<h2\>(.*?)\<\/h2\>@s',
			'@\<div class\="event-time"\>(.*?)\<\/div\>@s',
			'@\<p\>.*?Location: (.*?)\<\/p\>@s',
			'@\<h3\>\<a.*?\>(.*?)\<\/a\>\<\/h3\>@s',
			'@\<p\>.*?(Sponsored.*?):(.*?)\<\/p\>@s',
			'@YOU SHALL NOT MATCH@s'
		)
	);
	// The odd call at the end of the second url could be removed, and the two lines below this uncommented, for the same effect.
	// $date = getdate();
	// $regex[1][0] .= $date['mon'].'/'.$date['mday'].'/'.$date['year'];
	return $regex[$array][$key];
}
// ---------------------------------- //



// ---------------------------------- //
// Below is the original form of this file, with lots of comments.
// ---------------------------------- //

/*
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
*/
?>