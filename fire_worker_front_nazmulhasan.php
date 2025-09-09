<?php
// Check if a session is not already active before starting one.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection file
include('DBconnect.php');

$error_message = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$success_message = isset($_GET['success']) ? "Worker successfully removed!" : '';

// Fetch available workers (for dropdown selection)
$workers = [];
$stmt = $conn->prepare("SELECT serial_no, name FROM workers");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $workers[] = [
            'serial_no' => htmlspecialchars($row['serial_no']),
            'name' => htmlspecialchars($row['name'])
        ];
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
    <title>Fire Worker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
            pointer-events: none;
        }

        .text, .select {
            width: 100%;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            outline: none;
            padding: 10px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .text::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .text:focus, .select:focus {
            border-color: #e24a4a;
            box-shadow: 0 0 8px rgba(226, 74, 74, 0.5);
        }

        .button {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(90deg, #e24a4a, #c0392b);
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
    </style>
</head>
<body class="body">
    <?php include('navbar.php'); ?>

    <section class="tenants">
        <div class="tenants_add_box">
            <h2 style="font-size: 2rem; text-align: center; margin-bottom: 1.5rem; color: #fff; font-weight: bold;">Fire a Worker</h2>
            
            <?php if ($error_message): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="fire_worker_back_nazmulhasan.php">
                <select name="serial_no" class="select" required>
                    <option value="">Select Worker</option>
                    <?php foreach ($workers as $worker): ?>
                        <option value="<?php echo $worker['serial_no']; ?>">
                            <?php echo $worker['serial_no'] . " - " . $worker['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" class="button" value="Fire Worker">
            </form>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
