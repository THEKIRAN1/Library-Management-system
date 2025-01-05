<?php
require_once '../config/db.php'; // Adjust the path if needed
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query the database for the user by email
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
       // Password verification and session setup
// Password verification and session setup
if (password_verify($password, $user['password'])) {
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];

    // Debugging: log the role
    error_log("User Role: " . $user['role']); // Check if role is being retrieved

    // Redirect based on role
    switch ($user['role']) {
        case 'admin':
            header("Location: http://localhost/library_management/frontend/admin_dashboard.php");  // Absolute path for admin
            exit;
        case 'teacher':
        case 'student':
            header("Location: http://localhost/library_management/frontend/user_dashboard.php");  // Absolute path for user
            exit;
        default:
            header("Location: http://localhost/library_management/frontend/admin_dashboard.php");  // Default redirect if role is unexpected
            exit;
    }



} else {
    // Invalid password
    $error = "Invalid password!";
}

    } else {
        // Invalid email
        $error = "Invalid email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Login</h2>
                        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
