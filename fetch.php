<?php

    set_time_limit(0);
    
    require("functions.php");
    
    if ( isset($_POST["submit"]) && $_POST["submit"] ) {
        $url = $_POST["url"];
        $html = getWithCurl($url);

        $urls = (get_img_src_from_html($html));
        pre_fresh();
        ob_start();
        foreach ($urls as $url) {
            ob_flush(); flush();
            if ( is_duplicate($url) == false ) {
            
                $img = getWithCurl($url);
                
                if ( isNot404($img) ) {
                    $filename = saveImage($url, $img);
                    
                    show($url." was downloaded and saved as <strong>" . $filename . "</strong>");
                } else {
                
                    show($url . " may be a PNG since the initial download failed. Attepting...");
                
                    $pngUrl = str_replace(".jpg", ".png", $url);
                    
                    $img2 = getWithCurl($pngUrl);
                    
                    if ( isNot404($img) ) {
                      $filename = saveImage($pngUrl, $img2);
                    show($pngUrl." was downloaded as a PNG and saved as <strong>" . $filename . "</strong>");
                    } else {
                      show($pngUrl . " failed to download as either JPG or PNG.");
                    }

                }
            } else {
                show($url.": was a duplicate.");
            }
            ob_flush(); flush();
        }
        show('Finished. <a href="index.html">Do it again.</a>');
        ob_end_flush(); flush();
    } else {
        show("No, you need a URL.");
    }
    
?>