<?php
// Enable error reporting for debugging purposes.
// NOTE: This should be disabled in a production environment.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start the session to manage user login state.
session_start();

// Include the database connection file.
include('DBconnect.php');

// Initialize variables to store messages and user data.
$error_message = '';
$success_message = '';
$user_flat_no = null;
$garage_spot = null;
$user_email = null;

// --- 1. AUTHENTICATION AND DATA RETRIEVAL ---
// Check if the user is logged in by checking the session variable 'phone_number'.
// This is a critical security check. If not met, redirect to the login page.
// The original code was checking for 'phone', which was causing the redirect.
if (!isset($_SESSION['phone_number'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user's phone number from the session.
$user_phone = $_SESSION['phone_number'];

// Now, fetch the user's email and flat number from the database using their phone number.
$stmt = $conn->prepare("SELECT flat_no, email FROM tenants WHERE phone_number = ?");
$stmt->bind_param("s", $user_phone);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $user_flat_no = $row['flat_no'];
    $user_email = $row['email'];
}
$stmt->close();

// If the user's flat number is found, fetch the associated garage spot details.
if ($user_flat_no) {
    $stmt = $conn->prepare("SELECT * FROM garage WHERE flat_no = ?");
    $stmt->bind_param("s", $user_flat_no);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $garage_spot = $row;
    }
    $stmt->close();
}

// --- 2. HANDLE FORM SUBMISSION FOR GARAGE ACTION ---
// Check if the form was submitted via POST and has an 'action' field.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $spot_label = $_POST['spot_label'];
    $new_status = '';
    
    // Determine the new status based on the user's action.
    if ($action === 'occupy') {
        $new_status = 'Occupied';
    } elseif ($action === 'rent') {
        $new_status = 'For Rent';
    } elseif ($action === 'vacant') {
        // New logic to set the status back to Vacant
        $new_status = 'Vacant';
    }

    // Only proceed if a valid status was determined.
    if (!empty($new_status)) {
        // Update the garage spot status in the database.
        $stmt = $conn->prepare("UPDATE garage SET status = ? WHERE spot_label = ?");
        $stmt->bind_param("ss", $new_status, $spot_label);
        if ($stmt->execute()) {
            $success_message = "Garage spot status has been updated successfully!";
            // Use the Post/Redirect/Get pattern to prevent form resubmission.
            header("Location: tenantgarage.php?success=" . urlencode($success_message));
            exit();
        } else {
            $error_message = "Error updating garage status: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Garage Details</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <!-- The CSS link is now corrected to look in the same folder -->
        <link rel="stylesheet" href="adminstyle.css">
        <title>Tenant Dashboard</title>
    </head>
    <body class="body">
        <?php include('navbartenant.php'); ?>
        <main>
            <div class="tenants2">
                <h2 class="text-center mb-4">Garage Spot Details</h2>

                <!-- Display success or error messages -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($garage_spot): ?>
                    <div class="card p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4>Garage Spot: <?php echo htmlspecialchars($garage_spot['spot_label']); ?></h4>
                            <span class="badge <?php echo ($garage_spot['status'] === 'Vacant') ? 'bg-secondary' : 'bg-success'; ?>">
                                <?php echo htmlspecialchars($garage_spot['status']); ?>
                            </span>
                        </div>
                        <hr>
                        <p><strong>Rent:</strong> $<?php echo htmlspecialchars($garage_spot['rent']); ?></p>
                    </div>

                    <?php if ($garage_spot['status'] === 'Vacant'): ?>
                        <div class="card p-4">
                            <h4 class="text-center mb-3">Manage Your Garage Spot</h4>
                            <p class="text-center">This spot is currently vacant. Would you like to occupy it or leave it for rent?</p>
                            <form method="POST" action="tenantgarage.php" class="d-flex justify-content-center gap-3">
                                <input type="hidden" name="spot_label" value="<?php echo htmlspecialchars($garage_spot['spot_label']); ?>">
                                <button type="submit" name="action" value="occupy" class="btn btn-primary">Occupy Spot</button>
                                <button type="submit" name="action" value="rent" class="btn btn-secondary">Leave for Rent</button>
                            </form>
                        </div>
                    <?php elseif ($garage_spot['status'] === 'Occupied' || $garage_spot['status'] === 'For Rent'): ?>
                        <div class="card p-4 text-center">
                            <p>You can change the status of your garage spot at any time.</p>
                            <form method="POST" action="tenantgarage.php">
                                <input type="hidden" name="spot_label" value="<?php echo htmlspecialchars($garage_spot['spot_label']); ?>">
                                <button type="submit" name="action" value="vacant" class="btn btn-warning">I change My mind</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- This block handles the case where the status is 'Rented' and no button should be shown -->
                        <div class="card p-4 text-center">
                            <p class="text-muted">Your garage spot is currently rented out. No actions are available at this time.</p>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="alert alert-info text-center" role="alert">
                        No garage spot is assigned to your flat.
                    </div>
                <?php endif; ?>

            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </main>
    </body>
</html>
