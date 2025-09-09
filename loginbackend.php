<?php
// Enable error reporting (for debugging — disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();
// Include the database connection file
include('DBconnect.php');

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare statement to fetch user by email. Note the change to 'phone_number'.
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Login success — set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['role'] = $user['role']; // Save role
            // CRITICAL FIX: Use 'phone_number' to match the database column and 'tenant.php'
            $_SESSION['phone_number'] = $user['phone_number'];
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: adminhome.php");
            } elseif ($user['role'] === 'tenant'){
                header("Location: tenant.php");
            } elseif ($user['role'] === 'manager'){
                header("Location: manager.php");
            }
            // Always exit after a header redirect
            exit();
        } else {
            // Set error message for invalid password and redirect back to login
            $_SESSION['error_message'] = "Invalid email or password.";
            header("Location: login.php");
            exit();
        }
    } else {
        // Set error message for user not found and redirect back to login
        $_SESSION['error_message'] = "Invalid email or password.";
        header("Location: login.php");
        exit();
    }
} else {
    // If the page is accessed without a POST request, redirect to the login form
    header("Location: login.php");
    exit();
}
?>
