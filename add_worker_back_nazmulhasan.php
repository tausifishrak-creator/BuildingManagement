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
// Only allow admin users to add workers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// --- 2. HANDLE FORM SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form inputs
    $serial_no = trim($_POST['serial_no']);
    $name = trim($_POST['name']);
    $post = trim($_POST['post']);
    $phone_number = trim($_POST['phone_number']);
    $wages = trim($_POST['wages']);
    

    // --- 3. INPUT VALIDATION ---
    if (empty($serial_no) || empty($name) || empty($post) || empty($phone_number) || empty($wages)) {
        $error_message = "All fields are required.";
    } else {
        // --- 4. CHECK IF NID ALREADY EXISTS IN WORKER TABLE ---
        $stmt_check = $conn->prepare("SELECT serial_no FROM workers WHERE serial_no = ?");
        if ($stmt_check === false) {
            $error_message = "Error preparing NID check query: " . $conn->error;
        } else {
            $stmt_check->bind_param("s", $serial_no);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                $error_message = "Workers with this NID already exists.";
            }
            $stmt_check->close();
        }

        // --- 5. INSERT NEW WORKER IF NO ERRORS ---
        if (empty($error_message)) {
            $stmt_insert = $conn->prepare("INSERT INTO workers (serial_no, name ,post, phone_number, wages ) VALUES (?, ?, ?, ?, ?)");
            if ($stmt_insert === false) {
                $error_message = "Error preparing insert query: " . $conn->error;
            } else {
                $stmt_insert->bind_param("sssss", $serial_no, $name ,$post, $phone_number, $wages);
                if ($stmt_insert->execute()) {
                    // Success: Redirect to worker frontend with success message
                    header("Location: add_worker_front_nazmulhasan.php?success=1");
                    exit();
                } else {
                    $error_message = "Error adding workers: " . $stmt_insert->error;
                }
                $stmt_insert->close();
            }
        }
    }
} else {
    // If not a POST request, redirect back to the worker form
    header("Location: add_worker_front_nazmulhasan.php");
    exit();
}

// --- 6. REDIRECT ON ERROR ---
if (!empty($error_message)) {
    header("Location: add_worker_front_nazmulhasan.php?error=" . urlencode($error_message));
    exit();
}
?>
