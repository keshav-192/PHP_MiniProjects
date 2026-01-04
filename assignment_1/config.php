<?php
declare(strict_types=1);

$DB_HOST = '127.0.0.1';
$DB_NAME = 'assign_1';
$DB_USER = 'root';
$DB_PASS = '';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_error) {
  http_response_code(500);
  exit('Database connection failed.');
}
$mysqli->set_charset('utf8mb4');
?>
