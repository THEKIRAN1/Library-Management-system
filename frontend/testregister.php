<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Include Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add Bootstrap CSS (from a CDN) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* Custom styling (optional) */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding-top: 50px;
        }

        .container {
            max-width: 650px;
            margin: 0 auto;
        }

        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 7px 8px rgba(0, 0, 0, 0.1);
        }

        /* Hide faculty section initially */
        #faculty-section {
            display: none;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="form-container">
        <h2 class="card-title text-center mb-4">Register</h2>

            <!-- Registration form starts here -->
            <form id="registrationForm" method="POST" action="register.php">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" class="form-control" name="name" id="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>

                <!-- Role Selection -->
                <div class="form-group">
                    <label for="role">Role </label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="">Select Role</option>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                    </select>
                </div>

                <!-- Department Selection -->
                <div class="form-group">
                    <label for="department">Department </label>
                    <select name="department" id="department" class="form-control" required>
                        <option value="">Select Department</option>
                        <!-- Departments will be loaded here -->
                    </select>
                </div>

                <!-- Faculty Section (Initially hidden) -->
                <div id="faculty-section" class="form-group">
                    <label for="faculty">Faculty </label>
                    <select name="faculty" id="faculty" class="form-control" required disabled>
                        <option value="">Select Faculty</option>
                        <!-- Faculties will be loaded here based on department -->
                    </select>
                </div>

                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Bootstrap JS and Popper.js (for Bootstrap features like tooltips, modals, etc.) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
                            $('#faculty-section').show(); // Show faculty section after selecting a department
                        }
                    });
                } else {
                    $('#faculty').html('<option value="">Select Faculty</option>');
                    $('#faculty').prop('disabled', true); // Disable faculty dropdown
                    $('#faculty-section').hide(); // Hide faculty section if no department is selected
                }
            });
        });
    </script>
</body>

</html>

<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'test';  // Use your actual database name
$conn = new mysqli($host, $username, $password, $dbname);

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

    // Insert the data into the registrations table
    $sql = "INSERT INTO registrations (name, email, department_id, faculty_id, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiis", $name, $email, $department_id, $faculty_id, $role); // Updated to bind 'role' parameter

    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
