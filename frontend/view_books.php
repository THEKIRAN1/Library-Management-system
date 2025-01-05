<?php
require_once '../config/db.php';
session_start(); // Start the session to check if the user is logged in

// Get the logged-in user's department and faculty
$user_id = $_SESSION['user_id'];

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

        <!-- Alert Message (Initially hidden) -->
        <div id="alertMessage" class="alert alert-success" style="display: none;">
            <strong>Success!</strong> You have borrowed the book.
        </div>

        <!-- Table to display books -->
        <table class="table table-bordered" id="booksTable">
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
                        echo "<tr data-book-id='" . $book['id'] . "' data-book-quantity='" . $book['number_of_books'] . "'>";
                        echo "<td>" . htmlspecialchars($book['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($book['author']) . "</td>";
                        echo "<td><button class='btn btn-primary borrowBtn' data-book-id='" . $book['id'] . "'>Borrow</button></td>";
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
    
    <script>
    // JavaScript to handle the borrow action and display the alert
    document.addEventListener('DOMContentLoaded', function () {
        const borrowButtons = document.querySelectorAll('.borrowBtn');
        
        borrowButtons.forEach(button => {
            button.addEventListener('click', function () {
                const bookId = this.getAttribute('data-book-id');
                
                // Make an AJAX request to borrow the book
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'borrow_book.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function () {
                    if (xhr.status == 200) {
                        // Show success alert
                        document.getElementById('alertMessage').style.display = 'block';

                        // Hide the alert after 3 seconds
                        setTimeout(function () {
                            document.getElementById('alertMessage').style.display = 'none';
                        }, 3000);

                        // Get the table row for this book
                        const bookRow = document.querySelector(`[data-book-id='${bookId}']`);
                        const bookQuantity = bookRow.getAttribute('data-book-quantity');
                        const updatedQuantity = bookQuantity - 1;

                        // Update the quantity in the table row
                        bookRow.setAttribute('data-book-quantity', updatedQuantity);

                        // If the quantity becomes 0, remove the row from the table
                        if (updatedQuantity == 0) {
                            bookRow.remove();
                        }
                    } else {
                        alert('Error borrowing book');
                    }
                };
                xhr.send('book_id=' + bookId);
            });
        });
    });
    </script>
</body>
</html>
