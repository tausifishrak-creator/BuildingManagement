<?php
require_once("DBconnect.php");

// Check if the connection failed
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch all existing renters
$sql = "SELECT id, name FROM non_tenants ORDER BY name ASC";
$result = mysqli_query($conn, $sql);

$renters = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $renters[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Remove Renter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="adminstyle.css">
</head>
<body class="body">
    <?php include('navbar.php'); ?>
    
    <main>
        <section class="tenants2">
            <div class="tenants_box">
                <h1>Remove Renter</h1>
                <form action="process_remove_renter.php" method="POST">
                    <div class="mb-3">
                        <label for="renter_id" class="form-label">Select Renter ID</label>
                        <select id="renter_id" class="form-select" name="renter_id" required>
                            <option value="" disabled selected>Select an ID</option>
                            <?php foreach ($renters as $renter): ?>
                                <option value="<?php echo $renter['id']; ?>"><?php echo htmlspecialchars($renter['id']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Renter Name</label>
                        <input type="text" class="form-control" id="name" name="name" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" disabled></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="garage_spot_label" class="form-label">Garage Spot</label>
                        <input type="text" class="form-control" id="garage_spot_label" name="garage_spot_label" disabled>
                    </div>
                    <button type="submit" class="btn btn-danger">Remove Renter</button>
                    <a href="admingarage.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('renter_id').addEventListener('change', function() {
            var renterId = this.value;
            if (renterId) {
                // Use fetch to get renter data
                fetch('fetch_renter_data.php?id=' + renterId)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            document.getElementById('name').value = data.name;
                            document.getElementById('phone_number').value = data.phone_number;
                            document.getElementById('address').value = data.address;
                            document.getElementById('email').value = data.email;
                            document.getElementById('garage_spot_label').value = data.garage_spot_label;
                        }
                    })
                    .catch(error => console.error('Error fetching data:', error));
            } else {
                // Clear the fields if no renter is selected
                document.getElementById('name').value = '';
                document.getElementById('phone_number').value = '';
                document.getElementById('address').value = '';
                document.getElementById('email').value = '';
                document.getElementById('garage_spot_label').value = '';
            }
        });
    </script>
</body>
</html>
