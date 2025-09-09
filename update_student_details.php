<?php
// Start the session to access and set session variables
session_start();

// Enable MySQLi error reporting for easier debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Include the database connection file
require_once("DBconnect.php");

// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required form fields are present
    if (isset($_POST['phone_number']) && isset($_POST['student_id']) && isset($_POST['institute']) && isset($_POST['emergency_contact']) && isset($_POST['emg_cont_name'])) {
        
        // Sanitize and get the form data
        $phone_number = trim($_POST['phone_number']);
        $student_id = trim($_POST['student_id']);
        $institute = trim($_POST['institute']);
        $emergency_contact = trim($_POST['emergency_contact']);
        $emg_cont_name = trim($_POST['emg_cont_name']);

        try {
            // Prepare the SQL statement to prevent SQL injection
            $stmt = $conn->prepare("UPDATE student_details SET student_id = ?, institute = ?, emergency_contact = ?, emg_cont_name = ? WHERE phone_number = ?");
            
            // Check if the statement was prepared successfully
            if ($stmt === false) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            // Bind the parameters to the statement
            // "sssss" indicates that all five parameters are strings
            $stmt->bind_param("sssss", $student_id, $institute, $emergency_contact, $emg_cont_name, $phone_number);
            
            // Execute the prepared statement
            if ($stmt->execute()) {
                // If the update was successful, set a success message
                $_SESSION['success_message'] = "Student details updated successfully.";
            } else {
                // If the update failed, set an error message
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            // Close the statement
            $stmt->close();

        } catch (Exception $e) {
            // Catch any exceptions and set an error message
            $_SESSION['error_message'] = "Error: " . $e->getMessage();
        }

    } else {
        // If not all required fields were submitted, set an error message
        $_SESSION['error_message'] = "All fields are required. Please try again.";
    }
} else {
    // If the request method is not POST, set an error message
    $_SESSION['error_message'] = "Invalid request method.";
}

// Close the database connection
$conn->close();

// Redirect the user back to the tenant dashboard page
header("Location: tenant.php");
exit();
?>
