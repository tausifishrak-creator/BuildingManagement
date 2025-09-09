<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('DBconnect.php');

$error_message = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$success_message = isset($_GET['success']) ? "Tenant successfully removed!" : '';

// Fetch tenants with their names and phone numbers
$tenants = [];
$stmt = $conn->prepare("SELECT flat_no, tenant_name, phone_number FROM tenants");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $tenants[] = [
            'flat_no' => htmlspecialchars($row['flat_no']),
            'name' => htmlspecialchars($row['tenant_name']),
            'phone_number' => htmlspecialchars($row['phone_number'])
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
    <title>Remove Tenant</title>
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
        .text, .select-text {
            width: 100%;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
            padding: 8px;
        }
        .text::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .text:focus, .select-text:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 8px rgba(74, 144, 226, 0.5);
        }
        .button {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(90deg, #e24a4a, #6a1b1b);
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
            <h2 style="font-size: 2rem; text-align: center; margin-bottom: 1.5rem; color: #fff; font-weight: bold;">
                Remove Tenant
            </h2>

            <?php if ($error_message): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="remove_tenant_back_zihadhasan.php">
                <select id="tenant_info" class="select-text option" name="tenant_info" required>
                    <option value="" disabled selected>Select Tenant</option>
                    <?php foreach ($tenants as $tenant): ?>
                        <option value="<?php echo $tenant['phone_number']; ?>">
                            <?php echo $tenant['name'] . " (" . $tenant['phone_number'] . ")"; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="submit" class="button" value="Remove Tenant">
            </form>
        </div>
    </section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
