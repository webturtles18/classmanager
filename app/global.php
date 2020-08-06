<?php 

/**
 * Constants
 * **/
if(!defined('DS')){
    define('DS', DIRECTORY_SEPARATOR);
}

if(!defined('DB_DATE_FORMAT')){
    define('DB_DATE_FORMAT',"Y-m-d");
}

if(!defined('DB_TIME_FORMAT')){
    define('DB_TIME_FORMAT',"H:i");
}

if(!defined('DB_DATETIME_FORMAT')){
    define('DB_DATETIME_FORMAT', "Y-m-d H:i");
}

if(!defined('DISPLAY_FULL_DATETIME')){
    define('DISPLAY_FULL_DATETIME', "l M d, Y - h:i A");
}

if(!defined('DISPLAY_DATETIME')){
    define('DISPLAY_DATETIME', "M d, Y h:i A");
}

if(!defined('DISPLAY_DATE')){
    define('DISPLAY_DATE', "M d, Y");
}

if(!defined('DATEPICKER_DATETIME')){
    define('DATEPICKER_DATETIME', "m/d/Y h:i A");
}

if(!defined('DATEPICKER_DATE')){
    define('DATEPICKER_DATE', "m/d/Y");
}

if(!defined('LOG_DT')){
    define('LOG_DT', "Ymd-Hi-sv");
}

/**
 * Function print_data
 * This is used for print array
 * **/
if (!function_exists('print_data')) {
    function print_data($data,$break = true)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";

        if($break === true) exit;
    }
}