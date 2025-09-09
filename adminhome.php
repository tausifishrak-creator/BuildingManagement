<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("DBconnect.php");

// Restrict to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Count queries
$totalUsers = $conn->query("SELECT COUNT(*) AS total_users FROM `users`")->fetch_assoc()['total_users'] ?? 0;
$totalTenants = $conn->query("SELECT COUNT(*) AS total_tenants FROM `tenants`")->fetch_assoc()['total_tenants'] ?? 0;
$totalManagers = $conn->query("SELECT COUNT(*) AS total_managers FROM `manager`")->fetch_assoc()['total_managers'] ?? 0;
$totalWorkers = $conn->query("SELECT COUNT(*) AS total_workers FROM `workers`")->fetch_assoc()['total_workers'] ?? 0;
$totalflats = $conn->query("SELECT COUNT(*) AS total_flats FROM `flat_details`")->fetch_assoc()['total_flats'] ?? 0;
$totalgarage = $conn->query("SELECT COUNT(*) AS total_garage FROM `garage`")->fetch_assoc()['total_garage'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="adminstyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap"
        rel="stylesheet"
    />
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

<div class="container mt-5">
    <div class="card shadow p-4">
        <h2 class="mb-3">Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p class="mb-4">This is the admin dashboard.</p>

        <div class="row row-cols-1 row-cols-md-3 g-4">

            <!-- Total Users -->
            <div class="col">
                <a href="adminusers.php" class="text-decoration-none">
                    <div class="card text-white bg-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title">Total Users</h5>
                                <i class="fa-solid fa-users fa-2x"></i>
                            </div>
                            <p class="card-text fs-3"><?= $totalUsers ?></p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Total Tenants -->
            <div class="col">
                <a href="tenantforadminview.php" class="text-decoration-none">
                    <div class="card text-white bg-info h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title">Total Tenants</h5>
                                <i class="fa-solid fa-house-user fa-2x"></i>
                            </div>
                            <p class="card-text fs-3"><?= $totalTenants ?></p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Total Managers -->
            <div class="col">
                <a href="adminmanager.php" class="text-decoration-none">
                    <div class="card text-white bg-success h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title">Total Managers</h5>
                                <i class="fa-solid fa-user-tie fa-2x"></i>
                            </div>
                            <p class="card-text fs-3"><?= $totalManagers ?></p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Total Workers -->
            <div class="col">
                <a href="total_workers_nazmulhasan.php" class="text-decoration-none">
                    <div class="card text-white bg-warning h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title">Total Workers</h5>
                                <i class="fa-solid fa-hard-hat fa-2x"></i>
                            </div>
                            <p class="card-text fs-3"><?= $totalWorkers ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Flats Status -->
            <div class="col">
                <a href="flats.php" class="text-decoration-none">
                    <div class="card text-white bg-secondary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title">Flats Status</h5>
                                <i class="fa-solid fa-building fa-2x"></i>
                            </div>
                            <p class="card-text fs-3"><?= $totalflats ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <!-- garage status -->
            <div class="col">
                <a href="admingarage.php" class="text-decoration-none">
                    <div class="card text-white bg-dark h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title">Garage Status</h5>
                                <i class="fa-solid fa-car fa-2x"></i>
                            </div>
                            <p class="card-text fs-3"><?= $totalgarage ?></p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>