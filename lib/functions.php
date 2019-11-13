<?php

function isallowExt($filename, $list) {
    $ext = substr($filename, strrpos($filename, '.') + 1);
    foreach($list as $ex) {
        if($ext === $ex){
            return true;
        }
    }
    return false;
}

function updateRandomString($str){
    return uniqid("DetaShare_").hash('sha256', $str);
}

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>