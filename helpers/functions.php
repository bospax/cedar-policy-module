<?php

date_default_timezone_set('Asia/Manila');

function sanitize($str) {
    $str = strip_tags(trim($str));
    $str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    $str = filter_var($str, FILTER_SANITIZE_STRING);

    return $str;
}

function encode($arr) {
    $arr = json_encode($arr);
    return $arr;
}

function decode($str) {
    $str = json_decode($str, true);
    return $str;
}

function cutFilename($str) {
    $str = substr($str, 0, 10).'.pdf';

    return $str;
}

function cutTitle($str) {
    
    if (strlen($str) > 30) {
        $str = substr($str, 0, 30).'..';
    }

    return $str;
}

function formatDate($old_date) {
    $old_date_timestamp = strtotime($old_date);
    $new_date = date('m/d/Y', $old_date_timestamp);

    return $new_date;
}

?>