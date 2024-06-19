<?php
// Подключение к базе данных
include 'db.php';

try {
    // SQL-запрос для получения данных о мероприятиях и участниках
    $stmt = $conn->prepare("
        SELECT E.EventID, E.Name AS EventName, E.Description, E.EventDate, E.StartTime, E.EndTime, 
               UP.Login AS ParticipantLogin
        FROM Events E
        LEFT JOIN EventParticipants EP ON E.EventID = EP.EventID
        LEFT JOIN Users UP ON EP.UserID = UP.UserID
        ORDER BY E.EventDate, E.StartTime
    ");
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
    <title>Таблица мероприятий</title>
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
    </style>
</head>
<body>
    <h1>Таблица мероприятий</h1>
    <?php if (count($results) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Название мероприятия</th>
                    <th>Описание</th>
                    <th>Дата</th>
                    <th>Время начала</th>
                    <th>Время окончания</th>
                    <th>Участник</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['EventName']); ?></td>
                        <td><?php echo htmlspecialchars($row['Description']); ?></td>
                        <td><?php echo htmlspecialchars($row['EventDate']); ?></td>
                        <td><?php echo htmlspecialchars($row['StartTime']); ?></td>
                        <td><?php echo htmlspecialchars($row['EndTime']); ?></td>
                        <td><?php echo htmlspecialchars($row['ParticipantLogin'] ?? 'NULL'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Нет доступных данных для отображения.</p>
    <?php endif; ?>
    <br>
    <a href="profile.php">Личный кабинет</a>
</body>
</html>
