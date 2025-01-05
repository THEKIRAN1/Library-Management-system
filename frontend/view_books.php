<?php
require_once '../config/db.php';
session_start(); // Start the session to check if the user is logged in



// Get the logged-in user's department and faculty
$user_id = $_SESSION['user_id'];
$query_user = "SELECT department_id, faculty_id FROM users WHERE id = ?";
$stmt = $conn->prepare($query_user);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();

if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
    $department_id = $user['department_id'];
    $faculty_id = $user['faculty_id'];

    // Query to get books for the logged-in user's department and faculty
    $query = "SELECT * FROM books WHERE department_id = ? AND faculty_id = ?";
    $stmt_books = $conn->prepare($query);
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
                    echo "<tr><td colspan='4' class='text-center'>No books available</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="user_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
