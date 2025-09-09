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
// Only allow admin users to fire workers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// --- 2. HANDLE FORM SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form input
    $serial_no = trim($_POST['serial_no']);

    // --- 3. INPUT VALIDATION ---
    if (empty($serial_no)) {
        $error_message = "Worker selection is required.";
    } else {
        // --- 4. CHECK IF WORKER EXISTS ---
        $stmt_check = $conn->prepare("SELECT serial_no FROM workers WHERE serial_no = ?");
        if ($stmt_check === false) {
            $error_message = "Error preparing worker check query: " . $conn->error;
        } else {
            $stmt_check->bind_param("s", $serial_no);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows === 0) {
                $error_message = "Worker not found.";
            }
            $stmt_check->close();
        }

        // --- 5. DELETE WORKER IF EXISTS ---
        if (empty($error_message)) {
            $stmt_delete = $conn->prepare("DELETE FROM workers WHERE serial_no = ?");
            if ($stmt_delete === false) {
                $error_message = "Error preparing delete query: " . $conn->error;
            } else {
                $stmt_delete->bind_param("s", $serial_no);
                if ($stmt_delete->execute()) {
                    // Success: Redirect to fire worker frontend with success message
                    header("Location: fire_worker_front_nazmulhasan.php?success=1");
                    exit();
                } else {
                    $error_message = "Error removing worker: " . $stmt_delete->error;
                }
                $stmt_delete->close();
            }
        }
    }
} else {
    // If not a POST request, redirect back to the fire worker form
    header("Location: fire_worker_front_nazmulhasan.php");
    exit();
}

// --- 6. REDIRECT ON ERROR ---
if (!empty($error_message)) {
    header("Location: fire_worker_front_nazmulhasan.php?error=" . urlencode($error_message));
    exit();
}
?>
