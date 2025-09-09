<?php
// Enable error reporting (for debugging — disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if a session is not already active before starting one.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection file
include('DBconnect.php');

// Initialize a variable to hold any error messages
$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize user input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $retype_password = $_POST['retype_password'];
    $role = trim($_POST['role']);
    $phone_number = null; // Initialize phone variable

    // Basic validation, phone field is now removed from validation
    if (empty($name) || empty($email) || empty($password) || empty($retype_password) || empty($role)) {
        $error_message = "All fields are required.";
    } elseif ($password !== $retype_password) {
        $error_message = "Passwords do not match.";
    } else {
        // --- Check if email already exists in the users table ---
        $check_user_stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        if ($check_user_stmt) {
            $check_user_stmt->bind_param("s", $email);
            $check_user_stmt->execute();
            $result = $check_user_stmt->get_result();
            $row = $result->fetch_array();
            $user_count = $row[0];
            $check_user_stmt->close();
            
            if ($user_count > 0) {
                $error_message = "This email is already registered. Please use a different email or log in.";
            }
        } else {
            $error_message = "Database query failed: " . $conn->error;
        }

        // Only proceed if no email-in-use error was found
        if (empty($error_message)) {
            // Check if the user's role is 'tenant' and if their email exists in the tenants table.
            if ($role === 'tenant') {
                $check_tenant_stmt = $conn->prepare("SELECT phone_number FROM tenants WHERE email = ?");
                if ($check_tenant_stmt) {
                    $check_tenant_stmt->bind_param("s", $email);
                    $check_tenant_stmt->execute();
                    $result = $check_tenant_stmt->get_result();
                    $row = $result->fetch_assoc();
                    $check_tenant_stmt->close();
                    
                    if ($row) {
                        $phone_number = $row['phone_number'];
                    } else {
                        $error_message = "You cannot sign up as a tenant unless your email is pre-registered in the tenant's database. Please contact an administrator.";
                    }
                } else {
                    $error_message = "Database query failed: " . $conn->error;
                }
            } elseif ($role === 'manager') {
                // Check if the user's role is 'manager' and if their email exists in the manager table.
                $check_manager_stmt = $conn->prepare("SELECT phone_number FROM manager WHERE email = ?");
                if ($check_manager_stmt) {
                    $check_manager_stmt->bind_param("s", $email);
                    $check_manager_stmt->execute();
                    $result = $check_manager_stmt->get_result();
                    $row = $result->fetch_assoc();
                    $check_manager_stmt->close();
                    
                    if ($row) {
                        $phone_number = $row['phone_number'];
                    } else {
                        $error_message = "You cannot sign up as a manager unless your email is pre-registered in the manager's database. Please contact an administrator.";
                    }
                } else {
                    $error_message = "Database query failed: " . $conn->error;
                }
            }
        }

        // If all checks pass and a phone number was found, proceed with user insertion.
        if (empty($error_message) && $phone_number !== null) {
            // Hash the password before storing it in the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare statement to insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, phone_number) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $hashed_password, $role, $phone_number);

            if ($stmt->execute()) {
                // Redirect to a success page or login page
                header("Location: login.php?success=1");
                exit();
            } else {
                // Handle insertion error
                $error_message = "Error: " . $stmt->error;
            }

            $stmt->close();
        } elseif (empty($error_message) && $phone_number === null) {
            // This case should not be reached if the role checks are successful, but as a fallback
            $error_message = "Phone number could not be retrieved. Please check your role and email.";
        }
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
            
            <form method="POST" action="signup.php">
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
                    <i class="fas fa-user-tag input-icon"></i>
                    <select name="role" class="select-text" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="tenant">Tenant</option>
                        <option value="manager">Manager</option>
                    </select>
                </div>
                
                <input type="submit" class="button" value="Sign up">
            </form>
        </div>
    </body>
</html>
