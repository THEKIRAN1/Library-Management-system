<?php
require_once '../config/db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');  // Redirect to login if not logged in
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the user's name based on the user_id (make sure to adjust column name if it's different)
$query_user = "SELECT name FROM users WHERE id = ?";
$stmt_user = $conn->prepare($query_user);
if ($stmt_user === false) {
    die('MySQL prepare error: ' . $conn->error);
}

$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();

if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
    $username = htmlspecialchars($user['name']); // Secure the output
} else {
    die('User not found.');
}

// Fetch the user's borrowed books history
$query = "SELECT 
              b.title AS book_title, 
              b.author AS book_author, 
              bb.borrow_date, 
              bb.return_date, 
              bb.id AS borrow_id
          FROM 
              borrowed_books bb
          JOIN 
              books b ON bb.book_id = b.id
          WHERE 
              bb.user_id = ?";

$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);  // If prepare fails, stop and show error
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Debugging: Check the query result
if ($result === false) {
    die('MySQL query error: ' . $conn->error);
}

// Handle the return book request
if (isset($_GET['return_id'])) {
    $borrow_id = $_GET['return_id'];
    $return_date = date('Y-m-d'); // Set current date as return date

    // Update the borrowed_books table with the return date
    $update_query = "UPDATE borrowed_books SET return_date = ? WHERE id = ?";
    $stmt_update = $conn->prepare($update_query);
    if ($stmt_update === false) {
        die('MySQL prepare error: ' . $conn->error);
    }
    $stmt_update->bind_param("si", $return_date, $borrow_id);
    $stmt_update->execute();

    // If the update was successful, increase the available books count
    if ($stmt_update->affected_rows > 0) {
        // Get the book ID to update the available number of books
        $book_id_query = "SELECT book_id FROM borrowed_books WHERE id = ?";
        $stmt_book_id = $conn->prepare($book_id_query);
        $stmt_book_id->bind_param("i", $borrow_id);
        $stmt_book_id->execute();
        $result_book_id = $stmt_book_id->get_result();
        
        if ($result_book_id->num_rows > 0) {
            $book_data = $result_book_id->fetch_assoc();
            $book_id = $book_data['book_id'];

            // Update the books table to increment the available number of books
            $update_books_query = "UPDATE books SET number_of_books = number_of_books + 1 WHERE id = ?";
            $stmt_update_books = $conn->prepare($update_books_query);
            $stmt_update_books->bind_param("i", $book_id);
            $stmt_update_books->execute();
        }

        // Redirect to the dashboard with success message
        header('Location: user_dashboard.php?message=Book returned successfully.');
        exit;
    } else {
        echo "Error returning the book.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Arial', sans-serif;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 1.25rem;
            font-weight: bold;
        }
        .card-body {
            background-color: #f9f9f9;
        }
        .history-table th {
            background-color: #007bff;
            color: white;
        }
        .history-table td {
            text-align: center;
        }
        .empty-message {
            text-align: center;
            font-size: 1.2rem;
            color: #6c757d;
        }
        /* Make the table responsive on small devices */
        .table-responsive {
            overflow-x: auto;
        }
        /* For small screens, buttons should be full width */
        @media (max-width: 768px) {
            .btn {
                width: 100%; /* Full width for buttons on small screens */
                margin-bottom: 10px; /* Add some space between buttons */
            }
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Welcome, <?php echo $username; ?>!</h2> <!-- Display the user's name here -->

    <!-- Borrowed Books History Section -->
    <div class="card">
        <div class="card-header">
            Borrowed Books History
        </div>
        <div class="card-body">
            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered history-table">
                        <thead>
                            <tr>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Borrow Date</th>
                                <th>Return Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['book_author']); ?></td>
                                    <td><?php echo date("F j, Y", strtotime($row['borrow_date'])); ?></td>
                                    <td><?php echo $row['return_date'] ? date("F j, Y", strtotime($row['return_date'])) : 'Not Returned Yet'; ?></td>
                                    <td>
                                        <?php if ($row['return_date'] === NULL): ?>
                                            <a href="user_dashboard.php?return_id=<?php echo $row['borrow_id']; ?>" class="btn btn-danger btn-sm">Return</a>
                                        <?php else: ?>
                                            <span class="text-muted">Returned</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-message">
                    You haven't borrowed any books yet.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="text-center mt-4">
        <a href="view_books.php" class="btn btn-primary btn-lg">View Books</a>
        <a href="logout.php" class="btn btn-secondary btn-lg">Logout</a>
    </div>
</div>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
