
<?php
// Database connection settings
$servername = "localhost";
$username = "root"; // Use your MySQL username
$password = ""; // Use your MySQL password
$dbname = "book_database"; // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST['title'];
    $author = $_POST['author'];
    $year = $_POST['year'];
    $genre = $_POST['genre'];
    $description = $_POST['description'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO books (title, author, year, genre, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $title, $author, $year, $genre, $description);

    // Execute the prepared statement
    if ($stmt->execute()) {
        echo '<div class="container mt-5">
                <div class="alert alert-success" role="alert">
                    New book added successfully!
                </div>
              </div>';
    } else {
        echo '<div class="container mt-5">
                <div class="alert alert-danger" role="alert">
                    Error: ' . $stmt->error . '
                </div>
              </div>';
    }

    // Close the statement and connection
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a Book</title>
    <!-- Link to Bootstrap 4/5 CSS (using CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 50px;
        }
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

    <div class="container">
        <h1 class="text-center">Add a New Book</h1>
        <div class="form-container">
            <form action="add_book.php" method="POST">
                <div class="mb-3">
                    <label for="title" class="form-label">Book Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>

                <div class="mb-3">
                    <label for="author" class="form-label">Author</label>
                    <input type="text" class="form-control" id="author" name="author" required>
                </div>

                <div class="mb-3">
                    <label for="year" class="form-label">Publication Year</label>
                    <input type="number" class="form-control" id="year" name="year" required>
                </div>

                <div class="mb-3">
                    <label for="genre" class="form-label">Genre</label>
                    <select class="form-select" id="genre" name="genre" required>
                        <option value="fiction">Fiction</option>
                        <option value="non-fiction">Non-Fiction</option>
                        <option value="fantasy">Fantasy</option>
                        <option value="mystery">Mystery</option>
                        <option value="romance">Romance</option>
                        <option value="science">Science</option>
                        <option value="history">History</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Book Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

    <!-- Link to Bootstrap JS (using CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
