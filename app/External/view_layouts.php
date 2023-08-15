<?php
// _ => content
return [
  'admin'   => ['header', 'nav', '_', 'footer', 'end'],
  'auth'    => ['header', 'nav', '_', 'footer', 'end'],
  'default' => ['header', 'nav', '_', 'footer', 'end'],
  'error'   => ['header', 'nav', '_', 'footer', 'end'],
  'sandbox' => [
    'parts' => ['header', '_', 'end'],
    'layout' => [
      'bodyClass' => 'layout-boxed',
    ]
  ],
];