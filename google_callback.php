<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('YOUR_GOOGLE_CLIENT_ID');
$client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/google_callback.php');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    // Get profile info
    $oauth2 = new Google_Service_Oauth2($client);
    $profile = $oauth2->userinfo->get();

    // Use $profile['email'], $profile['name'] to log in or create account
    session_start();
    $_SESSION['email'] = $profile['email'];
    $_SESSION['name'] = $profile['name'];

    header('Location: dashboard.php');
    exit();
} else {
    echo "Google login failed.";
}
