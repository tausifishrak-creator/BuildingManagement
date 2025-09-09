<?php
// Start session to access user data
session_start();
// Include the database connection file
require_once("DBconnect.php");

// Check if the user is logged in and is a manager
if (isset($_SESSION['phone_number'])) {
    $phone = $_SESSION['phone_number'];
} else {
    // If phone number is not in session, redirect to login
    header("Location: login.php");
    exit();
}

$manager_phone = $_SESSION['phone_number'];
$manager_nid = '';
$manager_name = '';
$tenants_details = [];
$error_message = '';

try {
    // Step 1: Fetch the manager's NID and name using the phone number from the session.
    $stmt_manager = $conn->prepare("SELECT nid, name FROM manager WHERE phone_number = ?");
    $stmt_manager->bind_param("s", $manager_phone);
    $stmt_manager->execute();
    $result_manager = $stmt_manager->get_result();
    
    if ($result_manager->num_rows > 0) {
        $manager_row = $result_manager->fetch_assoc();
        $manager_name = $manager_row['name'];
        $manager_nid = $manager_row['nid'];
    } else {
        $error_message = "Manager profile not found for this phone number.";
    }
    $stmt_manager->close();

    // Step 2: If the manager NID was successfully retrieved, fetch the flats and tenant details.
    if (!empty($manager_nid)) {
        // Fetch all flats and their respective tenants assigned to this manager using a LEFT JOIN
        $stmt_tenants = $conn->prepare("
            SELECT fd.flat_no, fd.status, t.tenant_name, t.phone_number, t.email, t.tenant_type, t.movein_date
            FROM flat_details fd
            LEFT JOIN tenants t ON fd.flat_no = t.flat_no
            WHERE fd.manager_nid = ?
            ORDER BY fd.flat_no
        ");
        $stmt_tenants->bind_param("s", $manager_nid);
        $stmt_tenants->execute();
        $result_tenants = $stmt_tenants->get_result();
        
        if ($result_tenants->num_rows > 0) {
            while ($row = $result_tenants->fetch_assoc()) {
                $tenants_details[] = $row;
            }
        } else {
            $error_message = "You are not managing any flats or there are no tenants in your managed flats.";
        }
        $stmt_tenants->close();
    } else {
        $error_message = "Session phone number is not linked to a manager profile. Please log in again.";
    }

} catch (Exception $e) {
    $error_message = 'An error occurred: ' . htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manager Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="adminstyle.css">
</head>
<body class="body">
    <?php include('navbarmanager.php'); ?>

    <main>
        <div class="container mt-5">
            <div class="card shadow p-4">
                <h2 class="mb-3">Welcome, <?= htmlspecialchars($manager_name); ?>!</h2>
                <p class="mb-4">This is your dashboard. Below you can see the flats under your management and the tenants living in them.</p>
                <div class="col">
                            <a href="managerassignworker.php" class="text-decoration-none">
                                <div class="card text-white bg-info h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5 class="card-title">Worker</h5>
                                            <i class="fa-solid fa-hard-hat fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                <?php if ($error_message): ?>
                    <div class="alert alert-warning" role="alert"><?= $error_message; ?></div>
                <?php endif; ?>
            </div>
        </div>

        <section class="tenants2">
            <div class="tenants_box">
                <h1>Flats and Tenant Details Under Your Management</h1>
                <?php if (!empty($tenants_details)): ?>
                    <table class="tenants_table">
                        <thead>
                            <tr>
                                <th>Flat No.</th>
                                <th>Tenant Name</th>
                                <th>Phone Number</th>
                                <th>Email</th>
                                <th>Tenant Type</th>
                                <th>Move-in Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tenants_details as $tenant): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($tenant["flat_no"]); ?></td>
                                    <td><?php echo htmlspecialchars($tenant["tenant_name"] ?? 'Vacant'); ?></td>
                                    <td><?php echo htmlspecialchars($tenant["phone_number"] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($tenant["email"] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($tenant["tenant_type"] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($tenant["movein_date"] ?? 'N/A'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info mt-4" role="alert">No tenant data found for your managed flats.</div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Close the database connection
$conn->close();
?>
