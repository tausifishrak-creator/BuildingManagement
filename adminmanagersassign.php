<?php
// Establish the database connection once at the very top of the page.
require_once("DBconnect.php");

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $flat_no = $_POST['flat_no'];
    $manager_nid = $_POST['manager_nid'];

    if (!empty($flat_no) && !empty($manager_nid)) {
        // Prepare an UPDATE statement to prevent SQL injection
        $stmt = $conn->prepare("UPDATE flat_details SET manager_nid = ? WHERE flat_no = ?");
        $stmt->bind_param("ss", $manager_nid, $flat_no);

        if ($stmt->execute()) {
            $message = "Manager assigned successfully to Flat " . htmlspecialchars($flat_no);
            $message_type = 'success';
        } else {
            $message = "Error assigning manager: " . $stmt->error;
            $message_type = 'danger';
        }
        $stmt->close();
    } else {
        $message = "Please select a flat and a manager.";
        $message_type = 'danger';
    }
}

// Fetch all managers to populate the dropdown
$managers = [];
$sql_managers = "SELECT nid, name FROM manager";
$result_managers = $conn->query($sql_managers);
if ($result_managers && $result_managers->num_rows > 0) {
    while ($row = $result_managers->fetch_assoc()) {
        $managers[] = $row;
    }
}

// Fetch all flats to populate the dropdown
$flats = [];
$sql_flats = "SELECT flat_no FROM flat_details";
$result_flats = $conn->query($sql_flats);
if ($result_flats && $result_flats->num_rows > 0) {
    while ($row = $result_flats->fetch_assoc()) {
        $flats[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Manager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="adminstyle.css">
    <style>
        .text option {
            background-color: #fff;
            color: #000;
        }
    </style>
</head>
<body class="body">
    <?php include('navbar.php'); ?>

    <section class="tenants">
        <div class="tenants_add_box">
            <h2 style="font-size: 2rem; text-align: center; margin-bottom: 1.5rem; color: #fff; font-weight: bold;">Assign Manager</h2>
            
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if (empty($managers) || empty($flats)): ?>
                <div class="message error">No managers or flats available to assign. Please add them first.</div>
            <?php else: ?>
                <form method="POST" action="adminmanagersassign.php">
                    <div class="input-group mb-3">
                        <select name="flat_no" class="text" required>
                            <option value="">Select Flat</option>
                            <?php foreach ($flats as $flat): ?>
                                <option value="<?php echo htmlspecialchars($flat['flat_no']); ?>"><?php echo htmlspecialchars($flat['flat_no']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group mb-3">
                        <select name="manager_nid" class="text" required>
                            <option value="">Select Manager</option>
                            <?php foreach ($managers as $manager): ?>
                                <option value="<?php echo htmlspecialchars($manager['nid']); ?>">
                                    <?php echo htmlspecialchars($manager['name']) . " (NID: " . htmlspecialchars($manager['nid']) . ")"; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <input type="submit" class="button" value="Assign Manager">
                </form>
            <?php endif; ?>
        </div>
    </section>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php 
// Close the database connection at the very end of the script.
$conn->close();
?>
