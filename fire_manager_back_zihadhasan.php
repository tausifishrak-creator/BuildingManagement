<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include DB connection
include('DBconnect.php');

// Initialize error variable
$error_message = '';

// --- 1. AUTHENTICATION CHECK ---
// Only allow admin to fire/remove managers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// --- 2. HANDLE FORM SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve input
    $nid = trim($_POST['nid']);

    // --- 3. INPUT VALIDATION ---
    if (empty($nid)) {
        $error_message = "Please select a manager to remove.";
    } else {
        // --- 4. CHECK IF MANAGER EXISTS ---
        $stmt_check = $conn->prepare("SELECT nid FROM manager WHERE nid = ?");
        if ($stmt_check === false) {
            $error_message = "Error preparing query: " . $conn->error;
        } else {
            $stmt_check->bind_param("s", $nid);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows === 0) {
                $error_message = "Manager not found.";
            }
            $stmt_check->close();
        }

        // --- 5. DELETE MANAGER IF EXISTS ---
        if (empty($error_message)) {
            $stmt_delete = $conn->prepare("DELETE FROM manager WHERE nid = ?");
            if ($stmt_delete === false) {
                $error_message = "Error preparing delete query: " . $conn->error;
            } else {
                $stmt_delete->bind_param("s", $nid);
                if ($stmt_delete->execute()) {
                    // Success → Redirect to fire_manager.php with success message
                    header("Location: fire_manager_front_zihadhasan.php?success=1");
                    exit();
                } else {
                    $error_message = "Error deleting manager: " . $stmt_delete->error;
                }
                $stmt_delete->close();
            }
        }
    }
} else {
    // If not POST, redirect back to form
    header("Location: fire_manager_front_zihadhasan.php");
    exit();
}

// --- 6. REDIRECT ON ERROR ---
if (!empty($error_message)) {
    header("Location: fire_manager_front_zihadhasan.php?error=" . urlencode($error_message));
    exit();
}
?>
