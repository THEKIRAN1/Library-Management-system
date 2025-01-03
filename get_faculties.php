<?php
require_once '../config/db.php';

// Check if department_id is passed
if (isset($_GET['department_id'])) {
    $department_id = $_GET['department_id'];

    // Prepare the query to get faculties based on department_id
    $query = "SELECT id, name FROM faculties WHERE department_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Create an array of faculties
    $faculties = [];
    while ($row = $result->fetch_assoc()) {
        $faculties[] = $row;
    }

    // Return the faculties as JSON
    echo json_encode($faculties);

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
