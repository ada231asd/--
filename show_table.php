<?php
// Подключение к базе данных
include 'db.php';

$userID = isset($_GET['userID']) ? intval($_GET['userID']) : null;

try {
    // SQL-запрос для получения свободных мест для данного пользователя
    $stmt_free_places = $conn->prepare("
        SELECT CP.PlaceID
        FROM ComputerPlaces CP
        LEFT JOIN BookingPlaces BP ON CP.PlaceID = BP.PlaceID AND (BP.UserID = :userID OR BP.UserID IS NULL)
        WHERE BP.PlaceID IS NULL
    ");
    $stmt_free_places->bindParam(':userID', $userID);
    $stmt_free_places->execute();
    $free_places = $stmt_free_places->fetchAll(PDO::FETCH_COLUMN);

    if (count($free_places) > 0) {
        // SQL-запрос для получения данных о бронированиях с названием тарифа
        $stmt_bookings = $conn->prepare("
            SELECT BP.BookingID, U.Login AS UserLogin, CP.Number AS PlaceNumber,
                   BP.Date, BP.StartTime, BP.EndTime, T.Name AS TariffName
            FROM BookingPlaces BP
            LEFT JOIN Users U ON BP.UserID = U.UserID
            INNER JOIN ComputerPlaces CP ON BP.PlaceID = CP.PlaceID
            INNER JOIN Tariffs T ON BP.TariffID = T.TariffID
            WHERE BP.PlaceID IN (" . implode(',', $free_places) . ")
            ORDER BY BP.Date, BP.StartTime
        ");
        $stmt_bookings->execute();
        $results = $stmt_bookings->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $results = [];
    }
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
    <title>Таблица бронирований</title>
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
    <h1>Таблица бронирований</h1>
    <?php if (count($results) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Пользователь</th>
                    <th>Место</th>
                    <th>Дата</th>
                    <th>Время начала</th>
                    <th>Время окончания</th>
                    <th>Тариф</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                    <td><?php echo htmlspecialchars($row['BookingID']); ?></td>
                        <td><?php echo isset($row['UserLogin']) ? htmlspecialchars($row['UserLogin']) : 'Неизвестный пользователь'; ?></td>
                        <td><?php echo htmlspecialchars($row['PlaceNumber']); ?></td>
                        <td><?php echo htmlspecialchars($row['Date']); ?></td>
                        <td><?php echo htmlspecialchars($row['StartTime']); ?></td>
                        <td><?php echo htmlspecialchars($row['EndTime']); ?></td>
                        <td><?php echo htmlspecialchars($row['TariffName']); ?></td>
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
