<?php
require_once 'config/init.php';
require_once 'config/db_config.php';
require_once 'config/email_config.php';
require_once 'config/validation.php';
//require_once 'config/captcha_config.php';
require 'PHPMailer/vendor/autoload.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Registration</title>
    <link rel="icon" href="Images\cysecicon.png" type="image/x-icon">
    <link rel="preconnect" href="https://www.google.com">
    <link rel="preconnect" href="https://www.gstatic.com" crossorigin>
    <link rel="stylesheet" href="css/register.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>
    <div class="registration-container">
        <div class="registration-form">
            <h2 style="color: #4caf50;">User Registration Form</h2>
            <form id="registrationForm" action="?" method="post" onsubmit="submitForm()">
                <div class="form-group">
                    <label for="fullName">Full Name<span class="required-field">*</span></label>
                    <input type="text" class="form-control" name="fullName" id="fullName" placeholder="Full Name" required>
                  
                </div>

                <div class="form-group">
                    <label for="userName">User Name<span class="required-field">*</span></label>
                    <input type="text" class="form-control" name="userName" id="userName" placeholder="User Name" required oninput="checkUsernameAvailability()">
                    <div id="usernameAvailabilityIndicator"></div>
                   
                </div>

                <div class="form-group">
                    <label for="email">Email (only @gmail.com is allowed)<span class="required-field">*</span></label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
                
                </div>

                <div class="form-group">
                    <label for="password">Password<span class="required-field">*</span></label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                 
                    <div class="password-strength-meter">
                        <div class="password-strength-bar" id="password-strength-bar"></div>
                        <div class="password-strength-label" id="password-strength-label"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm Password<span class="required-field">*</span></label>
                    <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password" required>
              
                </div>

                <div class="form-group">
                    <label for="secQuestion">Security Question<span class="required-field">*</span></label>
                    <input type="text" class="form-control" name="secQuestion" id="secQuestion" placeholder="Security Question" required>
                 
                </div>

                <div class="form-group">
                    <label for="securityAns">Security Answer<span class="required-field">*</span></label>
                    <input type="text" class="form-control" name="securityAns" id="securityAns" placeholder="Security Answer" required>
            
                </div>

                <div class="g-recaptcha" data-sitekey="6LcgwikpAAAAANeL2CO-I_QcRyRG0-fhrPohqDmY"></div>
                <hr>

                <button type="submit" value="submit" class="form-btn" name="register">Register</button>
                <p style="color: #4caf50;">Already a member? <a href="login.php" class="already-member">Log In</a></p>
            </form>
        </div>

        <div class="additional-container">
            <div class="image-container">
                <img src="Images\join_us.jpg" alt="Image">
            </div>

            <div class="policy-container">
                <h3>Password Policies</h3>
                <ul>
                    <li>Minimum 8 characters</li>
                    <li>At least 1 uppercase letter</li>
                    <li>At least 1 lowercase letter</li>
                    <li>At least 1 number</li>
                    <li>At least 1 special character</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        function checkUsernameAvailability() {
        var username = document.getElementById("userName").value;
         var availabilityIndicator = document.getElementById("usernameAvailabilityIndicator");

        // Make an AJAX request to the server
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "check_username_availability.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = xhr.responseText;
            // Update the UI based on the server response
            if (response === "available") {
                availabilityIndicator.innerHTML = '<span style="color: green;">Username is available</span>';
            } else {
                availabilityIndicator.innerHTML = '<span style="color: red;">Username not available</span>';
            }
        }
        };
        xhr.send("username=" + username);
}

        document.getElementById("password-strength-bar").style.width = "0%";

        $('#registrationForm').on('input', function() {
            updatePasswordStrengthMeter();
        });

        function updatePasswordStrengthMeter() {
            console.log("Updating password strength meter");
            var password = $('#password').val();
            var passwordStrength = calculatePasswordStrength(password, <?php echo $passwordPolicy["minLength"]; ?>);
            var passwordStrengthBar = $('#password-strength-bar');
            var passwordStrengthLabel = $('#password-strength-label');

            switch (passwordStrength) {
                case 0:
                    passwordStrengthBar.css({ width: "0%" });
                    passwordStrengthLabel.text("");
                    break;
                case 1:
                    passwordStrengthBar.css({ width: "20%", backgroundColor: "#ff6666" });
                    passwordStrengthLabel.text("Weak").css({ color: "#ff6666" });
                    break;
                case 2:
                    passwordStrengthBar.css({ width: "40%", backgroundColor: "#ffcc66" });
                    passwordStrengthLabel.text("Moderate").css({ color: "#ffcc66" });
                    break;
                case 3:
                    passwordStrengthBar.css({ width: "60%", backgroundColor: "#99ff99" });
                    passwordStrengthLabel.text("Good").css({ color: "#99ff99" });
                    break;
                case 4:
                    passwordStrengthBar.css({ width: "80%", backgroundColor: "#66ff66" });
                    passwordStrengthLabel.text("Strong").css({ color: "#66ff66" });
                    break;
                case 5:
                    passwordStrengthBar.css({ width: "100%", backgroundColor: "#33cc33" });
                    passwordStrengthLabel.text("Excellent").css({ color: "#33cc33" });
                    break;
            }
        }

        function calculatePasswordStrength(password, minLength) {
            var strength = 0;

            if (password.length >= minLength) {
                strength += 1;
            }

            if (password.match(/[A-Z]/)) {
                strength += 1;
            }

            if (password.match(/[a-z]/)) {
                strength += 1;
            }

            if (password.match(/[0-9]/)) {
                strength += 1;
            }

            if (password.match(/[^\w\d]/)) {
                strength += 1;
            }

            return strength;
        }

        function submitForm() {
            var recaptchaResponse = grecaptcha.getResponse();

            if (recaptchaResponse.length === 0) {
                alert("reCAPTCHA response is missing");
                return false; // Prevent form submission
            }

            // Continue with form submission
            document.getElementById("registrationForm").submit();
        }
    </script>
</body>
</html>
