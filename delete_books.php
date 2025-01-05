<?php
require_once '../config/db.php';

// Check if book_id is provided in the query string
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Invalid request.";
    exit;
}

$book_id = intval($_GET['id']); // Sanitize the book ID

// Delete the book from the database
$delete_query = "DELETE FROM books WHERE id = ?";
$stmt_delete = $conn->prepare($delete_query);
if (!$stmt_delete) {
    die("Error preparing delete statement: " . $conn->error);
}
$stmt_delete->bind_param("i", $book_id);
$stmt_delete->execute();

header("Location: view_books.php"); // Redirect back to the book list
exit;
?>
