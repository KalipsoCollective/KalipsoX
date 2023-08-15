<?php

/**
	* This section contains settings that are used for the core structure of
	* the system and require only one-time correction. You can add settings 
	* that will not require re-intervention after the first adjustment during development.
	* 
	**/


return [
	'name'		=> 'KalipsoX',
	'dev_mode'	=> true,
	'session'	=> 'kx',
	'charset'	=> 'utf-8',
	'title_format' => '[TITLE] [SEPERATOR] [APP]',
	'available_languages' => ['tr'],
	'auth' => 'jwt', // cookie, jwt
];