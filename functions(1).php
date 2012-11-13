<?php

function saveImage($url, $img) {
  $filename = sha1($url) . ".jpg";
  $path = "images/" . $filename;
  file_put_contents($path, $img);
  add_duplicate_log($url);
  return $filename;
}

function isNot404($img) {
  return (contains($img, "What a Terrible Failure") == false);
}

function show($msg) {
    echo('<div>' . $msg . '<br /></div>');
}

function pre_fresh() {
    $str = "1 2 3 4 5 6 7 8 9 0 a b c d e f";
    $p = explode(" ", $str);
    foreach ($p as $i) {
        $path = "logs/" . $i . ".txt";
        if ( !file_exists($path) ) {
            touch($path);
            file_append_contents($path, $path."\n");
        }
    }
}

function is_duplicate($url) {
    $sha1 = sha1($url);
    $begin = substr($sha1, 0, 1);
    $path = "logs/" . $begin . ".txt";
    $contents = file_read($path);
    if ( $contents == false ) return true;
    $parts = explode("\n", $contents);
    foreach ($parts as $unique) {
        if ( $unique == $sha1 ) return true;
    }
    return false;
}

function add_duplicate_log($url) {
    $sha1 = sha1($url);
    $begin = substr($sha1, 0, 1);
    $path = "logs/" . $begin . ".txt";
    $k = file_append_contents($path, $sha1 . "\n");
}

function file_append_contents($filename, $contents) {
    if (!file_exists($filename)) {
        return false;
    }
    $h = fopen($filename, "a");
    fwrite($h, $contents);
    fclose($h);
    return true;
}

function file_overwrite($filename, $contents) {
    if (!file_exists($filename)) {
        return false;
    }
    $h = fopen($filename, "w");
    fwrite($h, $contents);
    fclose($h);
    return true;
}

function file_read($filename) {
    if (!file_exists($filename)) {
        return false;
    }
    $h = fopen($filename, "r");
    $c = fread($h, filesize($filename));
    fclose($h);
    return $c;
}

function contains($haystack, $needle) {
    return (stripos($haystack, $needle) === false ? false : true);
}

function validate_link($link) {
    $link = strtolower($link);
    if ( contains($link, "4walled.org/thumb/") == false ) {
        return false;
    }
    $parts = explode("thumb/", $link);
    $code = $parts[1];
    return "http://4walled.org/src/" . $code;
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
}

function getWithCurl($url)
{
    $curl = curl_init();
 
    // Setup headers - I used the same headers from Firefox version 2.0.0.6
    // below was split up because php.net said the line was too long. :/
    $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
    $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
    $header[] = "Cache-Control: max-age=0";
    $header[] = "Connection: keep-alive";
    $header[] = "Keep-Alive: 300";
    $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
    $header[] = "Accept-Language: en-us,en;q=0.5";
    $header[] = "Pragma: ";
    // browsers keep this blank.
 
    $referer = "http://4walled.org/";
 
    $browsers = array("Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.3) Gecko/2008092510 Ubuntu/8.04 (hardy) Firefox/3.0.3", "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20060918 Firefox/2.0", "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3", "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.0.04506)");
    $choice2 = array_rand($browsers);
    $browser = $browsers[$choice2];
 
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, $browser);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_REFERER, $referer);
    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 7);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
 
    $data = curl_exec($curl);
 
    if ($data === false) {
        $data = curl_error($curl);
    }
 
    // execute the curl command
    curl_close($curl);
    // close the connection
 
    return $data;
    // and finally, return $html
}

?>