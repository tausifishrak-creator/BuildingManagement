<?php
// PHP code to count pending complaints.
// It connects to the database and runs a query. It DOES NOT close the connection.
include('DBconnect.php');

$announcement = 0;
// Check if $conn is valid before using it
if ($conn) {
    $sql_count = "SELECT COUNT(*) AS announcement FROM announcements";
    $result_count = $conn->query($sql_count);

    if ($result_count) {
        $row_count = $result_count->fetch_assoc();
        $announcement = $row_count['announcement'];
    }
}
?>


<!-- Start of Navbar Code -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="manager.php">
            <i class="bi bi-building"></i> Building Management
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="manager.php"><i class="bi bi-house-door-fill"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="managerview_announcements.php"><i class="bi bi-megaphone-fill"></i> Announcement
                    <?php if ($announcement > 0): ?>
                            <span class="badge rounded-pill bg-danger ms-1">
                                <?php echo $announcement; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>



                <li class="nav-item">
                    <a class="nav-link text-light" href="login.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End of Navbar Code -->