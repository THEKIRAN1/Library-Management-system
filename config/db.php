<?php
$host = 'localhost';          // Database server
$dbname = 'library_management'; // Database name
$username = 'root';           // Default username for XAMPP/WAMP
$password = '';               // Default password for XAMPP/WAMP (leave empty)

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
