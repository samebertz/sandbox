<?php
// I uncomment this line when I need to read things. BEWARE... of reading... things...
// header("Content-type: text/plain");


// This is just to get the number of sales, to form the query string.
$html = file_get_contents('http://store.steampowered.com/'); // Get the html to extract the number of sales.
$get_num = array(); // ARRAYS!
preg_match('@id\="tab_Discounts_count_end"\>[0-9]*?\<\/span\> of (\d*).*?\<\/div\>@s', $html, $get_num); // Find the number of sales with this stupidly long regex. Probably could be shortened, but I'm LAZY.
$get_num = $get_num[1]; // Get the subpattern with the number of sales.

// This is the real url to parse for the sales, with the total number appended on the end. It would possibly work with a start of 1, but I haven't testet that. I'm LAZY.
$url = 'http://store.steampowered.com/search/tab?bHoverEnabled=true&cc=US&l=english&style=&navcontext=1_4_4_&tab=Discounts&start=0&count=';
$html = file_get_contents($url . $get_num);


// This is the surprisingly small piece of code that actually parses the html for the names of the games and DLC that are on sale.
$regex = '@\<h4>(.*?)\<\/h4\>@s'; // This regex has a subpattern where the name of the game is nestled in h4 tags inside of each sale div.
// Luckily, the only h4 tags are around those titles, so I didn't have to iterate through each sale. Woo!
$array = array(); // ARRAYS!
preg_match_all($regex, $html, $array); // Match those patterns!
$array = $array[1]; // Oh yeah! Extracting subpatterns!

// Aaaaaand, print it out so I can see that it worked.
foreach ($array as $title) {
	print_r($title.'<br />'); 
}

// Weird issues with the tm, works on race stars but not hitman.
// I recall looking into it, and I think it is just based on the publisher's naming.
// F1 RACE STARS™
// Hitman: Absolution™



// ---------------------------------------- //
// Below this is some other crap I wrote,   //
// It probably didn't work, which is why    //
// it is all commented out. Good luck       //
// deciphering it, I probably won't comment //
// it, since I can't even remember what I   //
// was trying to do.                        //
// ---------------------------------------- //

// print_r($html);
// print_r($array);
// $regex = '@\<div id\="tab_discounts_content"(.*?)\<\/div\>@s';
/*
$str_1 = '<div id="tab_discounts_content" style="display: none;">';
$str_2 = '<div class="tab_page" style="height: 780px;">';
$str_3 = '<div id="tab_Discounts_items" class="v5 ">';
$str_1 = preg_quote($str_1);
$str_2 = preg_quote($str_2);
$str_3 = preg_quote($str_3);
$str_a = '@'.$str_1.'\s+'.$str_2.'\s+'.$str_3;
$str_b = '\<\/div\>\s+\<\/div\>\s+\<\/div\>@s';
$str = $str_a.'(.*?)'.$str_b;
$array = array();
preg_match_all($str, $html, $array);
print_r($array[0])
*/

// print_r($html);
// print_r('<br />');

// $regex = '@\<div class\="tab_row(.*?)\<\/div\>@s';
// $regex_name = '@\<h4\>(.*?)\<\/h4\>@s';

// $array = array();
// $names = array();

// preg_match_all($regex, $html, $array);
// print_r($array);
// print_r('<br />');
// $array = $array[0];
// print_r($array);
// print_r('<br />');

// foreach ($array as $row) {
// 	print_r($row);
// 	preg_match($regex_name, $row, $names);
// 	// $name = $names[0];
// 	print_r($names);
// 	print_r('<br />');
// }


/*<div class="tab_row even " onmouseover="GameHover( this, event, $('global_hover'), {&quot;type&quot;:&quot;app&quot;,&quot;id&quot;:203140} );" onmouseout="HideGameHover( this, event, $('global_hover') )" id="tab_row_Discounts_203140">
		<div class="tab_overlay">
			<a href="http://store.steampowered.com/app/203140/?snr=1_4_4__106">
				<img src="http://cdn.store.steampowered.com/public/images/blank.gif">
			</a>
		</div>
		<div class="tab_item_img">
			<img src="http://cdn.store.steampowered.com/public/images/blank.gif" id="delayedimage_home_tabs_20"" class="tiny_cap_img" alt="Hitman: Absolution™">
		</div>
		<div class="tab_desc with_discount">
			<h4>Hitman: Absolution™</h4>
			<div class="genre_release"><img class="platform_img" src="http://cdn.store.steampowered.com/public/images/v5/platforms/platform_win.png" width="22" height="22">Action - Available: Nov 20, 2012</div>
					</div>

					<div class="tab_discount discount_pct">
				-10%
			</div>
		
		<div class="tab_price">
												<span style="color: #626366 ;"><strike>&#36;49.99</strike></span><br>&#36;44.99									</div>
		<br clear="all">
	</div>
*/

?>