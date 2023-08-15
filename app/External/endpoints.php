<?php

/**
 * We use this file to specify authorization points and language definitions for display.
 * ex: [endpoint] => ['default' => (checked status), 'name' => (language definition for display)]
 */


return [
	'hesap' => [
		'default' => true,
		'name' => 'auth.auth',
	],

	'hesap/cikis' => [
		'default' => true,
		'name' => 'auth.auth_logout',
	],
];