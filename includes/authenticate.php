<?php
require_once __DIR__ . '/init.php';

use Sessions\AutoLogin;

if (isset($_SESSION['authenticated']) || isset($_SESSION['lynda_auth'])) {
    // we're OK
} else {
    $autologin = new AutoLogin($db);
    $autologin->checkCredentials();
    if (!isset($_SESSION['lynda_auth'])) {
        header('Location: ../User_Auth/login.php');
        exit;
    }
}