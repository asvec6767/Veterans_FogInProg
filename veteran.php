<? include_once('header.php'); 

// Подключение к БД и получение информации о ветеране
require_once 'db.php';  // Подключаем файл с функциями для работы с БД

$conn = connectDB(); //Подключение к бд
if (!$conn) {
  die("Ошибка подключения к БД.");  //Завершаем выполнение скрипта, если нет подключения
}?>

<link rel="stylesheet" href="/css/veteran.css">
<script src="/js/veteran_script.js"></script>

<? if (isset($_GET['id'])){ /**Страница ветерана */

    // Получаем ID ветерана из GET-параметра
    $veteran_id = isset($_GET['id']) ? intval($_GET['id']) : 0; // 0 как значение по умолчанию

    // Проверяем, что ID ветерана существует
    if ($veteran_id <= 0) {
        echo "Некорректный ID ветерана.";
        $conn = null; // Закрываем соединение с базой данных
        return; // Прерываем выполнение скрипта
    }

    $veteran = getVeteranInfo($conn, $veteran_id);

    if ($veteran) {
        echo htmlspecialchars($veteran["name"]);
    } else {
        echo "Информация о ветеране";
    }
    ?>

        <title>Информация о ветеране</title>
        <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=YOUR_API_KEY" type="text/javascript"></script>

        <main>
            <section id="veteran-info">
                <?php

                if ($veteran) {
                    echo "<h2>" . htmlspecialchars($veteran["name"]) . "</h2>";
                    echo "<p>Годы жизни: " . htmlspecialchars($veteran["birth_year"]) . " - " . htmlspecialchars($veteran["death_year"]) . "</p>";
                    echo "<p>" . htmlspecialchars($veteran["biography"]) . "</p>";
                    echo "<p>Зажженные свечи: <span id='candle-count'>" . htmlspecialchars($veteran["candles"]) . "</span> <button onclick='candleVeteran()'>Зажечь свечу</button></p>";

                } else {
                    echo "Информация о ветеране не найдена.";
                }
                ?>
            </section>

            <section id="gallery">
                <h2>Галерея фотографий</h2>
                <div class="gallery-container">
                    <?php
                    $images = getVeteranImages($conn, $veteran_id); //Получение изображений

                    if ($images) {
                        foreach ($images as $image) {
                            echo "<img src='/img/veteran/" . htmlspecialchars($image["image_url"]) . "' alt='Фотография'>";
                        }
                    } else {
                        echo "Фотографии не найдены.";
                    }
                    ?>
                </div>
            </section>

            <section id="map">
                <h2>Места, связанные с ветераном</h2>
                <div id="map-container"></div>
                <script>
                    // Передача данных о точках на карте в JavaScript
                    var mapPoints = <?php
                        $mapPoints = getMapPoints($conn, $veteran_id);
                        if ($mapPoints) {
                            echo json_encode($mapPoints); // Преобразуем в JSON
                        } else {
                            echo "[]";  // Пустой массив, если нет точек
                        }
                    ?>;
                </script>
            </section>
        </main>
        
    <script>
        function likeVeteran() {
            var likeCountElement = document.getElementById('like-count');
            var likeCount = parseInt(likeCountElement.innerText);
            likeCount++;

            // Отправка запроса на сервер для обновления данных в БД
            fetch('like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'veteran_id=<?php echo $veteran_id; ?>&likes=' + likeCount // Используем ID ветерана из PHP
            })
            .then(response => {
                if (response.ok) {
                    likeCountElement.innerText = likeCount; // Обновление на странице
                } else {
                    console.error('Ошибка при отправке запроса');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
            });
        }
    </script>
<?} else { /**Страница списка всех Ветеранов */?>
    <main>
        <section id="veteran-list">
            <ul>
                <?php

                $veterans = getAllVeterans($conn);

                if ($veterans) {
                    foreach ($veterans as $veteran) {
                        echo "<li><a href='/veteran/" . htmlspecialchars($veteran["id"]) . "'>" . htmlspecialchars($veteran["name"]) . "</a></li>";
                    }
                } else {
                    echo "Ветераны не найдены.";
                }

                ?>
            </ul>
        </section>
    </main>
<?}?>

<?php
if ($conn) {
  $conn = null; //Закрываем соединение с БД
}
?>

<? include_once('footer.php'); ?>