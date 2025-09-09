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

// Check if the form was submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form inputs
    $flat_no = trim($_POST['flat_no']);
    $tenant_name = trim($_POST['tenant_name']);
    $phone_number = trim($_POST['phone_number']);
    $email = trim($_POST['email']);
    $tenant_type = trim($_POST['tenant_type']);
    $movein_date = date("Y-m-d"); // Automatically sets the move-in date to the current date

    // Step 1: Validate required inputs
    if (empty($flat_no) || empty($tenant_name) || empty($phone_number) || empty($tenant_type)) {
        $error_message = "Please fill out all required fields (Flat No, Name, Phone Number, and Tenant Type).";
    } else {
        // Step 2: Check the current status of the flat
        $stmt_status = $conn->prepare("SELECT status FROM flat_details WHERE flat_no = ?");
        if ($stmt_status === false) {
            $error_message = "Error preparing status query: " . $conn->error;
        } else {
            $stmt_status->bind_param("s", $flat_no);
            $stmt_status->execute();
            $result = $stmt_status->get_result();
            $flat_info = $result->fetch_assoc();
            $stmt_status->close();
        }

        if (!$flat_info) {
            $error_message = "Flat number '{$flat_no}' not found.";
        } else {
            $current_status = $flat_info['status'];
            $current_student_count = 0;

            // Check existing tenant type and count students if the flat is occupied
            if ($current_status === 'Occupied') {
                $stmt_check_tenants = $conn->prepare("SELECT tenant_type FROM tenants WHERE flat_no = ?");
                $stmt_check_tenants->bind_param("s", $flat_no);
                $stmt_check_tenants->execute();
                $result_check_tenants = $stmt_check_tenants->get_result();
                $tenant_info = $result_check_tenants->fetch_assoc();
                $stmt_check_tenants->close();
                
                if ($tenant_info) {
                    $existing_type = $tenant_info['tenant_type'];

                    if ($existing_type === 'Students') {
                        $stmt_count_students = $conn->prepare("SELECT COUNT(*) AS student_count FROM tenants WHERE flat_no = ? AND tenant_type = 'Students'");
                        $stmt_count_students->bind_param("s", $flat_no);
                        $stmt_count_students->execute();
                        $result_count = $stmt_count_students->get_result();
                        $student_count_row = $result_count->fetch_assoc();
                        $current_student_count = $student_count_row['student_count'];
                        $stmt_count_students->close();
                    }
                }
            }

            // Apply validation logic based on the gathered data
            if ($tenant_type === 'Family' && $current_status === 'Occupied') {
                $error_message = "Failed to add tenant: Flat is already occupied.";
            } elseif ($tenant_type === 'Students' && $current_student_count >= 4) {
                 $error_message = "Failed to add tenant: Flat has reached its maximum student capacity of 4.";
            }

            // If no errors, proceed with adding the new tenant
            if (empty($error_message)) {
                $conn->begin_transaction();
                try {
                    // Insert into the tenants table
                    $stmt_tenant = $conn->prepare("INSERT INTO tenants (flat_no, tenant_name, email, tenant_type, movein_date, phone_number) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt_tenant->bind_param("ssssss", $flat_no, $tenant_name, $email, $tenant_type, $movein_date, $phone_number);
                    $stmt_tenant->execute();
                    $stmt_tenant->close();

                    // Update flat status based on the final state
                    if ($tenant_type === 'Family' || ($tenant_type === 'Students' && ($current_student_count + 1) >= 4)) {
                         $stmt_update_flat = $conn->prepare("UPDATE flat_details SET status = 'Occupied' WHERE flat_no = ?");
                         $stmt_update_flat->bind_param("s", $flat_no);
                         $stmt_update_flat->execute();
                         $stmt_update_flat->close();
                    }
                    
                    $conn->commit();
                    $_SESSION['success_message'] = "Tenant successfully added!";
                    header("Location: add_tenant_front.php");
                    exit();
                } catch (mysqli_sql_exception $exception) {
                    $conn->rollback();
                    $error_message = "Error during transaction: " . $exception->getMessage();
                }
            }
        }
    }
} else {
    // If not a POST request, redirect back to the form
    header("Location: add_tenant_front.php");
    exit();
}

// Redirect with error message if an error occurred
if ($error_message) {
    // Pass the error message via a session variable
    $_SESSION['error_message'] = $error_message;
    header("Location: add_tenant_front.php");
    exit();
}
?>