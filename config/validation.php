<?php
require_once 'db_config.php';
require_once 'PHPMailer/vendor/autoload.php';

// Assuming that sanitizeInput and calculatePasswordStrength functions are defined

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $fullName = sanitizeInput($_POST['fullName']);
    $userName = sanitizeInput($_POST['userName']);
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $confirmPassword = sanitizeInput($_POST['confirmPassword']);
    $secQuestion = sanitizeInput($_POST['secQuestion']);
    $securityAns = sanitizeInput($_POST['securityAns']);

    // Validation checks
    $errors = [];

    if (empty($fullName) || empty($userName) || empty($email) || empty($password) || empty($confirmPassword) || empty($secQuestion) || empty($securityAns)) {
        $errors[] = "All fields are required.";
    } else {
        // Open a new mysqli connection
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT * FROM users WHERE UserName = ?");
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Username is not available. Please choose another username.";
        }

        $stmt->close(); // Close the first statement

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $errors[] = "This email is already registered.";
            }

            $stmt->close(); // Close the second statement

            // Password validation
            $passwordPolicy = array(
                "minLength" => 8,
                "minUpperCase" => 1,
                "minLowerCase" => 1,
                "minNumbers" => 1,
                "minSpecialChars" => 1
            );

            if (strlen($password) < $passwordPolicy["minLength"]) {
                $errors[] = "Password must be at least 8 characters long.";
            }

            if (!preg_match("/[A-Z]/", $password)) {
                $errors[] = "Password must contain at least one uppercase letter.";
            }

            if (!preg_match("/[a-z]/", $password)) {
                $errors[] = "Password must contain at least one lowercase letter.";
            }

            if (!preg_match("/[0-9]/", $password)) {
                $errors[] = "Password must contain at least one number.";
            }

            if (!preg_match("/[^A-Za-z0-9]/", $password)) {
                $errors[] = "Password must contain at least one special character.";
            }

            if ($password !== $confirmPassword) {
                $errors[] = "Passwords do not match.";
            }

            // Continue with registration if there are no errors
            if (empty($errors)) {
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $conn->prepare("INSERT INTO users (FullName, UserName, Email, Password, sec_question, security_ans, role_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $role_id = 2;
                $stmt->bind_param("ssssssi", $fullName, $userName, $email, $passwordHash, $secQuestion, $securityAns, $role_id);

                if ($stmt->execute()) {
                    // Send a registration confirmation email
                    $mail = new PHPMailer;
                    // Add your SMTP configuration here
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'Codeyaima3301@gmail.com';
                    $mail->Password = 'hazi iped jkhy wsoa';
                    $mail->SMTPSecure = 'tls'; // Use 'tls' or 'ssl'
                    $mail->Port = 587; // Use the appropriate port for your SMTP configuration

                    // Enable SMTP debugging and log errors
                    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Set to 2 for detailed debug output
                    $mail->Debugoutput = function ($str, $level) {
                        error_log("[$level] $str");
                    };

                    $mail->setFrom('codeyaima3301@gmail.com', 'Amir Maharjan');
                    $mail->addAddress($email, $fullName);
                    $mail->Subject = 'Registration Confirmation';
                    $mail->Body = 'Thank you for registering!';

                    try {
                        if ($mail->send()) {
                            echo "<script>alert('Registration successful! Check your email for confirmation.');</script>";
                        } else {
                            echo "<script>alert('Registration successful, but failed to send confirmation email.');</script>";
                        }
                    } catch (Exception $e) {
                        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
                    }
                } else {
                    $errors[] = "Error: " . $stmt->error;
                }

                $stmt->close(); // Close the third statement
            }
        }

        // Close the database connection
        $conn->close();
    }

    // Display errors if any
    function displayErrors($errors) {
        if (!empty($errors)) {
            echo '<div style="color: red;">' . implode('<br>', $errors) . '</div>';
        }
    }
}
?>
