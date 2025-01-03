<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the form data
    $title = $_POST['title'];
    $publication = $_POST['publication'];
    $department_id = $_POST['department'];
    $faculty_id = $_POST['faculty'];
    $isbn = $_POST['isbn'];
    $author = $_POST['author'];

    // Insert the new book into the database
    $query = "INSERT INTO books (title, publication, department_id, faculty_id, isbn, author) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssiiis", $title, $publication, $department_id, $faculty_id, $isbn, $author);
    
    if ($stmt->execute()) {
        $success = "Book added successfully!";
    } else {
        $error = "Error during book addition!";
    }
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
    <!-- Include Bootstrap CSS -->
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
                    <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
                    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

                    <!-- Add Book Form -->
                    <form method="POST">
                        <div class="mb-3">
                            <label for="title" class="form-label">Book Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="author" class="form-label">Author</label>
                            <input type="text" class="form-control" name="author" required>
                        </div>

                        <div class="mb-3">
                            <label for="isbn" class="form-label">ISBN</label>
                            <input type="text" class="form-control" name="isbn" required>
                        </div>

                        <div class="mb-3">
                            <label for="publication" class="form-label">Publication</label>
                            <input type="text" class="form-control" name="publication" required>
                        </div>

                        <div class="mb-3">
                            <label for="department" class="form-label">Department</label>
                            <select name="department" id="department" class="form-select" required>
                                <?php while ($row = $departments_result->fetch_assoc()) { ?>
                                    <option value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
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

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Dynamically load faculties based on department selection
    document.getElementById('department').addEventListener('change', function() {
        var departmentId = this.value;
        
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "get_faculties.php?department_id=" + departmentId, true);
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
</script>

</body>
</html>
