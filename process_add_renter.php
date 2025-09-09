<?php
require_once("DBconnect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $id = $_POST['id'];
    $name = $_POST['name'];
    $phoneNumber = $_POST['phone_number'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $garageSpotLabel = $_POST['garage_spot_label'];
    
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Begin transaction
    mysqli_begin_transaction($conn);

    try {
        // 1. Insert new renter into the `non_tenants` table
        $sql_insert = "INSERT INTO non_tenants (id, name, phone_number, address, email, garage_spot_label) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "isssss", $id, $name, $phoneNumber, $address, $email, $garageSpotLabel);
        
        if (!mysqli_stmt_execute($stmt_insert)) {
            throw new Exception("Error adding renter: " . mysqli_stmt_error($stmt_insert));
        }

        // 2. Update the `garage` table to set the spot status to 'Rented'
        $sql_update = "UPDATE garage SET status = 'Rented' WHERE spot_label = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "s", $garageSpotLabel);

        if (!mysqli_stmt_execute($stmt_update)) {
            throw new Exception("Error updating garage status: " . mysqli_stmt_error($stmt_update));
        }

        // If both operations are successful, commit the transaction
        mysqli_commit($conn);
        header("Location: admingarage.php?success=add");

    } catch (Exception $e) {
        // If an error occurs, roll back the transaction
        mysqli_rollback($conn);
        header("Location: admingarage.php?error=" . urlencode($e->getMessage()));
    } finally {
        // Close statements and connection
        mysqli_stmt_close($stmt_insert);
        mysqli_stmt_close($stmt_update);
        $conn->close();
    }
} else {
    // If the request is not a POST, redirect to the form page
    header("Location: addrenter.php");
}
?>
