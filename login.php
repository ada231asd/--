<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Подготовка и выполнение запроса для получения пользователя
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Login = :login");
    $stmt->bindParam(':login', $login);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Проверка пароля
    if ($user && password_verify($password, $user['Password'])) {
        // Установка сессионных переменных
        $_SESSION['UserID'] = $user['UserID'];
        $_SESSION['RoleID'] = $user['RoleID'];
        $_SESSION['Login'] = $user['Login'];

        // Перенаправление на страницу личного кабинета или админ панель
        if ($user['RoleID'] == 1) {
            header("Location: admin.php");
        } else {
            header("Location: profile.php");
        }
        exit();
    } else {
        echo "Неверный логин или пароль.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Вход</title>
</head>
<body>
    <form method="post" action="login.php">
        Логин: <input type="text" name="login" required><br>
        Пароль: <input type="password" name="password" required><br>
        <input type="submit" value="Вход">
        <br>
        <br>
        <a href="register.php">Регистрация</a>
    </form>
</body>
</html>
