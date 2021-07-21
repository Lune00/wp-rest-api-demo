<?php
foreach (glob(dirname(__FILE__) . "/functions/post-types/*.php") as $filename) {
    require_once $filename;
}

foreach (glob(dirname(__FILE__) . "/functions/*.php") as $filename) {
    require_once $filename;
}
