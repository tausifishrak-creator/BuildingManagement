<?php
// Enable error reporting (for debugging — disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Make sure there is NO output before this point.
// Including the database connection file first is good practice.
include('DBconnect.php');

// Initialize a variable to hold any error messages
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize user input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $retype_password = $_POST['retype_password'];
    $phone = trim($_POST['phone']);
    $role = trim($_POST['role']);

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($retype_password) || empty($phone) || empty($role)) {
        $error_message = "All fields are required.";
    } elseif ($password !== $retype_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Hash the password before storing it in the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare statement to insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, phone_number) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $hashed_password, $role, $phone);

        if ($stmt->execute()) {
            // Redirect to a success page or login page
            header("Location: login.php?success=1");
            // It is critical to call exit() or die() after a header redirect
            exit();
        } else {
            // Handle insertion error
            $error_message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <!-- This CSS link is relative to the PHP file's location -->
        <link rel="stylesheet" href="style.css"> 
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
        href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap"
        rel="stylesheet"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <title>BuildingManager ∣ Sign Up</title>
        <style>
            .input-group {
                position: relative;
                display: flex;
                align-items: center;
                margin-bottom: 15px;
            }

            .input-icon {
                position: absolute;
                left: 12px;
                color: rgba(255, 255, 255, 0.7);
                pointer-events: none; /* Make the icon not clickable */
            }

            .text {
                width: 100%;
                padding: 12px 12px 12px 40px; /* Adjust padding for icon */
                border-radius: 8px;
                border: 1px solid rgba(255, 255, 255, 0.3);
                background: rgba(255, 255, 255, 0.2);
                color: #fff;
                outline: none;
                transition: border-color 0.3s, box-shadow 0.3s;
            }

            .text::placeholder {
                color: rgba(255, 255, 255, 0.7);
            }

            .text:focus {
                border-color: #4a90e2;
                box-shadow: 0 0 8px rgba(74, 144, 226, 0.5);
            }
            
            .select-text {
                width: 100%;
                padding: 12px 12px 12px 40px; /* Adjust padding for icon */
                border-radius: 8px;
                border: 1px solid rgba(255, 255, 255, 0.3);
                background: rgba(255, 255, 255, 0.2);
                color: #fff;
                outline: none;
                transition: border-color 0.3s, box-shadow 0.3s;
                appearance: none;
                -webkit-appearance: none;
                background-image: url('data:image/svg+xml;utf8,<svg fill="%23FFFFFF" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M7 10l5 5 5-5z"/></svg>');
                background-repeat: no-repeat;
                background-position: right 10px center;
            }
            
            /* This rule ensures the option text is black for readability */
            .select-text option {
                color: #000;
            }

            .button {
                width: 100%;
                padding: 12px;
                border-radius: 8px;
                border: none;
                background: linear-gradient(90deg, #4a90e2, #6a5acd);
                color: #fff;
                font-weight: bold;
                cursor: pointer;
                transition: transform 0.2s, box-shadow 0.2s;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }

            .button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            }
        </style>
    </head>
    <body>
        <div class="topL">
            <div style="font-size:50px;"><b>BuildingManager</b></div> 
            <div class="signup_button"><a href="login.php" style="color: black; text-decoration: none;">Log in</a></div>
        </div>
        <div class="signup_bar">
            <h2 style="font-size: 2rem; text-align: center; margin-bottom: 1.5rem; color: #fff; font-weight: bold;">Sign Up</h2>
            
            <?php if (!empty($error_message)): ?>
                <p style="color:red; text-align: center;"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            
            <form method="POST" action="A-login.php">
                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="name" class="text" placeholder="Name" required>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="text" name="email" class="text" placeholder="Email" required>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" class="text" placeholder="Password" required>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="retype_password" class="text" placeholder="Retype Password" required>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-phone input-icon"></i>
                    <input type="text" name="phone" class="text" placeholder="Phone Number" required>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-user-tag input-icon"></i>
                    <select name="role" class="select-text" required>
                    <option value="" disabled selected>Select Role</option>
                    <option value="admin">Admin</option>
                    </select>
                </div>
                
                <input type="submit" class="button" value="Sign up">
            </form>
        </div>
    </body>
</html>
                    