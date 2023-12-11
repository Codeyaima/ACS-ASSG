<?php 
// Verify reCAPTCHA


$recaptchaSecretKey = "6LcgwikpAAAAAGV3xenmoW2K06r1cGjSC8XxHqlh"; // Replace with your Secret Key
$recaptchaResponse = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

if (empty($recaptchaResponse)) {
    // Handle case when reCAPTCHA response is not provided
    $errors[] = "reCAPTCHA response is missing.";
} 
if (empty($recaptchaResponse)) {
    $errors[] = "reCAPTCHA response is missing.";
} else {
    $recaptchaUrl = "https://www.google.com/recaptcha/api/siteverify";
    $recaptchaData = array (
        'secret' => $recaptchaSecretKey,
        'response' => $recaptchaResponse,
    );

    $recaptchaOptions = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($recaptchaData),
        ),
    );

    $recaptchaContext = stream_context_create($recaptchaOptions);
    $recaptchaResult = @file_get_contents($recaptchaUrl, false, $recaptchaContext);
    $recaptchaResultJson = json_decode($recaptchaResult, true);

    if (!$recaptchaResultJson['success']) {
        // reCAPTCHA verification failed
        $errors[] = "reCAPTCHA verification failed.";
    }
}
if (!empty($errors)) {
    echo '<div style="color: red;">' . implode('<br>', $errors) . '</div>';
}

?>