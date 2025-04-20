<?php
// candle.php
require_once 'db.php';

$conn = connectDB();
if (!$conn) {
    die("Ошибка подключения к БД.");
}

// Получение данных из POST запроса
$veteran_id = isset($_POST['veteran_id']) ? intval($_POST['veteran_id']) : 0;
$candles = $_POST['candles'];

// Проверка ID ветерана
if ($veteran_id <= 0) {
    echo "Некорректный ID ветерана.";
    $conn = null;
    return;
}

// Обновление данных в БД
if (updateCandles($conn, $veteran_id, $candles)) {
    echo "Свечи успешно обновлены";
} else {
    echo "Ошибка обновления свечей.";
}

$conn = null;
?>