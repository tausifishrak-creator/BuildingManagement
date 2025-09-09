<?php
require_once("DBconnect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the renter ID from the form
    $renterId = $_POST['renter_id'];
    
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Begin transaction
    mysqli_begin_transaction($conn);

    try {
        // Step 1: Find the garage spot label associated with the renter before deleting
        $sql_find_spot = "SELECT garage_spot_label FROM non_tenants WHERE id = ?";
        $stmt_find_spot = mysqli_prepare($conn, $sql_find_spot);
        mysqli_stmt_bind_param($stmt_find_spot, "i", $renterId);
        mysqli_stmt_execute($stmt_find_spot);
        $result = mysqli_stmt_get_result($stmt_find_spot);
        $row = mysqli_fetch_assoc($result);
        $garageSpotLabel = $row['garage_spot_label'];
        mysqli_stmt_close($stmt_find_spot);

        if (!$garageSpotLabel) {
            throw new Exception("Renter not found or no garage spot associated.");
        }

        // Step 2: Delete the renter from the `non_tenants` table
        $sql_delete = "DELETE FROM non_tenants WHERE id = ?";
        $stmt_delete = mysqli_prepare($conn, $sql_delete);
        mysqli_stmt_bind_param($stmt_delete, "i", $renterId);
        
        if (!mysqli_stmt_execute($stmt_delete)) {
            throw new Exception("Error removing renter: " . mysqli_stmt_error($stmt_delete));
        }
        mysqli_stmt_close($stmt_delete);

        // Step 3: Update the `garage` table to set the spot status to 'For Rent'
        $sql_update = "UPDATE garage SET status = 'For Rent' WHERE spot_label = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "s", $garageSpotLabel);

        if (!mysqli_stmt_execute($stmt_update)) {
            throw new Exception("Error updating garage status: " . mysqli_stmt_error($stmt_update));
        }
        mysqli_stmt_close($stmt_update);

        // If all operations are successful, commit the transaction
        mysqli_commit($conn);
        header("Location: admingarage.php?success=remove");

    } catch (Exception $e) {
        // If an error occurs, roll back the transaction
        mysqli_rollback($conn);
        header("Location: admingarage.php?error=" . urlencode($e->getMessage()));

    } finally {
        // Close the database connection
        $conn->close();
    }
} else {
    // If the request is not a POST, redirect back to the form page
    header("Location: removerenter.php");
}
?>
