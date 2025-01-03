<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'library_management';  // Use your actual database name
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $department_id = $_POST['department'];
    $faculty_id = $_POST['faculty'];
    $role = $_POST['role']; // Get the role selected by the user (student or teacher)
    $password = $_POST['password']; // Get the plain-text password from the form

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the data into the user table (not registrations)
    $sql = "INSERT INTO users (name, email, department_id, faculty_id, role, password) VALUES (?, ?, ?, ?, ?, ?)";
    
    // Prepare the query
    $stmt = $conn->prepare($sql);

    // Check for errors in preparing the query
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("ssiiss", $name, $email, $department_id, $faculty_id, $role, $hashed_password);

    // Execute the query
    if ($stmt->execute()) {
        $success = "Registration successful! You can now log in.";
        $login_link = '<a href="login.php" class="btn btn-primary w-100 mt-3">Login Now</a>';
    } else {
        $error = "Error: " . $stmt->error;
    }

    // Close the statement
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
    <title>Register</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body.bg-light {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .card.shadow-lg {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 1rem;
        }

        .card-title {
            font-size: 2rem;
            font-weight: 600;
        }

        .form-label {
            font-size: 1rem;
            font-weight: 400;
        }

        .form-control,
        .form-select {
            font-size: 1rem;
            padding: 0.75rem;
            border-radius: 0.375rem;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .alert {
            margin-top: 15px;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        #faculty-container {
            display: none;
        }

        #faculty {
            width: 100%;
        }

        #register-btn {
            cursor: not-allowed;
        }

        #register-btn:enabled {
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Register</h2>

                        <!-- Success/Error messages inside the form -->
                        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
                        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

                        <!-- Registration Form -->
                        <form method="POST">
                            <div class="mb-2">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>

                            <div class="mb-2">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>

                            <div class="mb-2">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>

                            <div class="mb-2">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="teacher">Teacher</option>
                                    <option value="student">Student</option>
                                </select>
                            </div>

                            <!-- Department Selection -->
                            <div class="mb-2">
                                <label for="department" class="form-label">Department</label>
                                <select name="department" id="department" class="form-select" required>
                                    <option value="">Select Department</option>
                                    <!-- Departments will be loaded here -->
                                </select>
                            </div>

                            <!-- Faculty Section (Initially hidden) -->
                            <div class="mb-3" id="faculty-container" style="display: none;">
                                <label for="faculty" class="form-label">Faculty</label>
                                <select name="faculty" id="faculty" class="form-control" disabled required>
                                    <option value="">Select Faculty</option>
                                    <!-- Faculties will be loaded here based on department -->
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100" id="register-btn" disabled>Register</button>
                        </form>

                        <!-- Success message with Login Now button -->
                        <?php
                         if (isset($success)) echo $login_link  ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load departments on page load
        $(document).ready(function () {
            $.ajax({
                url: 'testget_departments.php', // URL for getting departments
                type: 'GET',
                success: function (data) {
                    let departments = JSON.parse(data);
                    departments.forEach(function (department) {
                        $('#department').append('<option value="' + department.id + '">' + department.name + '</option>');
                    });
                }
            });

            // Load faculties based on selected department
            $('#department').on('change', function () {
                let departmentId = $(this).val();
                if (departmentId) {
                    $.ajax({
                        url: 'testget_faculties.php', // URL for getting faculties based on department
                        type: 'GET',
                        data: { department_id: departmentId },
                        success: function (data) {
                            let faculties = JSON.parse(data);
                            $('#faculty').html('<option value="">Select Faculty</option>'); // Reset faculty dropdown
                            faculties.forEach(function (faculty) {
                                $('#faculty').append('<option value="' + faculty.id + '">' + faculty.name + '</option>');
                            });
                            $('#faculty').prop('disabled', false); // Enable faculty dropdown
                            $('#faculty-container').show(); // Show faculty section after selecting a department
                            $('#register-btn').prop('disabled', false); // Enable the register button once faculty is selected
                        }
                    });
                } else {
                    $('#faculty').html('<option value="">Select Faculty</option>');
                    $('#faculty').prop('disabled', true); // Disable faculty dropdown
                    $('#faculty-container').hide(); // Hide faculty section if no department is selected
                    $('#register-btn').prop('disabled', true); // Disable register button if no department or faculty selected
                }
            });
        });
    </script>
</body>

</html>
