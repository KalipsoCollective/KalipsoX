<?php

/**
 * Basic constants
 **/
define('KX_START', microtime(true)); // We use it for the exec. time recorded in the log.
define('KX_ROOT',  rtrim($_SERVER["DOCUMENT_ROOT"], '/').'/');

/**
 * Shutdown function registration
 **/
register_shutdown_function( function() {
    KX\Core\Exception::fatalHandler();
});

/**
 * Error handler set
 **/
set_error_handler( function($level, $error, $file, $line) {
    if (0 === error_reporting()) {
        return false;
    }
    KX\Core\Exception::errorHandler($level, $error, $file, $line);
}, E_ALL);

/**
 * Exception handler set
 **/
set_exception_handler( function($e) {
    KX\Core\Exception::exceptionHandler($e);
});

/**
 * php.ini set and error reporting setting
 **/
ini_set('display_errors', 'on');
error_reporting(E_ALL);