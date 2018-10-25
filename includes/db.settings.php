<?php
/**
 * Created by PhpStorm.
 * User: KoDusk
 * Date: 2/15/2018
 * Time: 11:06 AM
 */

// DB Connection
$db_host = "mysql.cs.rmit.edu.au:4022";
$db_user = "s3681709";
$db_pass = "nope";
$database = "s3681709";

$db = new mysqli($db_host, $db_user, $db_pass, $database);

if($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}