<?php
require_once("DBconnect.php");

// Check if the connection failed
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch all available garage spots
$sql = "SELECT spot_label FROM garage WHERE status = 'For Rent'";
$result = mysqli_query($conn, $sql);

$availableSpots = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $availableSpots[] = $row['spot_label'];
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Renter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="adminstyle.css">
    <style>
        /* This CSS removes the increment arrows from number inputs in all browsers */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>
</head>
<body class="body">
    <?php include('navbar.php'); ?>
    
    <main>
        <section class="tenants2">
            <div class="tenants_box">
                <h1>Add New Renter</h1>
                <form action="process_add_renter.php" method="POST">
                    <div class="mb-3">
                        <label for="id" class="form-label">NID</label>
                        <input type="number" class="form-control" id="id" name="id" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Renter Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="garage_spot_label" class="form-label">Select Garage Spot</label>
                        <select id="garage_spot_label" class="form-select" name="garage_spot_label" required>
                            <option value="" disabled selected>Select a Spot</option>
                            <?php foreach ($availableSpots as $spot): ?>
                                <option value="<?php echo $spot; ?>"><?php echo $spot; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Renter</button>
                    <a href="admingarage.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
