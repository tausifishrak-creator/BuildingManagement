<?php
// PHP code to count pending complaints.
// It connects to the database and runs a query. It DOES NOT close the connection.
include('DBconnect.php');

$pending_complaints = 0;
// Check if $conn is valid before using it
if ($conn) {
    $sql_count = "SELECT COUNT(*) AS pending_count FROM complaints WHERE status = 'pending'";
    $result_count = $conn->query($sql_count);

    if ($result_count) {
        $row_count = $result_count->fetch_assoc();
        $pending_complaints = $row_count['pending_count'];
    }
}
?>

<!-- Start of Navbar Code -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="adminhome.php">
            <i class="bi bi-building"></i> Building Management
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="adminhome.php"><i class="bi bi-house-door-fill"></i> Home</a>
                </li>
                <!-- Manage Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarManageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-tools"></i> Manage
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarManageDropdown">
                        <li><a class="dropdown-item" href="add_tenant_front.php"><i class="bi bi-person-plus-fill"></i> Add Tenant</a></li>
                        <li><a class="dropdown-item" href="remove_tenant_front_zihadhasan.php"><i class="bi bi-person-x-fill"></i> Remove Tenant</a></li>
                        <li><a class="dropdown-item" href="add_manager_front.php"><i class="bi bi-person-check-fill"></i> Hire Manager</a></li>
                        <li><a class="dropdown-item" href="fire_manager_front_zihadhasan.php"><i class="bi bi-person-x-fill"></i> Fire Manager</a></li>
                        <li><a class="dropdown-item" href="add_worker_front_nazmulhasan.php"><i class="bi bi-person-check-fill"></i> Hire Worker</a></li>
                        <li><a class="dropdown-item" href="fire_worker_front_nazmulhasan.php"><i class="bi bi-person-x-fill"></i> Fire Worker</a></li>
                        <li><a class="dropdown-item" href="adminmanagersassign.php"><i class="bi bi-person-up"></i> Assign Managers</a></li>
                        <li><a class="dropdown-item" href="A-login.php"><i class="bi bi-person-x-fill"></i> Add another Admin</a></li>
                    </ul>
                </li>
                
                <!-- Complaints Link with Notification Badge -->
                <li class="nav-item">
                    <a class="nav-link" href="admincomplaints.php">
                        <i class="bi bi-chat-dots-fill"></i> Complaints
                        <?php if ($pending_complaints > 0): ?>
                            <span class="badge rounded-pill bg-danger ms-1">
                                <?php echo $pending_complaints; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="adminpublicmessage.php"><i class="bi bi-megaphone-fill"></i> Public Message</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="login.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End of Navbar Code -->
