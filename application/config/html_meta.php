<?php

//--------------------------------------------------------------------
// HTML Meta Tag settings
//--------------------------------------------------------------------
// This array will be automatically read into the ThemedController's
// $meta object to set default HTML meta tag values.
//

$config['meta'] = [
    'x-ua-compatible'   => 'ie=edge',
    'viewport'          => 'width=device-width, initial-scale=1',
];

//--------------------------------------------------------------------
// HTTP Equivalent tags.
//--------------------------------------------------------------------
// Any tag names listed here will be output with `http-equiv` instead
// of the standard `name` attribute.
//

$config['http-equiv'] = [
    'x-dns-prefetch-control'
];
