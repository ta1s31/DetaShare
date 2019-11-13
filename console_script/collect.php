<?php 

    $filename = 'useragent.txt';
    
    $line = file($filename);
    foreach($line as $ua){
        preg_match_all('/Mac OS/', $ua, $match);
        print_r($match);
    }


?>