<?php
// Establish the database connection once at the very top of the page.
require_once("DBconnect.php");

// It's good practice to check if the connection failed
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Garage Spot Detail</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- The CSS link is now corrected to look in the same folder -->
    <link rel="stylesheet" href="adminstyle.css">
    <style>
        .notification-badge {
            position: absolute;
            top: 10px;
            right: 15px;
            padding: 5px 8px;
            border-radius: 50%;
            background: red;
            color: white;
            font-size: 0.75rem;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .tenants_box {
            margin-bottom: 40px;
        }
    </style>
</head>
<body class="body">
    <?php include('navbar.php'); ?>
    
    <main>
        <section class="tenants2">
            <!-- Garage Spots Table -->
            <div class="tenants_box">
                <h1>Garage Spots</h1>
                <!-- Buttons to add/remove renters -->
                <div class="action-buttons">
                    <a href="addrenter.php" class="btn btn-primary">Add Renter</a>
                    <a href="removerenter.php" class="btn btn-danger">Remove Renter</a>
                </div>
                <table class="tenants_table">
                    <thead>
                        <tr>
                            <th>Spot Label</th>
                            <th>Rent</th>
                            <th>Status</th>
                            <th>Designated Flat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Corrected SQL query to select all columns and order by the numeric part of the spot_label
                        // This new method first sorts by the length of the number, then by the number itself.
                        $sql_vacant = "SELECT * FROM garage ORDER BY LENGTH(SUBSTRING_INDEX(spot_label, '-', -1)), CAST(SUBSTRING_INDEX(spot_label, '-', -1) AS UNSIGNED) ASC";
                        $result_vacant = mysqli_query($conn, $sql_vacant);

                        if (mysqli_num_rows($result_vacant) > 0) {
                            while($row = mysqli_fetch_array($result_vacant)){
                        ?>
                                <tr>
                                    <td><?php echo $row["spot_label"]; ?></td>
                                    <td><?php echo $row["rent"]; ?></td>
                                    <td><?php echo $row["status"]; ?></td>
                                    <td><?php echo $row["flat_no"]; ?></td>
                                </tr>
                        <?php
                            }
                        } 
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Renters Table -->
            <div class="tenants_box">
                <h1>Renters</h1>
                <table class="tenants_table">
                    <thead>
                        <tr>
                            <th>NID</th>
                            <th>Renter Name</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Garage Spot</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // SQL query to fetch all data from the non_tenants table
                        $sql_renters = "SELECT * FROM non_tenants";
                        $result_renters = mysqli_query($conn, $sql_renters);

                        if (mysqli_num_rows($result_renters) > 0) {
                            while($row_renter = mysqli_fetch_array($result_renters)){
                        ?>
                                <tr>
                                    <td><?php echo $row_renter["id"]; ?></td>
                                    <td><?php echo $row_renter["name"]; ?></td>
                                    <td><?php echo $row_renter["phone_number"]; ?></td>
                                    <td><?php echo $row_renter["address"]; ?></td>
                                    <td><?php echo $row_renter["email"]; ?></td>
                                    <td><?php echo $row_renter["garage_spot_label"]; ?></td>
                                </tr>
                        <?php
                            }
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <!-- Add Bootstrap JavaScript bundle for dropdown functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Close the database connection at the very end of the script.
$conn->close();
?>
