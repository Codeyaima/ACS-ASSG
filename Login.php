<?php
// Start the session to manage user login status
session_start();

// Include the PHPMailer library for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Function to sanitize input data
function sanitizeInput($data)
{
    return htmlspecialchars(trim($data));
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    // Retrieve and sanitize user inputs
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);

    // Database connection
    $conn = new mysqli("localhost", "root", "", "cysec");

    // Check for a successful connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check user credentials
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the entered password with the stored hash
        if (password_verify($password, $user['Password'])) {
            // Password is correct, log in the user
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['Email'];

            // Redirect to a welcome page or dashboard
            header("Location: welcome.php");
            exit();
        } else {
            $errors[] = "Incorrect password. Please try again.";
        }
    } else {
        $errors[] = "No user found with the provided email address.";
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="icon" href="Images\cysecicon.png" type="image/x-icon">
    <style>
        body {
            background: url('Images\login_background.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            max-width: 400px;
            padding: 20px;
            border: 2px solid #008000; /* Green border */
            border-radius: 20px; /* Increased border-radius */
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent white background */
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-btn {
            background-color: #4caf50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>User Login</h2>
        <form id="loginForm" action="" method="post">
            <?php
            // Display error messages, if any
            if (!empty($errors)) {
                echo '<div style="color: red;">' . implode('<br>', $errors) . '</div>';
            }
            ?>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="form-btn" name="login">Login</button>
            <p>Don't have an account? <a href="registration.php">Register here</a></p>
        </form>
    </div>
</body>

</html>
