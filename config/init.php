
<?php
require_once 'db_config.php';
require_once 'email_config.php';
require_once 'PHPMailer/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sanitizeInput($data)
{
    return htmlspecialchars(trim($data));
}

function handleDatabaseError($stmt)
{
    die("Error: " . $stmt->error);
}

$errors = [];
?>

