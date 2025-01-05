<?php
require_once '../config/db.php';
session_start();

// Initialize variables for success/error messages
$title = $publication = $department_id = $faculty_id = $author = $number_of_books = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture and sanitize POST data
    $title = $_POST['title'] ?? '';
    $publication = $_POST['publication'] ?? '';
    $department_id = $_POST['department'] ?? '';
    $faculty_id = $_POST['faculty'] ?? '';
    $author = $_POST['author'] ?? '';
    $number_of_books = $_POST['number_of_books'] ?? '';

    // Validate required fields
    if (empty($title) || empty($publication) || empty($department_id) || empty($faculty_id) || empty($author) || empty($number_of_books)) {
        $_SESSION['error'] = 'All fields are required.';
        header("Location: add_book.php");
        exit;
    }

    // Insert into the database
    $query = "INSERT INTO books (title, publication, author, department_id, faculty_id, number_of_books) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        $_SESSION['error'] = 'Prepare failed: ' . $conn->error;
        header("Location: add_book.php");
        exit;
    }

    $stmt->bind_param("sssiii", $title, $publication, $author, $department_id, $faculty_id, $number_of_books);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Book(s) added successfully!";
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    header("Location: add_book.php");
    exit;
}

// Fetch departments
$departments_query = "SELECT * FROM departments";
$departments_result = $conn->query($departments_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Add New Book</h2>

                    <!-- Display success or error messages -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $_SESSION['success']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $_SESSION['error']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <!-- Book form -->
                    <form method="POST">
                        <div class="mb-3">
                            <label for="title" class="form-label">Book Title</label>
                            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($title); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="author" class="form-label">Author</label>
                            <input type="text" class="form-control" name="author" value="<?= htmlspecialchars($author); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="publication" class="form-label">Publication</label>
                            <input type="text" class="form-control" name="publication" value="<?= htmlspecialchars($publication); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="department" class="form-label">Department</label>
                            <select name="department" id="department" class="form-select" required>
                                <option value="" disabled selected>Select Department</option>
                                <?php while ($row = $departments_result->fetch_assoc()): ?>
                                    <option value="<?= $row['id']; ?>" <?= $row['id'] == $department_id ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($row['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="faculty" class="form-label">Faculty</label>
                            <select name="faculty" id="faculty" class="form-select" required>
                                <option value="" disabled selected>Select Faculty</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="number_of_books" class="form-label">Number of Books</label>
                            <input type="number" class="form-control" name="number_of_books" value="<?= htmlspecialchars($number_of_books); ?>" required min="1">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add Book</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Hide success message after 2 seconds
    setTimeout(function () {
        var successMessage = document.querySelector('.alert-success');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
    }, 2000);

    // Fetch faculties based on department selection
    document.getElementById('department').addEventListener('change', function () {
        var departmentId = this.value;

        var xhr = new XMLHttpRequest();
        xhr.open("GET", "testget_faculties.php?department_id=" + departmentId, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var faculties = JSON.parse(xhr.responseText);
                var facultySelect = document.getElementById('faculty');
                facultySelect.innerHTML = '<option value="" disabled selected>Select Faculty</option>';

                faculties.forEach(function (faculty) {
                    var option = document.createElement('option');
                    option.value = faculty.id;
                    option.text = faculty.name;
                    facultySelect.appendChild(option);
                });
            }
        };
        xhr.send();
    });
</script>
</body>
</html>
