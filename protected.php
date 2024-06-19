<?php
include 'session_check.php';

// Предполагаем, что роль администратора имеет ID 1
if ($_SESSION['RoleID'] != 1) {
    echo "Доступ запрещен. Только для администраторов.";
    exit();
}

echo "Добро пожаловать, " . $_SESSION['Login'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Защищенная страница</title>
</head>
<body>
    <h1>Страница администратора</h1>
    <p>Эта страница доступна только администраторам.</p>
    <a href="logout.php">Выход</a>
</body>
</html>
