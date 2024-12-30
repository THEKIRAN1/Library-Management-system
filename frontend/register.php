<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Using hashed password
    $role = $_POST['role']; // Either 'teacher' or 'student'
    $department_id = $_POST['department'];
    $faculty_id = $_POST['faculty'];

    // Insert the user into the database
    $query = "INSERT INTO users (name, email, password, role, department_id, faculty_id) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssii", $name, $email, $password, $role, $department_id, $faculty_id);
    
    if ($stmt->execute()) {
        $success = "Registration successful!";
    } else {
        $error = "Error during registration!";
    }
}

// Fetch departments for the dropdown
$departments_query = "SELECT * FROM departments";
$departments_result = $conn->query($departments_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Register</h2>
                    
                    <!-- Success/Error messages -->
                    <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
                    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

                    <!-- Registration Form -->
                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="teacher">Teacher</option>
                                <option value="student">Student</option>
                            </select>
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

                        <button type="submit" class="btn btn-primary w-100">Register</button>
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
