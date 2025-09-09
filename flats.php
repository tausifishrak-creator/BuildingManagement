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
    </style>
</head>
<body class="body">
    <?php include('navbar.php'); ?>
    
    <main>
        <section class="tenants2">
            <div class="tenants_box">
                <h1>Flats</h1>
                <table class="tenants_table">
                    <thead>
                        <tr>
                            <th>Flat-no</th>
                            <th>Area</th>
                            <th>No. of Rooms</th>
                            <th>Rent</th>
                            <th>Status</th>
                            <th>Manager NID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require_once("DBconnect.php");

                        // It's good practice to check if the connection failed
                        if (!$conn) {
                            die("Database connection failed: " . mysqli_connect_error());
                        }

                        // Corrected SQL query to select all columns
                        $sql_vacant = "SELECT * FROM flat_details";
                        $result_vacant = mysqli_query($conn, $sql_vacant);

                        if (mysqli_num_rows($result_vacant) > 0) {
                            while($row = mysqli_fetch_array($result_vacant)){
                        ?>
                                <tr>
                                    <td><?php echo $row["flat_no"]; ?></td>
                                    <td><?php echo $row["area"]; ?></td>
                                    <td><?php echo $row["number_of_rooms"]; ?></td>
                                    <td><?php echo $row["rent"]; ?></td>
                                    <td><?php echo $row["status"]; ?></td>
                                    <!-- This is the new cell with the conditional logic -->
                                    <td>
                                        <?php 
                                            // Check if the manager_nid is not set or is null
                                            if (empty($row["manager_nid"])) {
                                                echo 'Not Assigned';
                                            } else {
                                                echo $row["manager_nid"];
                                            }
                                        ?>
                                    </td>
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
