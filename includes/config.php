<?php
/**
 * Created by PhpStorm.
 * User: Rashid
 * Date: 26-Jan-17
 * Time: 11:24 AM
 */

define('SITE_NAME', 'Musanada');

// Do not include a trailing slash
define('ROOT_URL', 'http://localhost:8000');

define('MYSQL_DATABASE', 'musanada');
define('MYSQL_USERNAME', 'root');
define('MYSQL_PASSWORD', '');
define('MYSQL_HOST', 'localhost');

define('MAX_BALANCE', 1000000);
define('MAX_LOAN_AMOUNT', 4000);
define('ECONOMY_TYPE', 'virtual');

// In bytes
define('MAX_IMAGE_SIZE', 5 * 1024 * 1024);
define('LOAN_LENGTH', 12);

define('DEBUG', true);

session_start();

if(DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}