<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Total Workers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="adminstyle.css">
</head>
<body class="body">
    <?php include('navbar.php'); ?>
    
    <main>
    <section class="tenants2">
        <div class="tenants_box">
            <h1>Total Workers</h1>
            <table class="tenants_table">
                <thead>
                    <tr>
                        <th>Serial No</th>
                        <th>Name</th>
                        <th>Post</th>
                        <th>Phone Number</th>
                        <th>Wages</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    require_once("DBconnect.php");
                    $sql = "SELECT serial_no, name, post, phone_number, wages FROM workers";
                    $result = mysqli_query($conn, $sql);
                    
                    if(mysqli_num_rows($result) > 0){
                        while($row = mysqli_fetch_assoc($result)){
                ?>
                <tr>
                    <td><?php echo $row["serial_no"]; ?></td>
                    <td><?php echo $row["name"]; ?></td>
                    <td><?php echo $row["post"]; ?></td>
                    <td><?php echo $row["phone_number"]; ?></td>
                    <td><?php echo $row["wages"]; ?></td>
                </tr>
                <?php
                        }
                    } else {
                ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No workers found.</td>
                </tr>
                <?php
                    }
                ?>
                </tbody>
            </table>
        </div>
    </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
