<?php
// Подключение к базе данных
include 'db.php';

// Обработка POST-запросjd на работу с данными
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['updateUser'])) {
        $userID = $_POST['userID'];
        $login = $_POST['login'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $roleName = $_POST['role']; // Текстовое представление роли
    
        try {
            // Находим RoleID по RoleName
            $stmtFindRole = $conn->prepare("SELECT RoleID FROM Roles WHERE RoleName = :roleName");
            $stmtFindRole->bindParam(':roleName', $roleName, PDO::PARAM_STR);
            $stmtFindRole->execute();
            $roleID = $stmtFindRole->fetchColumn();
    
            // Проверяем, что роль существует
            if (!$roleID) {
                echo "Ошибка при обновлении данных: Не существует роли с именем '$roleName'.";
                die();
            }
    
            // SQL-запрос для обновления данных пользователя
            $stmt = $conn->prepare("
                UPDATE Users
                SET Login = :login, Email = :email, Phone = :phone, RoleID = :roleID
                WHERE UserID = :userID
            ");
            $stmt->bindParam(':login', $login, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':roleID', $roleID, PDO::PARAM_INT);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
    
            echo "Данные пользователя с ID $userID успешно обновлены.";
        } catch (PDOException $e) {
            echo "Ошибка при обновлении данных: " . $e->getMessage();
            die();
        }
    }
    
    
    if (isset($_POST['deleteUser'])) {
        $userID = $_POST['userID'];

        try {
            $stmtDeleteEventParticipants = $conn->prepare("DELETE FROM EventParticipants WHERE UserID = :userID");
            $stmtDeleteEventParticipants->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmtDeleteEventParticipants->execute();
            // Удаление самого пользователя
            $stmtDeleteUser = $conn->prepare("DELETE FROM Users WHERE UserID = :userID");
            $stmtDeleteUser->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmtDeleteUser->execute();

            echo "Пользователь с ID $userID успешно удален.";
        } catch (PDOException $e) {
            echo "Ошибка при удалении данных: " . $e->getMessage();
            die();
        }
    }
    if (isset($_POST['addUser'])) {
        $login = $_POST['login'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Хеширование пароля
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $roleID = $_POST['role'];
        $membershipTypeID = $_POST['membership'];

        try {
            // Вставка нового пользователя
            $stmt = $conn->prepare("
                INSERT INTO Users (Login, Password, Email, Phone, RoleID, MembershipTypeID)
                VALUES (:login, :password, :email, :phone, :roleID, :membershipTypeID)
            ");
            $stmt->bindParam(':login', $login, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':roleID', $roleID, PDO::PARAM_INT);
            $stmt->bindParam(':membershipTypeID', $membershipTypeID, PDO::PARAM_INT);
            $stmt->execute();

            echo "Новый пользователь успешно добавлен.";
        } catch (PDOException $e) {
            echo "Ошибка при добавлении пользователя: " . $e->getMessage();
            die();
        }
    }
    
}

$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

try {
    if ($search !== '%') {
        // SQL-запрос для поиска данных пользователей с ролями и членством
        $stmt = $conn->prepare("
            SELECT U.UserID, U.Login, U.Email, U.Phone, R.RoleName, M.MembershipTypeName
            FROM Users U
            LEFT JOIN Roles R ON U.RoleID = R.RoleID
            LEFT JOIN MembershipTypes M ON U.MembershipTypeID = M.MembershipTypeID
            WHERE U.Login LIKE :search OR U.Email LIKE :search OR R.RoleName LIKE :search OR M.MembershipTypeName LIKE :search
        ");
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
    } else {
        // SQL-запрос для выборки всех пользователей с ролями и членством
        $stmt = $conn->prepare("
            SELECT U.UserID, U.Login, U.Email, U.Phone, R.RoleName, M.MembershipTypeName
            FROM Users U
            LEFT JOIN Roles R ON U.RoleID = R.RoleID
            LEFT JOIN MembershipTypes M ON U.MembershipTypeID = M.MembershipTypeID
        ");
    }
    
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Ошибка получения данных: " . $e->getMessage();
    die();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Таблица пользователей</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #fff;
            text-align: left;
        }
        th {
            background-color: #333;
        }
        .search-form, .add-form {
            margin-bottom: 20px;
        }
        .search-input, .add-input {
            padding: 5px;
            font-size: 16px;
        }
        .search-button, .add-button {
            padding: 5px 10px;
            font-size: 16px;
            background-color: #4CAF50;
            border: none;
            color: white;
            cursor: pointer;
        }
        .delete-button {
            padding: 5px 10px;
            font-size: 16px;
            background-color: #f44336;
            border: none;
            color: white;
            cursor: pointer;
        }
        .update-form {
            margin: 20px;
        }
        .update-input {
            padding: 5px;
            font-size: 16px;
        }
        .update-button {
            padding: 5px 10px;
            font-size: 16px;
            background-color: #4CAF50;
            border: none;
            color: white;
            cursor: pointer;
        }
        .add-form {
            margin: 20px;
        }
        .add-input {
            padding: 5px;
            font-size: 16px;
        }
        .add-button {
            padding: 5px 10px;
            font-size: 16px;
            background-color: #4CAF50;
            border: none;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Таблица пользователей</h1>
    
    <form action="" method="get" class="search-form">
        <input type="text" name="search" class="search-input" placeholder="Поиск по логину, email, роли или членству">
        <button type="submit" class="search-button">Искать</button>
    </form>
    
    <?php if (count($results) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Логин</th>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Роль</th>
                    <th>Пати</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                    <form action="" method="post" class="update-form">
                            <input type="hidden" name="userID" value="<?php echo $row['UserID']; ?>">
                            <tr>
                        </form>
                           <form action="" method="post" class="update-form">
                            <input type="hidden" name="userID" value="<?php echo $row['UserID']; ?>">
                            <td><?php echo htmlspecialchars($row['UserID']); ?></td>
                            <td><input type="text" name="login" value="<?php echo htmlspecialchars($row['Login']); ?>" class="update-input"></td>
                            <td><input type="email" name="email" value="<?php echo htmlspecialchars($row['Email']); ?>" class="update-input"></td>
                            <td><input type="text" name="phone" value="<?php echo htmlspecialchars($row['Phone']); ?>" class="update-input"></td>
                            <td><input type="role" id="role" name="role" value="<?php echo htmlspecialchars($row['RoleName']); ?>" class="update-input"></td>
                            <td><input type="MembershipTypeName" id="membership" name="membership" value="<?php echo htmlspecialchars($row['MembershipTypeName']); ?>" class="update-input"></td>
                            <td><button type="submit" name="updateUser" class="update-button">Обновить</button></td>
                        </form>
                        <form action="" method="post" class="delete-form">
                            <input type="hidden" name="userID" value="<?php echo $row['UserID']; ?>">
                            <td><button type="submit" name="deleteUser" class="delete-button">Удалить</button></td>
                        </form>
                        
                    </tr>
                    
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Нет данных по вашему запросу.</p>
    <?php endif; ?>
    <h2>Добавление нового пользователя</h2>
    <form action="" method="post" class="add-form">
        <label for="login">Логин:</label>
        <input type="text" id="login" name="login" class="add-input" required><br><br>
        
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" class="add-input" required><br><br>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" class="add-input" required><br><br>
        
        <label for="phone">Телефон:</label>
        <input type="text" id="phone" name="phone" class="add-input"><br><br>
        
        <label for="role">Роль:</label>
        <select id="role" name="role" class="add-input" required>
            <option value="1">Admin</option>
            <option value="2">Member</option>
        </select><br><br>
        
        <label for="membership">Тип членства:</label>
        <select id="membership" name="membership" class="add-input" required>
            <option value="1">Standard</option>
            <option value="2">Premium</option>
        </select><br><br>
        
        <button type="submit" name="addUser" class="add-button">Добавить пользователя</button>
    </form>
</body>
</html>
