<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if a session is not already active before starting one.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection file
include('DBconnect.php');

// Initialize an error variable
$error_message = '';

// --- 1. AUTHENTICATION AND SECURITY CHECK ---
// Check if the user is logged in AND has the 'admin' role.
// If not, redirect them to the login page for security.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// --- 2. HANDLE FORM SUBMISSION ---
// Check if the form was submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form inputs
    $nid = trim($_POST['nid']);
    $name = trim($_POST['name']);
    $phone_number = trim($_POST['phone_number']);
    $email = trim($_POST['email']);
    $home_address = trim($_POST['home_address']);
    $hire_date = date("Y-m-d"); // Automatically set the hire date to today

    // --- 3. INPUT VALIDATION ---
    if (empty($nid) || empty($name) || empty($phone_number) || empty($email) || empty($home_address) || empty($hire_date)) {
        $error_message = "All fields are required.";
    } else {
        // --- 4. CHECK IF NID ALREADY EXISTS ---
        $stmt_check = $conn->prepare("SELECT nid FROM manager WHERE nid = ?");
        if ($stmt_check === false) {
            $error_message = "Error preparing NID check query: " . $conn->error;
        } else {
            $stmt_check->bind_param("s", $nid);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                $error_message = "Manager with this NID already exists.";
            }
            $stmt_check->close();
        }

        // --- 5. INSERT NEW MANAGER IF NO ERRORS ---
        if (empty($error_message)) {
            $stmt_insert = $conn->prepare("INSERT INTO manager (nid, name, phone_number, email, home_address, hire_date) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt_insert === false) {
                $error_message = "Error preparing insert query: " . $conn->error;
            } else {
                $stmt_insert->bind_param("ssssss", $nid, $name, $phone_number, $email, $home_address, $hire_date);
                if ($stmt_insert->execute()) {
                    // Success: Redirect to a success page or back to the form with a success message
                    // Corrected the redirection page to add_manager_front.php
                    header("Location: add_manager_front.php?success=1");
                    exit();
                } else {
                    $error_message = "Error adding manager: " . $stmt_insert->error;
                }
                $stmt_insert->close();
            }
        }
    }
} else {
    // If not a POST request, redirect back to the form
    header("Location: add_manager_front.php");
    exit();
}

// --- 6. REDIRECT ON ERROR ---
if (!empty($error_message)) {
    // Redirect with error message passed as a query parameter
    header("Location: add_manager_front.php?error=" . urlencode($error_message));
    exit();
}
?>
