<?php
// Start session to access session variables
session_start();
?>

<!DOCTYPE html>
<html>
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
        <title>BuildingManager ∣ Log in</title>
    </head>

    <body>
        <div class="topL">
            <div style="font-size:50px;"><b><a href="login.php" style="color: white; text-decoration: none;">BuildingManager</a></b></div>   
            <div class="signup_button"><a href="signup.php" style="color: black; text-decoration: none;"> Signup</a></div>
        </div>
        <div class="login_bar">
            <h2 style="font-size: 2rem; text-align: center; margin-bottom: 1.5rem; color: #fff; font-weight: bold;">Log In</h2>

            
            <?php
            // Check for and display any login error message from the session
            if (isset($_SESSION['error_message'])) {
                echo '<p style="color:red;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
                // Clear the message after displaying it
                unset($_SESSION['error_message']);
            }
            ?>
            <div>
            <!-- The form now points to loginbackend.php -->
            <form method="POST" action="loginbackend.php">
                <div class="input-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="text" name="email" class="text" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" class="text" placeholder="Password" required>
                </div>
                <input type="submit" class="button" value="Log in">
            </form>
        </div>
    </body>
</html>
