<?php
/**
 * Created by PhpStorm.
 * User: Rashid
 * Date: 26-Jan-17
 * Time: 12:16 PM
 */

try {
    $db = new PDO('mysql:host='.MYSQL_HOST.';dbname='.MYSQL_DATABASE, MYSQL_USERNAME, MYSQL_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    handleSqlErrors($e);
}
