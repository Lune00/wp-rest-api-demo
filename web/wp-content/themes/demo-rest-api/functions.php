<?php

/**
 * Helper debug
 */
if (!function_exists('write_log')) {

    function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }

}

function dump($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die;
}

foreach (glob(dirname(__FILE__) . "/functions/post-types/*.php") as $filename) {
    require_once $filename;
}

foreach (glob(dirname(__FILE__) . "/functions/*.php") as $filename) {
    require_once $filename;
}

foreach (glob(dirname(__FILE__) . "/functions/gravity-forms-programatic/*.php") as $filename) {
    require_once $filename;
}


