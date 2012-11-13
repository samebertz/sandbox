<?php
$test = ':';
preg_quote($test);
print_r($test);

/*header("Content-type: text/plain");
$html = file_get_contents('http://store.steampowered.com/');
print_r($html);*/


/*$html = file_get_contents('http://www.csom.umn.edu/events/#mode=monthly&startDate=11/5/2012');
print_r($html);*/


/*$date = getdate();
print_r($date['weekday'].', '.$date['month'].' '.$date['mday'].' '.$date['year']);
echo '<br />';
print_r($date['mon'].'/'.$date['mday'].'/'.$date['year']);*/


/*header("Content-type: text/plain");
$string = 'Monday, November 5th 2012';
print_r(preg_quote($string) . '<br />');
$string = '<div class="cal_time">08:00&nbsp;am:</div>';
print_r(preg_quote($string)) ;*/
?>