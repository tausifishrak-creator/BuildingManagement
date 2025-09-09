<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "buildingmanagement"; // Make sure to use the correct database name

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select the database
if (!mysqli_select_db($conn, $dbname)) {
    die("Could not select database: " . mysqli_error($conn));
}

// Optional: you can add a confirmation message
// echo "Connection Established";
?>