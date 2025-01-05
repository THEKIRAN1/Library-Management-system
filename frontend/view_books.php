<?php
require_once '../config/db.php';
session_start(); // Start the session to check if the user is logged in

// Get the logged-in user's department and faculty
$user_id = $_SESSION['user_id'];

// Check for flash messages
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;

// Clear the messages from session after they are displayed
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Prepare the query to get the department and faculty for the logged-in user
$query_user = "SELECT department_id, faculty_id FROM users WHERE id = ?";
$stmt = $conn->prepare($query_user);

if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();

if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
    $department_id = $user['department_id'];
    $faculty_id = $user['faculty_id'];

    // Query to get books for the logged-in user's department and faculty
    // Exclude books with number_of_books = 0
    $query = "SELECT * FROM books WHERE department_id = ? AND faculty_id = ? AND number_of_books > 0";
    $stmt_books = $conn->prepare($query);
    if ($stmt_books === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt_books->bind_param("ii", $department_id, $faculty_id);
    $stmt_books->execute();
    $result = $stmt_books->get_result();
} else {
    // If no user data found, redirect or show an error
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Books Available</h2>
        <p>Here are the books you can borrow:</p>

        <!-- Display Success or Error Messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success" id="message" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php elseif ($error_message): ?>
            <div class="alert alert-danger" id="message" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Table to display books -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if there are any books
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($book = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($book['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($book['author']) . "</td>";
                        echo "<td><a href='borrow_book.php?book_id=" . $book['id'] . "' class='btn btn-primary'>Borrow</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' class='text-center'>No books available</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="user_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript to hide the message after 2 seconds -->
    <script>
        window.onload = function() {
            var message = document.getElementById("message");
            if (message) {
                setTimeout(function() {
                    message.style.display = 'none';
                }, 2000);  // 2000ms = 2 seconds
            }
        };
    </script>
</body>
</html>
