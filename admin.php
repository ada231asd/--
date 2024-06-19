<?php
include 'session_check.php';
include 'db.php';

// Убедитесь, что это админ
if ($_SESSION['RoleID'] != 1) {
    echo "Доступ запрещен. Только для администраторов.";
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Админ панель</title>
</head>
<body>
    <h1>Админ панель</h1>
    <p>Добро пожаловать, администратор <?php echo htmlspecialchars($_SESSION['Login']); ?>!</p>
    <a href="logout.php">Выход</a>
</body>
</html>
