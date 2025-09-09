<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once("DBconnect.php");

// Check if the user is logged in
if (!isset($_SESSION['phone'])) {
    header("Location: login.php");
    exit();
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $adults = filter_input(INPUT_POST, 'adults', FILTER_VALIDATE_INT);
    $children = filter_input(INPUT_POST, 'children', FILTER_VALIDATE_INT);

    // Basic validation
    if (!$phone_number || $adults === false || $children === false) {
        $_SESSION['error_message'] = "Invalid input data. Please try again.";
        header("Location: tenant.php");
        exit();
    }

    try {
        // Prepare the SQL statement to INSERT new family details using phone_number
        $sql = "INSERT INTO family_details (phone_number, adults, children) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Bind parameters and execute the statement
        $stmt->bind_param("sii", $phone_number, $adults, $children);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Family details confirmed successfully!";
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "An error occurred: " . $e->getMessage();
    }
} else {
    // If the form was not submitted via POST, redirect
    $_SESSION['error_message'] = "Invalid request method.";
}

// Redirect back to the tenant dashboard
header("Location: tenant.php");
exit();
?>