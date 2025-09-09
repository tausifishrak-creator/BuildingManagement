<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if the DB connection file exists
if (!file_exists("DBconnect.php")) {
    die("Error: DBconnect.php not found. Please ensure it's in the same directory.");
}

// Include the database connection
require_once("DBconnect.php");

// Check if the database connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the user is logged in using the correct session variable
if (!isset($_SESSION['phone_number'])) {
    header("Location: login.php");
    exit();
}

// Use the correct session variable to get the phone number
$phone_number = $_SESSION['phone_number'];

// Initialize bill components
$tenant_name = 'Tenant';
$flat_no = null;
$flatRent = 0;
$garageRent = 0;
$totalMembers = 0;
$utilityBill = 0;
$totalBill = 0;
$tenant_type = ''; // Initialize tenant_type

$errorMessage = '';

try {
    // --- Step 1: Fetch tenant, flat, family details, and tenant type along with the tenant's name ---
    $query = "
        SELECT 
            t.flat_no, 
            t.tenant_name,
            t.tenant_type,
            fd.adults, 
            fd.children,
            f.rent AS flat_rent
        FROM tenants t
        LEFT JOIN family_details fd ON t.phone_number = fd.phone_number
        LEFT JOIN flat_details f ON t.flat_no = f.flat_no
        WHERE t.phone_number = ?
    ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Failed to prepare the tenant query: " . $conn->error);
    }

    $stmt->bind_param("s", $phone_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $tenantData = $result->fetch_assoc();
        $flat_no = $tenantData['flat_no'];
        $tenant_name = $tenantData['tenant_name'];
        $flatRent = $tenantData['flat_rent'] ?? 0; // Use null coalescing to handle potential null values
        $tenant_type = $tenantData['tenant_type'] ?? '';

        // --- Step 2: Calculate bill based on tenant type ---
        $utility_rate_per_member = 500; // This rate is used for both students and families

        // Note: The comparison is now case-sensitive and should match the value in your database ('Family' or 'Students')
        if ($tenant_type === 'Family') {
            $adults = $tenantData['adults'] ?? 0;
            $children = $tenantData['children'] ?? 0;
            $totalMembers = (int)$adults + (int)$children;
            $utilityBill = $totalMembers * $utility_rate_per_member;
        } elseif ($tenant_type === 'Students') {
            // For a student, the flat rent is divided by the number of students in the flat
            $queryStudentCount = "SELECT COUNT(*) AS student_count FROM tenants WHERE flat_no = ? AND tenant_type = 'Students'";
            $stmtStudent = $conn->prepare($queryStudentCount);
            if (!$stmtStudent) {
                throw new Exception("Failed to prepare student count query: " . $conn->error);
            }
            $stmtStudent->bind_param("s", $flat_no);
            $stmtStudent->execute();
            $resultStudent = $stmtStudent->get_result();
            $studentData = $resultStudent->fetch_assoc();
            $studentCount = $studentData['student_count'] ?? 1; // Default to 1 to avoid division by zero
            $stmtStudent->close();

            // Calculate the student's share of the flat rent
            if ($studentCount > 0) {
                $flatRent = $flatRent / $studentCount;
            } else {
                $flatRent = $flatRent;
            }
            
            // For a student, the utility bill is based on a fixed estimated value (1 member)
            $totalMembers = 1;
            $utilityBill = $totalMembers * $utility_rate_per_member;
        } else {
            // Default case for any other tenant type
            $totalMembers = 1;
            $utilityBill = $totalMembers * $utility_rate_per_member;
        }

    } else {
        $errorMessage = "Tenant details not found for this phone number. Please check your data or contact the administrator.";
    }
    $stmt->close();

    // --- Step 3: Fetch garage rent if status is 'Occupied' ---
    if ($flat_no) { // Only run this if a flat number was found
        $queryGarage = "
            SELECT rent, status 
            FROM garage 
            WHERE flat_no = ? AND status = 'Occupied'
        ";
        $stmtGarage = $conn->prepare($queryGarage);
        if (!$stmtGarage) {
            throw new Exception("Failed to prepare the garage query: " . $conn->error);
        }
        $stmtGarage->bind_param("s", $flat_no);
        $stmtGarage->execute();
        $resultGarage = $stmtGarage->get_result();
        
        if ($resultGarage->num_rows > 0) {
            $garageData = $resultGarage->fetch_assoc();
            $garageRent = $garageData['rent'] ?? 0;
        }
        $stmtGarage->close();
    }

    // --- Step 4: Calculate the total bill ---
    $totalBill = $flatRent + $garageRent + $utilityBill;

} catch (Exception $e) {
    // Catching any database-related errors
    $errorMessage = "A database error occurred: " . $e->getMessage();
} finally {
    // Ensure the database connection is closed
    if ($conn) {
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Total Bill</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="adminstyle.css">
</head>
<body class="body">
    <?php include('navbartenant.php'); ?>
    <main>
        <div class="tenants2 p-5">
            <h2 class="text-center mb-4">Your Monthly Bill</h2>
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php else: ?>
                <div class="card p-4 shadow-sm mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Bill Details</h4>
                        <?php if ($flat_no): ?>
                        <span class="badge bg-primary fs-6">
                            <?php echo "Flat No: " . htmlspecialchars($flat_no); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Flat Rent:</span>
                            <span><?php echo number_format($flatRent, 2) . " BDT"; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Garage Rent:</span>
                            <span><?php echo number_format($garageRent, 2) . " BDT"; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Estimated Utility Bill (for <?php echo htmlspecialchars($totalMembers); ?> members):</span>
                            <span><?php echo number_format($utilityBill, 2) . " BDT"; ?></span>
                        </li>
                    </ul>
                    <div class="card-footer mt-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Total Bill:</h5>
                        <h4 class="mb-0 text-success fw-bold">
                            <?php echo number_format($totalBill, 2) . " BDT"; ?>
                        </h4>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
