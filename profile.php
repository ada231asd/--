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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Личный кабинет</title>
</head>
<body>
    <h1>Личный кабинет</h1>
    <p>Добро пожаловать, <?php echo htmlspecialchars($user['Login']); ?>!</p>
    <p>Email: <?php echo htmlspecialchars($user['Email']); ?></p>
    <p>Телефон: <?php echo htmlspecialchars($user['Phone']); ?></p>
    <a href="logout.php">Выход</a>
</body>
</html>
