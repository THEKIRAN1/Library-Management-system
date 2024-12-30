<?php
require_once '../config/db.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Check if ID is provided and delete the book
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    $query = "DELETE FROM books WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $book_id);
    
    if ($stmt->execute()) {
        header("Location: view_books.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
