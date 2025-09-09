<?php
// Start the session to access user data
session_start();

// Include the database connection file
include('DBconnect.php');

$message = '';
$is_admin = false;

// Check if a user is logged in and their role is admin
if (isset($_SESSION['phone_number']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $is_admin = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_message']) && $is_admin) {
    $title = htmlspecialchars($_POST['title']);
    $announcement_message = htmlspecialchars($_POST['message']);

    if (!empty($title) && !empty($announcement_message)) {
        // Retrieve the user's email from the database using their phone number
        $phone_number = $_SESSION['phone_number'];
        $stmt_email = $conn->prepare("SELECT email FROM users WHERE phone_number = ?");
        $stmt_email->bind_param("s", $phone_number);
        $stmt_email->execute();
        $result_email = $stmt_email->get_result();

        if ($result_email->num_rows > 0) {
            $user_data = $result_email->fetch_assoc();
            $user_email = $user_data['email'];

            // Now, use the retrieved email to insert the announcement
            $stmt = $conn->prepare("INSERT INTO announcements (title, message, user_email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $announcement_message, $user_email);

            if ($stmt->execute()) {
                $message = '<div class="alert alert-success" role="alert">Announcement posted successfully!</div>';
            } else {
                $message = '<div class="alert alert-danger" role="alert">Error: ' . $stmt->error . '</div>';
            }
            $stmt->close();
        } else {
            $message = '<div class="alert alert-danger" role="alert">Error: Could not find user email for the given phone number.</div>';
        }
        $stmt_email->close();
    } else {
        $message = '<div class="alert alert-warning" role="alert">Please fill in both the title and message.</div>';
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$is_admin) {
    // Message for unauthorized access on form submission
    $message = '<div class="alert alert-danger" role="alert">Access Denied. You must be an administrator to post announcements.</div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Public Message - Building Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="adminstyle.css">
</head>
<body class="body">
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card form-container p-4 shadow-lg">
                    <h2 class="card-title text-center mb-4">Post a Public Announcement</h2>
                    <?php 
                    if (!$is_admin && !isset($_SESSION['phone_number'])) {
                        echo '<div class="alert alert-danger" role="alert">You must be logged in to access this page.</div>';
                    } else if (!$is_admin && isset($_SESSION['phone_number'])) {
                        echo '<div class="alert alert-warning" role="alert">You do not have the necessary permissions to post announcements.</div>';
                    } else {
                        echo $message;
                    }
                    ?>
                    <?php if ($is_admin): ?>
                    <form action="adminpublicmessage.php" method="POST">
                        <div class="mb-3">
                            <label for="title" class="form-label">Announcement Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message Content</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" name="post_message" class="btn btn-primary w-100">Post Announcement</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Section to display and remove existing announcements -->
    <div class="container mt-5 p-4 rounded" style="background-color: rgba(255, 255, 255, 0.8);">
        <h2 class="text-center mb-4" style="color: #212529;">Manage Existing Announcements</h2>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <?php
                    // Re-establish connection for this section
                    include('DBconnect.php');
                    $sql = "SELECT id, title, message, created_at FROM announcements ORDER BY created_at DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="card mb-3 shadow-sm">';
                            echo '     <div class="card-body">';
                            echo '        <h5 class="card-title text-primary">' . htmlspecialchars($row['title']) . '</h5>';
                            echo '        <h6 class="card-subtitle mb-2 text-muted">Posted: ' . date('F j, Y, g:i a', strtotime($row['created_at'])) . '</h6>';
                            echo '        <p class="card-text">' . nl2br(htmlspecialchars($row['message'])) . '</p>';
                            
                            // Only show the remove button to admins
                            if ($is_admin) {
                                echo '        <form action="remove_announcement.php" method="POST">';
                                echo '            <input type="hidden" name="id" value="' . $row['id'] . '">';
                                echo '            <button type="submit" class="btn btn-danger btn-sm float-end"><i class="bi bi-trash-fill"></i> Remove</button>';
                                echo '        </form>';
                            }
                            echo '     </div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="alert alert-info text-center">No announcements to display.</div>';
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
