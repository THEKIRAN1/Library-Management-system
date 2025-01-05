<?php
require_once '../config/db.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to borrow a book.");
}

<<<<<<< HEAD
// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Get the book ID from the GET request
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];

    // Check if the book is available
    $check_query = "SELECT number_of_books FROM books WHERE id = ? AND number_of_books > 0";
    $stmt_check = $conn->prepare($check_query);
    if ($stmt_check === false) {
        die('MySQL prepare error: ' . $conn->error);
    }
    $stmt_check->bind_param("i", $book_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    // If the book is available
    if ($stmt_check->num_rows > 0) {
        // Reduce the number of available books by 1
        $update_query = "UPDATE books SET number_of_books = number_of_books - 1 WHERE id = ?";
        $stmt_update = $conn->prepare($update_query);
        if ($stmt_update === false) {
            die('MySQL prepare error: ' . $conn->error);
        }
        $stmt_update->bind_param("i", $book_id);
        $stmt_update->execute();

        // Check if the update was successful
        if ($stmt_update->affected_rows > 0) {
            // Insert the borrow record into the borrowed_books table
            $borrow_date = date('Y-m-d');  // Current date as borrow date
            $return_date = NULL;  // No return date initially

            $insert_query = "INSERT INTO borrowed_books (user_id, book_id, borrow_date, return_date) 
                             VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($insert_query);
            if ($stmt_insert === false) {
                die('MySQL prepare error: ' . $conn->error);
            }
            $stmt_insert->bind_param("iiss", $user_id, $book_id, $borrow_date, $return_date);
            $stmt_insert->execute();

            // Set success message in the session
            $_SESSION['success_message'] = "Book borrowed successfully.";

            // Redirect to view_books.php with success message
            header('Location: view_books.php');
            exit;
        } else {
            // If updating the book count failed, set an error message in session
            $_SESSION['error_message'] = "Error borrowing book. Please try again.";
            header('Location: view_books.php');
            exit;
        }
    } else {
        // If no books are available for borrowing, set an error message in session
        $_SESSION['error_message'] = "No books available to borrow.";
        header('Location: view_books.php');
        exit;
    }
} else {
    // If no book ID is provided in the URL, set an error message in session
    $_SESSION['error_message'] = "No book ID provided.";
    header('Location: view_books.php');
    exit;
=======
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
>>>>>>> e9a6344a2ad39ac680f13e5ad307f8ca44da7697
}
?>
