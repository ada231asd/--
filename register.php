<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $membership_type_id = 1; // ID стандартного типа членства

    // Проверка длины пароля
    if (strlen($password) < 6) {
        echo "Пароль должен быть не менее 6 символов.";
    } 
    // Проверка совпадения паролей
    else if ($password !== $confirm_password) {
        echo "Пароли не совпадают.";
    } else {
        // Проверка уникальности логина
        $stmt_check = $conn->prepare("SELECT UserID FROM Users WHERE Login = :login");
        $stmt_check->bindParam(':login', $login);
        $stmt_check->execute();
        $row = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            echo "Пользователь с таким логином уже существует.";
        } else {
            // Хэширование пароля
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $default_role_id = 2; // Предположим, что 2 - это ID обычного пользователя

            // Подготовка и выполнение запроса на вставку данных
            $stmt = $conn->prepare("INSERT INTO Users (Login, Password, RoleID, Email, Phone, MembershipTypeID) VALUES (:login, :password, :role, :email, :phone, :membership_type)");
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':role', $default_role_id);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':membership_type', $membership_type_id);

            if ($stmt->execute()) {
                echo "Регистрация прошла успешно!";
            } else {
                echo "Ошибка: " . $stmt->errorInfo()[2];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
</head>
<body>
    <form method="post" action="register.php">
        Логин: <input type="text" name="login" required><br>
        Пароль: <input type="password" name="password" required><br>
        Повторите пароль: <input type="password" name="confirm_password" required><br>
        Email: <input type="email" name="email" required><br>
        Телефон: <input type="text" name="phone" required><br>
        <input type="submit" value="Регистрация">
    </form>
</body>
</html>
