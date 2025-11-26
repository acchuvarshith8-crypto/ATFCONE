<?php
// ------------------ DATABASE SETTINGS ------------------
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';        // default XAMPP password
$DB_NAME = 'atfc1';   // your correct database name

// ------------------ CONNECT ------------------
$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// ------------------ ERROR CHECK ------------------
if (!$conn) {
    die("DB Connection failed: " . mysqli_connect_error());
}
?>
