<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'library_management';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if department_id is provided
if (isset($_GET['department_id'])) {
    $department_id = $_GET['department_id'];

    // SQL to fetch faculties based on department_id
    $sql = "SELECT id, name FROM faculties WHERE department_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result = $stmt->get_result();

    
    $faculties = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $faculties[] = $row;
        }
    }

    // Return faculties as JSON
    echo json_encode($faculties);

    $stmt->close();
} else {
    // If department_id is not provided, return an empty array
    echo json_encode([]);
}

$conn->close();
?>
