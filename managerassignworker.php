<?php
// Start session to access user data
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
$flats = [];
$workers = [];
$success_message = '';
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

    // Step 2: Handle form submission to assign a worker
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($manager_nid)) {
        $flat_no = $_POST['flat_no'] ?? '';
        $worker_nid = $_POST['worker_nid'] ?? ''; // This variable is a bit of a misnomer, as it holds the worker's phone number.

        if (!empty($flat_no) && !empty($worker_nid)) {
            // Check if the selected flat belongs to the current manager to prevent unauthorized assignments
            $stmt_check_flat = $conn->prepare("SELECT flat_no FROM flat_details WHERE flat_no = ? AND manager_nid = ?");
            $stmt_check_flat->bind_param("ss", $flat_no, $manager_nid);
            $stmt_check_flat->execute();
            $result_check_flat = $stmt_check_flat->get_result();

            if ($result_check_flat->num_rows > 0) {
                // Update the worker's flat number in the 'post' column in the database
                $stmt_update = $conn->prepare("UPDATE workers SET post = ? WHERE phone_number = ?");
                $stmt_update->bind_param("ss", $flat_no, $worker_nid);
                
                if ($stmt_update->execute()) {
                    $success_message = "Worker successfully assigned to flat " . htmlspecialchars($flat_no);
                } else {
                    $error_message = "Failed to assign worker. Please try again.";
                }
                $stmt_update->close();
            } else {
                $error_message = "Invalid flat selection. You can only assign workers to flats you manage.";
            }
            $stmt_check_flat->close();
        } else {
            $error_message = "Please select both a flat and a worker.";
        }
    }

    // Step 3: Fetch the list of flats and workers for the form dropdowns
    if (!empty($manager_nid)) {
        // Fetch all flats managed by the current manager
        $stmt_flats = $conn->prepare("SELECT flat_no, status FROM flat_details WHERE manager_nid = ? ORDER BY flat_no");
        $stmt_flats->bind_param("s", $manager_nid);
        $stmt_flats->execute();
        $result_flats = $stmt_flats->get_result();
        while ($row = $result_flats->fetch_assoc()) {
            $flats[] = $row;
        }
        $stmt_flats->close();

        // Fetch all unassigned workers from the `workers` table
        $stmt_workers = $conn->prepare("SELECT phone_number, name FROM workers WHERE post IS NULL ORDER BY name");
        $stmt_workers->execute();
        $result_workers = $stmt_workers->get_result();
        while ($row = $result_workers->fetch_assoc()) {
            $workers[] = $row;
        }
        $stmt_workers->close();
    }
} catch (Exception $e) {
    $error_message = 'An error occurred: ' . htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Worker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="adminstyle.css">
    <style>
        .text, .select-text {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .select-text {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="%23FFFFFF" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M7 10l5 5 5-5z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
        }

        .text::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .text:focus, .select-text:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 8px rgba(74, 144, 226, 0.5);
        }

        .select-text option {
            color: #000;
            background: #fff;
        }
    </style>
</head>
<body class="body">
    <?php include('navbarmanager.php'); ?>

    <section class="tenants">
        <div class="tenants_add_box">
            <h2 style="font-size: 2rem; text-align: center; margin-bottom: 1.5rem; color: #fff; font-weight: bold;">Assign Worker</h2>
            <p style="text-align: center; color: #fff; margin-bottom: 1.5rem;">Use this form to assign an available worker to one of your managed flats.</p>
            
            <?php if ($success_message): ?>
                <div class="message success"><?= $success_message; ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="message error"><?= $error_message; ?></div>
            <?php endif; ?>

            <?php if (empty($manager_nid)): ?>
                <div class="message error">You must be a verified manager to use this page.</div>
            <?php else: ?>
                <form action="managerassignworker.php" method="POST">
                    <select id="flat_no" class="select-text option" name="flat_no" required>
                        <option value="" selected disabled>Select Flat Number</option>
                        <?php if (!empty($flats)): ?>
                            <?php foreach ($flats as $flat): ?>
                                <option value="<?= htmlspecialchars($flat['flat_no']); ?>">
                                    Flat <?= htmlspecialchars($flat['flat_no']); ?> (Status: <?= htmlspecialchars($flat['status']); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No flats found under your management.</option>
                        <?php endif; ?>
                    </select>

                    <select id="worker_nid" class="select-text option" name="worker_nid" required>
                        <option value="" selected disabled>Select Worker</option>
                        <?php if (!empty($workers)): ?>
                            <?php foreach ($workers as $worker): ?>
                                <option value="<?= htmlspecialchars($worker['phone_number']); ?>">
                                    <?= htmlspecialchars($worker['name']); ?> (Phone: <?= htmlspecialchars($worker['phone_number']); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No available workers found.</option>
                        <?php endif; ?>
                    </select>
                    
                    <button type="submit" class="button">Assign Worker</button>
                </form>
            <?php endif; ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Close the database connection
$conn->close();
?>
