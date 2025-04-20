<?php
$title = 'Добавление ветерана';
include_once('header.php'); 
require_once 'db.php';

$conn = connectDB();
if (!$conn) {
  die("Ошибка подключения к БД.");
}?>

<link rel="stylesheet" href="/css/veteran.css">
<link rel="stylesheet" href="/css/candle.css">
<script src="/js/veteran_script.js"></script>

<? 
$veteran_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($veteran_id <= 0) {
    echo "Некорректный ID ветерана.";
    $conn = null;
    return;
}

$veteran = getVeteranInfo($conn, $veteran_id);

// if ($veteran) {
//     echo htmlspecialchars($veteran["name"]);
// } else {
//     echo "Информация о ветеране";
// }
?>

<title>Информация о ветеране</title>
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=" type="text/javascript"></script>

<main>
    <section id="veteran-info">
        <?php
        if ($veteran) {
            echo "<h2>" . htmlspecialchars($veteran["name"]) . "</h2>";
            echo "<p>Годы жизни: " . htmlspecialchars($veteran["birth_year"]) . " - " . htmlspecialchars($veteran["death_year"]) . "</p>";
            echo "<p>" . htmlspecialchars($veteran["biography"]) . "</p>";
            echo "<p>Зажженные свечи: <span id='candle-count'>" . htmlspecialchars($veteran["candles"]) . "</span> <button id='light-candle-btn' class='toggle-candle-btn'>Зажечь свечу</button></p>";   
        } else {
            echo "Информация о ветеране не найдена.";
        }
        ?>
    </section>

    <!-- Добавляем контейнер для свечи -->
    <div class="background-container">
        <div class="container">
            <div class="candle-container">
                <div class="candle">
                    <div class="candle-stick"></div>
                    <div class="candle-body"></div>
                    <div class="candle-flame"></div>
                    <div class="wax-drips" id="waxDrips"></div>
                </div>
            </div>
        </div>
    </div>

    <section id="gallery">
        <h2>Галерея фотографий</h2>
        <div class="gallery-container">
            <?php
            $images = getVeteranImages($conn, $veteran_id);
            if ($images) {
                foreach ($images as $image) {
                    if (str_contains($image["image_url"],'/img/')) echo "<img src='" . htmlspecialchars($image["image_url"]) . "' alt='Фотография'>";
                    else echo "<img src='/img/veteran/" . htmlspecialchars($image["image_url"]) . "' alt='Фотография'>";
                }
            } else {
                echo "Фотографии не найдены.";
            }
            ?>
        </div>
    </section>

    <section id="map">
        <h2>Места, связанные с ветераном</h2>
        <div id="map-container" style="width: 100%; height: 400px;"></div>
        <script>
            var mapPoints = <?php
                $mapPoints = getMapPoints($conn, $veteran_id);
                if ($mapPoints) {
                    echo json_encode($mapPoints);
                } else {
                    echo "[]";
                }
            ?>;
        </script>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const candle = document.querySelector('.candle');
    const backgroundContainer = document.querySelector('.background-container');
    const waxDrips = document.getElementById('waxDrips');
    const candleCount = document.getElementById('candle-count');
    const lightBtn = document.getElementById('light-candle-btn');
    let isLit = false;
    let dripInterval;

    function candleVeteran() {
        const count = parseInt(candleCount.textContent);
        
        if (!isLit) {
            // Light the candle
            backgroundContainer.style.opacity = '1';
            candle.classList.add('lit');
            isLit = true;
            lightBtn.textContent = 'Погасить свечу';
            startDrips();
            
            // Update candle count
            candleCount.textContent = count + 1;
            
            // Send AJAX request to update count in database
            fetch('http://'+window.location.host+'/candle.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'veteran_id=<?php echo $veteran_id; ?>&candles=' + (count + 1)
            });
        } else {
            // Extinguish the candle
            backgroundContainer.style.opacity = '0';
            candle.classList.remove('lit');
            isLit = false;
            lightBtn.textContent = 'Зажечь свечу';
            stopDrips();
        }
    }

    function startDrips() {
        stopDrips();
        dripInterval = setInterval(createDrip, 3000);
        for (let i = 0; i < 3; i++) {
            setTimeout(createDrip, i * 1000);
        }
    }

    function stopDrips() {
        if (dripInterval) {
            clearInterval(dripInterval);
        }
    }

    function createDrip() {
        if (!isLit) return;
        
        const drip = document.createElement('div');
        drip.classList.add('drip');
        const leftPos = Math.random() * 80 + 10;
        drip.style.left = `${leftPos}px`;
        const dripWidth = Math.random() * 5 + 5;
        drip.style.width = `${dripWidth}px`;
        waxDrips.appendChild(drip);
        
        setTimeout(() => {
            if (drip.parentNode) {
                drip.remove();
            }
        }, 3000);
    }

    // Initialize
    lightBtn.addEventListener('click', candleVeteran);
    
    // If there are already candles, light it up
    if (parseInt(candleCount.textContent) > 0) {
        candleVeteran();
    }

    function likeVeteran() {
        var likeCountElement = document.getElementById('like-count');
        var likeCount = parseInt(likeCountElement.innerText);
        likeCount++;

        fetch('like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'veteran_id=<?php echo $veteran_id; ?>&likes=' + likeCount
        })
        .then(response => {
            if (response.ok) {
                likeCountElement.innerText = likeCount;
            } else {
                console.error('Ошибка при отправке запроса');
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
    }
});
</script>

<?php
if ($conn) {
  $conn = null;
}
include_once('footer.php'); 
?>
