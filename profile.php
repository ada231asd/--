<?php
include 'session_check.php';
include 'db.php';

// Получение информации о пользователе
$user_id = $_SESSION['UserID'];
$stmt = $conn->prepare("SELECT Login, Email, Phone FROM Users WHERE UserID = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Пользователь не найден.";
    exit();
}

// Проверка данных пользователя
$login = htmlspecialchars($user['Login']);
$email = htmlspecialchars($user['Email'] ?? '');
$phone = htmlspecialchars($user['Phone'] ?? '');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Личный кабинет</title>
</head>
<body>
    <h1>Личный кабинет</h1>
    <p>Добро пожаловать, <?php echo $login; ?>!</p>
    <p>Email: <?php echo $email; ?></p>
    <p>Телефон: <?php echo $phone; ?></p>
    <a href="logout.php">Выход</a>
    <br><br>
    <p><a href="show_table.php">Перейти к таблице бронирований</a></p>
    <br><br>
    <p><a href="show_table_event.php">Перейти к таблице ивенты</a></p>
    <br><br>
    <p><a href="show_table_user.php">Перейти к таблице пользователи</a></p>
    
</body>
</html>
