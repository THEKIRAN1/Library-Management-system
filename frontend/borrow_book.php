<?php
require_once '../config/db.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to borrow a book.");
}

// Get the book ID from the request
if (isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];

    // Check if there are books available for borrowing
    $check_query = "SELECT number_of_books FROM books WHERE id = ? AND number_of_books > 0";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bind_param("i", $book_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        // Reduce the number of books by 1
        $update_query = "UPDATE books SET number_of_books = number_of_books - 1 WHERE id = ?";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bind_param("i", $book_id);
        $stmt_update->execute();

        // Check if the update was successful
        if ($stmt_update->affected_rows > 0) {
            echo "Book borrowed successfully.";
        } else {
            echo "Error borrowing book.";
        }
    } else {
        echo "No books available.";
    }
} else {
    echo "No book ID provided.";
}
?>
