<?php
// Session Management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: /pages/auth/login.php');
        exit();
    }
}

function getUserData()
{
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'name' => $_SESSION['name'],
            'role' => $_SESSION['role']
        ];
    }
    return null;
}

function logout()
{
    session_destroy();
    header('Location: /pages/auth/login.php');
    exit();
}
