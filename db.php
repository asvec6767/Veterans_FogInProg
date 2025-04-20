<?php
// db.php - Файл для работы с базой данных

// Настройки подключения к БД
$servername = "";
$username = "";
$password = "";
$dbname = "";

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
        $stmt = $conn->prepare("SELECT veterans.`id`, veterans.`name`, veterans.`desc`, veteran_images.`image_url`, CONCAT_WS(' - ', birth_year, death_year) AS `date` FROM veterans LEFT JOIN veteran_images ON veterans.`id` = veteran_images.`veteran_id` WHERE published = 1 GROUP BY veterans.`id` ORDER BY veterans.`id` ASC");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        echo "Ошибка получения списка ветеранов: " . $e->getMessage();
        return false;
    }
}

// Функция для получения комментариев для ветерана
function getVeteranComments($conn, $veteran_id) {
    try {
        $stmt = $conn->prepare("SELECT id, date, name, text FROM veteran_comments WHERE veteran_id = :veteran_id ORDER BY date DESC");
        $stmt->bindParam(':veteran_id', $veteran_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        echo "Ошибка получения комментариев: " . $e->getMessage();
        return false;
    }
}

// Функция для добавления комментария в базу данных
function addVeteranComment($conn, $veteran_id, $name, $text) {
    try {
        $sql = "INSERT INTO veteran_comments (veteran_id, date, name, text) VALUES (:veteran_id, NOW(), :name, :text)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':veteran_id', $veteran_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':text', $text, PDO::PARAM_STR);
        return $stmt->execute();
    } catch(PDOException $e) {
        echo "Ошибка добавления комментария: " . $e->getMessage();
        return false;
    }
}

// Функция добавления ветерана в базу данных
function addVeteran($conn, $name, $birth_year, $death_year, $desc, $biography) {
    $sql = "INSERT INTO veterans (name, birth_year, death_year, `desc`, biography, published) VALUES (:name, :birth_year, :death_year, :desc, :biography, 0)";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':birth_year', $birth_year, PDO::PARAM_INT);
        $stmt->bindParam(':death_year', $death_year, PDO::PARAM_INT);
        $stmt->bindParam(':desc', $desc, PDO::PARAM_STR);
        $stmt->bindParam(':biography', $biography, PDO::PARAM_STR);
        return $stmt->execute();
    } catch (PDOException $e) {
        return false;
    }
}

// Функция обновления информации о Ветеране
function updateVeteran($conn, $veteran_id, $name, $birth_year, $death_year, $desc, $biography, $published){
    $sql = "UPDATE veterans SET name = :name, birth_year = :birth_year, death_year = :death_year, `desc` = :desc, biography = :biography, published = :published WHERE id = :id";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $veteran_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':birth_year', $birth_year, PDO::PARAM_INT);
        $stmt->bindParam(':death_year', $death_year, PDO::PARAM_INT);
        $stmt->bindParam(':desc', $desc, PDO::PARAM_STR);
        $stmt->bindParam(':biography', $biography, PDO::PARAM_STR);
        $stmt->bindParam(':published', $published, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->execute();
    } catch (PDOException $e) {
        return false;
    }
}
?>
