<?php
require_once '../config/db.php';

// Admin credentials
$name = 'Admin';
$email = 'admin@library.com';
$password = 'admin123';  // The password in plain text (we'll hash it)
$role = 'admin';

// Hash the password using password_hash() function
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert into users table
$query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

// Execute the query
if ($stmt->execute()) {
    echo "Admin user created successfully!";
} else {
    echo "Error: " . $stmt->error;
}
?>
