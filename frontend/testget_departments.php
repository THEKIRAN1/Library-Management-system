<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'test';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to fetch departments
$sql = "SELECT id, name FROM departments";
$result = $conn->query($sql);

$departments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}

// Return departments as JSON
echo json_encode($departments);

$conn->close();
?>
