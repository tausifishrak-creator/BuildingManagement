<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include DB connection
include('DBconnect.php');

$error_message = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$success_message = isset($_GET['success']) ? "Manager successfully removed!" : '';

// Fetch managers list (NID + Name) to allow selection
$managers = [];
$stmt = $conn->prepare("SELECT nid, name FROM manager");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $managers[] = [
            'nid' => htmlspecialchars($row['nid']),
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
    <title>Fire Manager</title>
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
        .text {
            width: 100%;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.2);
            color: #000;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
            padding: 10px;
        }
        .text::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .text:focus {
            border-color: #ff4c4c;
            box-shadow: 0 0 8px rgba(255, 76, 76, 0.5);
        }
        .button {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(90deg, #ff4c4c, #b22222);
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
            <h2 style="font-size: 2rem; text-align: center; margin-bottom: 1.5rem; color: #fff; font-weight: bold;">Fire Manager</h2>
            
            <?php if ($error_message): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="fire_manager_back_zihadhasan.php">
                <select name="nid" class="text" required>
                    <option value="">Select Manager to Remove</option>
                    <?php foreach ($managers as $manager): ?>
                        <option value="<?php echo $manager['nid']; ?>">
                            <?php echo $manager['name'] . " (NID: " . $manager['nid'] . ")"; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" class="button" value="Fire Manager">
            </form>
        </div>
    </section>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
