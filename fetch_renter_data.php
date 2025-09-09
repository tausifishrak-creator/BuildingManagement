<?php
// Establish the database connection
require_once("DBconnect.php");

// Set the content type to application/json
header('Content-Type: application/json');

// Get the renter ID from the GET request
$renterId = $_GET['id'] ?? null;

// Check if a renter ID was provided and if the database connection is successful
if (!$renterId || !$conn) {
    echo json_encode(['error' => 'Invalid request or database connection failed.']);
    exit();
}

try {
    // Prepare a statement to prevent SQL injection
    $sql = "SELECT id, name, phone_number, address, email, garage_spot_label FROM non_tenants WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        throw new Exception("SQL prepare failed: " . mysqli_error($conn));
    }

    // Bind the renter ID parameter
    mysqli_stmt_bind_param($stmt, "i", $renterId);
    
    // Execute the statement
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the data as an associative array
    $renterData = mysqli_fetch_assoc($result);

    if ($renterData) {
        // Encode the data as JSON and send it
        echo json_encode($renterData);
    } else {
        // Renter not found
        echo json_encode(['error' => 'Renter not found.']);
    }

    // Close the statement
    mysqli_stmt_close($stmt);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Close the database connection
    $conn->close();
}
?>
