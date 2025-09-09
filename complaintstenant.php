<?php

// Start the session to manage user data
session_start();

// Enable MySQLi error reporting for easier debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Include the database connection file
require_once("DBconnect.php");

// --- 1. User Authentication ---
// Check if the user is logged in via their phone number in the session.
if ( !isset($_SESSION['phone_number']) ) {
    header("Location: login.php");
    exit();
}

$phone = $_SESSION['phone_number'];
$tenant_name = 'Guest'; // Default value

// Fetch the tenant's name from the database to use in the complaint
$stmt = $conn->prepare("SELECT tenant_name FROM tenants WHERE phone_number = ?");
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();
if ( $result->num_rows > 0 ) {
    $tenant_info = $result->fetch_assoc();
    $tenant_name = $tenant_info['tenant_name'];
}
$stmt->close();

// --- 2. Handle Complaint Submission ---
// Check if the form was submitted via POST
if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {

    // Check if the complaint message is set
    if ( isset($_POST['complaint_message']) && !empty(trim($_POST['complaint_message'])) ) {
        $complaint_text = trim($_POST['complaint_message']);
        $status = 'pending'; // Set the initial status of the complaint

        try {
            // Prepare the SQL statement for inserting the complaint
            $stmt = $conn->prepare("INSERT INTO complaints (user_name, complaint_text, status, phone_number) VALUES (?, ?, ?, ?)");
            
            if ( $stmt === false ) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            // Bind the parameters and execute the statement
            $stmt->bind_param("ssss", $tenant_name, $complaint_text, $status, $phone);
            
            if ( $stmt->execute() ) {
                $_SESSION['success_message'] = "Your complaint has been submitted successfully.";
            } else {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $_SESSION['error_message'] = "An error occurred: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "Complaint message cannot be empty.";
    }
    
    // Redirect to the same page to prevent form resubmission on refresh
    header("Location: complaintstenant.php");
    exit();
}

// --- 3. Display Messages from Session ---
$error_message = isset($_SESSION['error_message']) ? htmlspecialchars($_SESSION['error_message']) : '';
$success_message = isset($_SESSION['success_message']) ? htmlspecialchars($_SESSION['success_message']) : '';

// Clear the session messages after displaying them
unset($_SESSION['error_message']);
unset($_SESSION['success_message']);

// Close the database connection
$conn->close();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tenant Complaints</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="adminstyle.css">
</head>
<body class="body">

    <?php include('navbartenant.php'); ?>
    
    <main>
        <div class="container mt-5">
            <div class="card shadow p-4">
                <h2 class="mb-3">Submit a Complaint</h2>
                <p class="mb-4">Please describe your issue below. An administrator will review it shortly.</p>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert"><?= $error_message; ?></div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div class="alert alert-success" role="alert"><?= $success_message; ?></div>
                <?php endif; ?>

                <form action="complaintstenant.php" method="POST">
                    <div class="mb-3">
                        <label for="complaint_message" class="form-label">Your Complaint</label>
                        <textarea class="form-control" id="complaint_message" name="complaint_message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Complaint</button>
                </form>
            </div>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
