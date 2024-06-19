<?php
session_start();

// Проверка, если пользователь не авторизован
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

// Если пользователь уже авторизован, перенаправить его на личный кабинет или админ панель
if (basename($_SERVER['PHP_SELF']) !== 'profile.php' && basename($_SERVER['PHP_SELF']) !== 'admin.php') {
    if ($_SESSION['RoleID'] == 1) {
        header("Location: admin.php");
    } else {
        header("Location: profile.php");
    }
    exit();
}
?>
