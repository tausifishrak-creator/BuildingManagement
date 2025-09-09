<?php 
// Start session
session_start();
// Enable MySQLi error reporting for easier debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
// Include the database connection file
require_once("DBconnect.php");

// Check if the user is logged in by checking the phone number in the session
if (isset($_SESSION['phone_number'])) {
    $phone = $_SESSION['phone_number'];
} else {
    // If phone number is not in session, redirect to login
    header("Location: login.php");
    exit();
}

// Check for and display session-based messages
$error_message = isset($_SESSION['error_message']) ? htmlspecialchars($_SESSION['error_message']) : '';
$success_message = isset($_SESSION['success_message']) ? htmlspecialchars($_SESSION['success_message']) : '';

// Clear the session messages after displaying them
unset($_SESSION['error_message']);
unset($_SESSION['success_message']);

// Fetch tenant's name, flat number, and type for conditional display
$tenant_info = null;
$tenant_name = 'Guest'; // Default value
$stmt = $conn->prepare("SELECT tenant_name, flat_no, tenant_type FROM tenants WHERE `phone_number` = ?");
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $tenant_info = $result->fetch_assoc();
    $tenant_name = $tenant_info['tenant_name'];
}
$stmt->close();

// Check if a record exists in family_details for this tenant's phone number
$family_details_exist = false;
$family_details = null;
if ($tenant_info && $tenant_info['tenant_type'] === 'Family') {
    $stmt = $conn->prepare("SELECT adults, children FROM family_details WHERE `phone_number` = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $family_details_exist = true;
        $family_details = $result->fetch_assoc();
    }
    $stmt->close();
}

// Check if a record exists in student_details for this tenant's phone number
$student_details_exist = false;
$student_details = null;
if ($tenant_info && $tenant_info['tenant_type'] === 'Students') {
    $stmt = $conn->prepare("SELECT student_id, institute, emergency_contact, emg_cont_name FROM student_details WHERE `phone_number` = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $student_details_exist = true;
        $student_details = $result->fetch_assoc();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
    <head>
    <meta charset="UTF-8">
    <title>Tenant Dashboard</title>
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
                    <h2 class="mb-3">Welcome, <?= htmlspecialchars($tenant_name); ?>!</h2>
                    <p class="mb-4">This is your dashboard.</p>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger" role="alert"><?= $error_message; ?></div>
                    <?php endif; ?>
                    <?php if ($success_message): ?>
                        <div class="alert alert-success" role="alert"><?= $success_message; ?></div>
                    <?php endif; ?>

                    <div class="row row-cols-1 row-cols-md-3 g-4">
                        <div class="col">
                            <a href="total_bill.php" class="text-decoration-none">
                                <div class="card text-white bg-primary h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5 class="card-title">Total Bill</h5>
                                            <i class="fa-solid fa-screwdriver-wrench fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col">
                            <a href="tenantgarage.php" class="text-decoration-none">
                                <div class="card text-white bg-dark h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5 class="card-title">Garage Status</h5>
                                            <i class="fa-solid fa-car fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <section class="tenants2">
                <div class="tenants_box">
                    <h1>Tenant Details</h1>
                    <table class="tenants_table">
                        <thead>
                            <tr>
                                <th>Flat-no</th>
                                <th>Tenant Name</th>
                                <th>Phone Number</th>
                                <th>Email</th>
                                <th>Tenant Type</th>
                                <th>Move-in Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            try {
                                $stmt = $conn->prepare("SELECT * FROM tenants WHERE `phone_number` = ?");
                                
                                if ($stmt === false) {
                                    die("Prepare failed: " . htmlspecialchars($conn->error));
                                }
                                
                                $stmt->bind_param("s", $phone);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                
                                if($result->num_rows > 0){
                                    while($row = $result->fetch_assoc()){
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row["flat_no"]); ?></td>
                            <td><?php echo htmlspecialchars($row["tenant_name"]); ?></td>
                            <td><?php echo htmlspecialchars($row["phone_number"]); ?></td>
                            <td><?php echo htmlspecialchars($row["email"]); ?></td>
                            <td><?php echo htmlspecialchars($row["tenant_type"]); ?></td>
                            <td><?php echo htmlspecialchars($row["movein_date"]); ?></td>
                        </tr>
                        <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="6">No tenant details found.</td></tr>';
                                }
                                
                                $stmt->close();

                            } catch (Exception $e) {
                                echo '<tr><td colspan="6" style="color:red;">An error occurred: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </section>
            
            <!-- Conditional Family Details Form -->
            <?php if ($tenant_info && $tenant_info['tenant_type'] === 'Family'): ?>
            <div class="container mt-5">
                <div class="card shadow p-4">
                    <?php if ($family_details_exist): ?>
                        <h2 class="mb-3">Update Family Details</h2>
                        <form action="update_family_details.php" method="POST">
                            <input type="hidden" name="phone_number" value="<?= htmlspecialchars($phone); ?>">
                            <div class="mb-3">
                                <label for="num_of_members" class="form-label">Number of Members</label>
                                <p class="form-control-plaintext">
                                    <?= ($family_details['adults'] ?? 0) + ($family_details['children'] ?? 0); ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label for="adults" class="form-label">Adults</label>
                                <input type="number" class="form-control" id="adults" name="adults" value="<?= htmlspecialchars($family_details['adults'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="children" class="form-label">Children</label>
                                <input type="number" class="form-control" id="children" name="children" value="<?= htmlspecialchars($family_details['children'] ?? ''); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    <?php else: ?>
                        <h2 class="mb-3">Confirm Your Family Details</h2>
                        <p class="mb-4">It looks like you haven't entered your family details yet. Please confirm them below to proceed.</p>
                        <form action="confirm_family_details.php" method="POST">
                            <input type="hidden" name="phone_number" value="<?= htmlspecialchars($phone); ?>">
                            <div class="mb-3">
                                <label for="adults" class="form-label">Adults</label>
                                <input type="number" class="form-control" id="adults" name="adults" value="0" required min="0">
                            </div>
                            <div class="mb-3">
                                <label for="children" class="form-label">Children</label>
                                <input type="number" class="form-control" id="children" name="children" value="0" required min="0">
                            </div>
                            <button type="submit" class="btn btn-success">Confirm</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php elseif ($tenant_info && $tenant_info['tenant_type'] === 'Students'): ?>
            <!-- Conditional Student Details Form -->
            <div class="container mt-5">
                <div class="card shadow p-4">
                    <?php if ($student_details_exist): ?>
                        <h2 class="mb-3">Update Student Details</h2>
                        <form action="update_student_details.php" method="POST">
                            <input type="hidden" name="phone_number" value="<?= htmlspecialchars($phone); ?>">
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Student ID</label>
                                <input type="text" class="form-control" id="student_id" name="student_id" value="<?= htmlspecialchars($student_details['student_id'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="institute" class="form-label">Institute</label>
                                <input type="text" class="form-control" id="institute" name="institute" value="<?= htmlspecialchars($student_details['institute'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="emergency_contact" class="form-label">Emergency Contact</label>
                                <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" value="<?= htmlspecialchars($student_details['emergency_contact'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="emg_cont_name" class="form-label">Emergency Contact Name</label>
                                <input type="text" class="form-control" id="emg_cont_name" name="emg_cont_name" value="<?= htmlspecialchars($student_details['emg_cont_name'] ?? ''); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    <?php else: ?>
                        <h2 class="mb-3">Confirm Your Student Details</h2>
                        <p class="mb-4">It looks like you haven't entered your student details yet. Please confirm them below to proceed.</p>
                        <form action="confirm_student_details.php" method="POST">
                            <input type="hidden" name="phone_number" value="<?= htmlspecialchars($phone); ?>">
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Student ID</label>
                                <input type="text" class="form-control" id="student_id" name="student_id" required>
                            </div>
                            <div class="mb-3">
                                <label for="institute" class="form-label">Institute</label>
                                <input type="text" class="form-control" id="institute" name="institute" required>
                            </div>
                            <div class="mb-3">
                                <label for="emergency_contact" class="form-label">Emergency Contact</label>
                                <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" required>
                            </div>
                            <div class="mb-3">
                                <label for="emg_cont_name" class="form-label">Emergency Contact Name</label>
                                <input type="text" class="form-control" id="emg_cont_name" name="emg_cont_name" required>
                            </div>
                            <button type="submit" class="btn btn-success">Confirm</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </main>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
