<?php

return [
    'schema' => [
        '_parts/header',
        '_parts/sidebar',
        '_parts/header_nav',
        'x',
        '_parts/footer',
        '_parts/end'
    ],
    'variables' => [
        'bodyClass' => 'dashboard layout-fluid',
        'afterBody' => '<div class="page">',
        'beforeBodyClose' => '</div>'
    ],
    'ajaxLayout' => ['_parts/sidebar', '_parts/header_nav', 'x', '_parts/footer']
];
