<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Users Detail</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- The CSS link is corrected to look in the same folder -->
    <link rel="stylesheet" href="adminstyle.css">
</head>
<body class="body">
    <?php include('navbar.php'); ?>
    
    <main>
    <section class="tenants2">
        <div class="tenants_box">
            <h1>Users Details</h1>
            <table class="tenants_table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Phone Number</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    require_once("DBconnect.php");
                    $sql = "SELECT name, email, role, phone_number FROM users";
                    $result = mysqli_query($conn, $sql);
                    if(mysqli_num_rows($result) > 0){
                        while($row = mysqli_fetch_array($result)){
                ?>
                <tr>
                    <td><?php echo $row["name"]; ?></td>
                    <td><?php echo $row["email"]; ?></td>
                    <td><?php echo $row["role"]; ?></td>
                    <td><?php echo $row["phone_number"]; ?></td>
                </tr>
                <?php
                        }
                    } else {
                ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No users found.</td>
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
