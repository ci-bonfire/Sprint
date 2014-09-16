<?php

// Here you can initialize variables that will be available to your tests

// We need a fake CI superobject that we can use
// when doing our unit testing.
$ci = new stdClass();
$ci->load = new stdClass();

function get_instance() {
    global $ci;

    return $ci;
}