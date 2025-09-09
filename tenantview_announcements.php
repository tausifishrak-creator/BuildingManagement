<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - Building Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="adminstyle.css">
</head>
<body class="body">
    <?php include('navbartenant.php'); ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
            <h2 class="text-white text-center mb-4">Building Announcements</h2>
                <?php
                    // Fetch and display announcements
                    include('DBconnect.php');
                    $sql = "SELECT * FROM announcements ORDER BY created_at DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="card mb-4 shadow-sm">';
                            echo '    <div class="card-body">';
                            echo '        <h5 class="card-title text-primary">' . htmlspecialchars($row['title']) . '</h5>';
                            echo '        <h6 class="card-subtitle mb-2 text-muted">Posted: ' . date('F j, Y, g:i a', strtotime($row['created_at'])) . '</h6>';
                            echo '        <p class="card-text">' . nl2br(htmlspecialchars($row['message'])) . '</p>';
                            echo '    </div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="alert alert-info text-center">No announcements have been posted yet.</div>';
                    }
                    if ($conn) {
                        $conn->close();
                    }
                ?>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
