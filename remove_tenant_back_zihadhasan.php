<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('DBconnect.php');

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the phone number from the selected option
    $phone_number = isset($_POST['tenant_info']) ? trim($_POST['tenant_info']) : '';

    if (empty($phone_number)) {
        $error_message = "Invalid tenant selection.";
    } else {
        // Delete tenant based on the phone number
        $stmt_delete = $conn->prepare("DELETE FROM tenants WHERE phone_number = ?");
        
        if ($stmt_delete === false) {
            $error_message = "Error preparing delete query: " . $conn->error;
        } else {
            $stmt_delete->bind_param("s", $phone_number);
            
            if ($stmt_delete->execute()) {
                if ($stmt_delete->affected_rows > 0) {
                    // Also delete the user associated with this tenant
                    $stmt_delete_user = $conn->prepare("DELETE FROM users WHERE phone_number = ?");
                    if ($stmt_delete_user) {
                        $stmt_delete_user->bind_param("s", $phone_number);
                        $stmt_delete_user->execute();
                        $stmt_delete_user->close();
                    }
                    
                    // Redirect back to the frontend with a success message
                    header("Location: remove_tenant_front_zihadhasan.php?success=1");
                    exit();
                } else {
                    $error_message = "Tenant not found or already removed.";
                }
            } else {
                $error_message = "Error deleting tenant: " . $stmt_delete->error;
            }
            $stmt_delete->close();
        }
    }
} else {
    // If direct access without POST, redirect back
    header("Location: remove_tenant_front_zihadhasan.php");
    exit();
}

// If there was any error, redirect back with the error message
if (!empty($error_message)) {
    header("Location: remove_tenant_front_zihadhasan.php?error=" . urlencode($error_message));
    exit();
}
?>
