<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'completionist');
define('DB_PASSWORD', 'eighteight');
define('DB_NAME', 'completionist');

$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($mysqli === false){
    die("ERROR: Could not connect. " . $mysqli->connect_error);
}
?>
