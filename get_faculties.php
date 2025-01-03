<?php
require_once 'config/db.php'; // Ensure this path points to your database connection

if (isset($_GET['department_id']) && is_numeric($_GET['department_id'])) {
    $department_id = intval($_GET['department_id']); // Sanitize input to an integer

    // Prepare the query to fetch faculties by department ID
    $query = "SELECT id, name FROM faculties WHERE department_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $department_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $faculties = [];
        while ($row = $result->fetch_assoc()) {
            $faculties[] = $row; // Add each faculty to the array
        }

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($faculties);
    } else {
        // Handle query preparation error
        http_response_code(500); // Internal Server Error
        header('Content-Type: application/json');
        echo json_encode(["error" => "Failed to prepare the database query."]);
    }
    $stmt->close();
} else {
    // If department ID is missing or invalid, return an error response
    header('Content-Type: application/json');
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Invalid or missing department ID."]);
}
$conn->close(); // Ensure the connection is closed
?>
