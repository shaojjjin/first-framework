<?php
/**
 * SYSTEM INFO
 */
define('IS_CGI', (0 === strpos(PHP_SAPI,'cgi') || false !== strpos(PHP_SAPI,'fcgi')) ? 1 : 0);
define('IS_WIN', strstr(PHP_OS, 'WIN') ? 1 : 0);
define('IS_CLI', PHP_SAPI=='cli'? 1 : 0);

/**
 * Define Folder Path 
 */
define('ROOT_PATH', __DIR__ . '/../');
define('APP_PATH', ROOT_PATH . 'application/');

require APP_PATH . 'functions.php';
require ROOT_PATH . 'vendor/autoload.php';

Framework\Core\App::init();


