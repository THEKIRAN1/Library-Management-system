<?php
require_once '../config/db.php';

// Start the session at the beginning of your script
session_start();

// Initialize variables for success/error messages
$success = $error = '';

// Destroy session on page reload
session_destroy();

// Re-initialize the session after destroying
session_start();

// Form data variables
$title = $publication = $department_id = $faculty_id = $author = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the form data
    $title = $_POST['title'];
    $publication = $_POST['publication'];
    $department_id = $_POST['department'];
    $faculty_id = $_POST['faculty'];
    $author = $_POST['author'];

    // Insert the new book into the database
    $query = "INSERT INTO books (title, publication, department_id, faculty_id, author) 
    VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        // This will give more information about the error if prepare() fails
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param("ssiii", $title, $publication, $department_id, $faculty_id, $author);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Book added successfully!"; // Store the success message in the session
    } else {
        $_SESSION['error'] = "Error during book addition!"; // Store the error message in the session
    }

    // Clear form data after successful submission
    $title = $publication = $department_id = $faculty_id = $author = '';
}

// Fetch departments for the department dropdown
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

                    <!-- Success/Error messages -->
                    <?php
                    // Display session messages if available
                    if (isset($_SESSION['success'])) {
                        echo '<div id="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">'
                            . $_SESSION['success'] .
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                        unset($_SESSION['success']); // Clear the session message after displaying
                    }

                    if (isset($_SESSION['error'])) {
                        echo '<div id="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">'
                            . $_SESSION['error'] .
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                        unset($_SESSION['error']); // Clear the session message after displaying
                    }
                    ?>

                    <!-- Add Book Form -->
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
                                <?php while ($row = $departments_result->fetch_assoc()) { ?>
                                    <option value="<?= $row['id']; ?>" <?= $row['id'] == $department_id ? 'selected' : ''; ?>>
                                        <?= $row['name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="faculty" class="form-label">Faculty</label>
                            <select name="faculty" id="faculty" class="form-select" required>
                                <!-- Faculties will be populated dynamically based on selected department -->
                            </select>
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
    document.getElementById('department').addEventListener('change', function() {
        var departmentId = this.value;

        var xhr = new XMLHttpRequest();
        xhr.open("GET", "testget_faculties.php?department_id=" + departmentId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var faculties = JSON.parse(xhr.responseText);
                var facultySelect = document.getElementById('faculty');
                facultySelect.innerHTML = ''; // Clear existing options

                faculties.forEach(function(faculty) {
                    var option = document.createElement('option');
                    option.value = faculty.id;
                    option.text = faculty.name;
                    facultySelect.appendChild(option);
                });
            }
        };
        xhr.send();
    });

    // Hide success message after 5 seconds
    setTimeout(function() {
        var successMessage = document.getElementById('successMessage');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
    }, 5000);
</script>

</body>
</html>
