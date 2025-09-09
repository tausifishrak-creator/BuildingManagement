<?php
// Check if a session is not already active before starting one.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection file
include('DBconnect.php');

// Use session variables to retrieve error and success messages
$error_message = isset($_SESSION['error_message']) ? htmlspecialchars($_SESSION['error_message']) : '';
$success_message = isset($_SESSION['success_message']) ? htmlspecialchars($_SESSION['success_message']) : '';

// Clear the session messages after displaying them
unset($_SESSION['error_message']);
unset($_SESSION['success_message']);

// Fetch available flat numbers based on business logic
$flats = [];
$sql = "
    -- Select all flats that are currently vacant
    SELECT flat_no FROM flat_details WHERE status = 'Vacant'
    UNION
    -- Select flats that are occupied by students but not yet at capacity (4 students)
    SELECT flat_no FROM flat_details WHERE flat_no IN (
        SELECT flat_no FROM tenants WHERE tenant_type = 'Students'
        GROUP BY flat_no HAVING COUNT(*) < 4
    );
";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $flats[] = htmlspecialchars($row['flat_no']);
    }
    $stmt->close();
} else {
    $error_message = "Database query failed: " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Flats Detail</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- The CSS link is now corrected to look in the same folder -->
    <link rel="stylesheet" href="adminstyle.css">
                <style>
                .input-group {
                    position: relative;
                    display: flex;
                    align-items: center;
                    margin-bottom: 15px;
                }

                .input-icon {
                    position: absolute;
                    left: 12px;
                    color: rgba(255, 255, 255, 0.7);
                    pointer-events: none; /* Make the icon not clickable */
                }

                .text {
                    width: 100%;
                    border-radius: 8px;
                    border: 1px solid rgba(255, 255, 255, 0.3);
                    background: rgba(255, 255, 255, 0.2);
                    color: #fff;
                    outline: none;
                    transition: border-color 0.3s, box-shadow 0.3s;
                }

                .text::placeholder {
                    color: rgba(255, 255, 255, 0.7);
                }

                .text:focus {
                    border-color: #4a90e2;
                    box-shadow: 0 0 8px rgba(74, 144, 226, 0.5);
                }
                
                .button {
                    width: 100%;
                    padding: 12px;
                    border-radius: 8px;
                    border: none;
                    background: linear-gradient(90deg, #4a90e2, #6a5acd);
                    color: #fff;
                    font-weight: bold;
                    cursor: pointer;
                    transition: transform 0.2s, box-shadow 0.2s;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                }

                .button:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
                }

                .message {
                    text-align: center;
                    padding: 10px;
                    margin-bottom: 15px;
                    border-radius: 8px;
                    font-weight: bold;
                }

                .success {
                    background-color: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }

                .error {
                    background-color: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                }
                </style>
</head>
<body class="body">
    <?php include('navbar.php'); ?>

    <section class="tenants">
        <div class="tenants_add_box">
            <h2 style="font-size: 2rem; text-align: center; margin-bottom: 1.5rem; color: #fff; font-weight: bold;">Add New Tenants</h2>
            
            <?php if ($error_message): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="add_tenant_back.php">
                <select id="flat_no" class="select-text option" name="flat_no" required>
                    <option value="" disabled selected>Select Flat Number</option>
                    <?php foreach ($flats as $flat): ?>
                        <option value="<?php echo $flat; ?>"><?php echo $flat; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="tenant_name" class="text" placeholder="Full Name" required>
                <input type="text" name="phone_number" class="text" placeholder="Phone Number" required>
                <input type="text" name="email" class="text" placeholder="Email" required>
                <select id="tenant_type" class="select-text option" name="tenant_type" required>
                    <option value="" disabled selected>Select Tenant Type</option>
                    <option value="Family">Family</option>
                    <option value="Students">Students</option>
                </select>
                <input type="submit" class="button" value="Add">
            </form>
        </div>
    </section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
