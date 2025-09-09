<?php
include('DBconnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back to the admin page with a success message
        header("Location: adminpublicmessage.php?status=success");
    } else {
        // Redirect back with an error message
        header("Location: adminpublicmessage.php?status=error");
    }
    $stmt->close();
} else {
    // Redirect back if no valid ID was provided
    header("Location: adminpublicmessage.php");
}
if ($conn) {
    $conn->close();
}
exit();
?>
