<?php
// db.php - Файл для работы с базой данных

// Настройки подключения к БД
$servername = "localhost";
$username = "f1116969_user";
$password = "pass123@";
$dbname = "f1116969_user";

// Функция для подключения к БД
function connectDB() {
    global $servername, $username, $password, $dbname;
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        echo "Ошибка подключения к БД: " . $e->getMessage();
        return false;  // Возвращаем false в случае ошибки
    }
}

// Функция для получения информации о ветеране по ID
function getVeteranInfo($conn, $veteran_id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM veterans WHERE id = :id");
        $stmt->bindParam(':id', $veteran_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetch();
    } catch(PDOException $e) {
        echo "Ошибка получения информации о ветеране: " . $e->getMessage();
        return false;
    }
}

// Функция для получения всех точек на карте для ветерана
function getMapPoints($conn, $veteran_id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM map_point WHERE veteran_id = :veteran_id");
        $stmt->bindParam(':veteran_id', $veteran_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll(); // Возвращает массив всех точек
    } catch(PDOException $e) {
        echo "Ошибка получения точек карты: " . $e->getMessage();
        return false;
    }
}

// Функция для получения URL-ов изображений ветерана
function getVeteranImages($conn, $veteran_id) {
    try {
        $stmt = $conn->prepare("SELECT image_url FROM veteran_images WHERE veteran_id = :veteran_id");
        $stmt->bindParam(':veteran_id', $veteran_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll(); // Возвращает массив всех URL
    } catch(PDOException $e) {
        echo "Ошибка получения изображений ветерана: " . $e->getMessage();
        return false;
    }
}

// Функция для обновления количества лайков
function updatecandles($conn, $veteran_id, $candles) {
    try {
        $sql = "UPDATE veterans SET candles = :candles WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':candles', $candles, PDO::PARAM_INT);
        $stmt->bindParam(':id', $veteran_id, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    } catch(PDOException $e) {
        echo "Ошибка обновления лайков: " . $e->getMessage();
        return false;
    }
}

// Функция для получения всех ветеранов
function getAllVeterans($conn) {
    try {
        $stmt = $conn->prepare("SELECT id, name FROM veterans");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        echo "Ошибка получения списка ветеранов: " . $e->getMessage();
        return false;
    }
}
?>