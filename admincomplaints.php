<?php
// admincomplaints.php - Admin panel to view and resolve complaints.
// This version is integrated with login.php, DBconnect.php, and styled with Bootstrap.

// --- 1. INCLUDE NECESSARY FILES ---
// Start the session and include the database connection.
session_start();
include('DBconnect.php');

// --- 2. PHP LOGIC AND AUTHENTICATION ---
// Check if the user is logged in AND has the 'admin' role.
// If not, redirect them to the login page for security.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Handle complaint resolution
if (isset($_POST['resolve_complaint'])) {
    $complaint_id = $_POST['complaint_id'];
    // Use a prepared statement to prevent SQL injection.
    $sql = "UPDATE complaints SET status = 'resolved' WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $complaint_id);
        $stmt->execute();
        $stmt->close();
        header("Location: admincomplaints.php"); // Refresh the page
        exit();
    }
}

// Fetch complaints from the database
$sql = "SELECT id, user_name, complaint_text, status FROM complaints ORDER BY status DESC, created_at DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="adminstyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap"
        rel="stylesheet"
    />
    <style>
        .notification-badge {
            position: absolute;
            top: 10px;
            right: 15px;
            padding: 5px 8px;
            border-radius: 50%;
            background: red;
            color: white;
            font-size: 0.75rem;
        }
    </style>
</head>
<body class="body">

<?php include('navbar.php'); ?>
<!-- End of Navbar Code -->

<div class="container mt-5">
    <div class="card shadow p-4">
        <h2 class="mb-4 text-center">Complaints Dashboard</h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User Name</th>
                            <th>Complaint</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['complaint_text']); ?></td>
                                <td>
                                    <?php
                                    $statusClass = ($row['status'] === 'resolved') ? 'bg-success' : 'bg-danger';
                                    ?>
                                    <span class="badge rounded-pill <?php echo $statusClass; ?>">
                                        <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'pending'): ?>
                                        <form action="admincomplaints.php" method="POST" style="display: inline;">
                                            <input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                            <button type="submit" name="resolve_complaint" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Resolve
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <i class="fas fa-check-circle text-success"></i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center" role="alert">
                No complaints to display.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Include Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?php
// Close the database connection (handled by DBconnect.php but good practice to close)
$conn->close();
?>