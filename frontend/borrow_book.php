<?php
require_once '../config/db.php';
session_start();

// Check if the user is logged in


// Check if the book ID is passed
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];

    // Query to check if the book exists
    $query = "SELECT * FROM books WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Book exists, proceed with borrowing (You can insert borrowing logic here)

        // Example: Insert a record into a borrowings table
        $user_id = $_SESSION['user_id'];
        $query_borrow = "INSERT INTO borrowings (user_id, book_id, borrowed_at) VALUES (?, ?, NOW())";
        $stmt_borrow = $conn->prepare($query_borrow);
        $stmt_borrow->bind_param("ii", $user_id, $book_id);
        $stmt_borrow->execute();

        // Redirect to view books page with success message
        header("Location: view_books.php?borrowed=true");
        exit;
    } else {
        // Book does not exist
        header("Location: view_books.php?error=Book not found");
        exit;
    }
} else {
    // Book ID is not provided
    header("Location: view_books.php?error=Invalid book ID");
    exit;
}
