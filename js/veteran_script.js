document.addEventListener('DOMContentLoaded', function () {
    // Инициализация карты Yandex
    ymaps.ready(init);

    function init() {
        var myMap = new ymaps.Map('map-container', {
            center: [55.76, 37.64], // Москва - устанавливаем центр по умолчанию
            zoom: 10
        }, {
            searchControlProvider: 'yandex#search'
        });
// Полностью чистая карта без элементов управления
myMap.controls.remove('zoomControl');
myMap.controls.remove('searchControl');
myMap.controls.remove('trafficControl');
myMap.controls.remove('typeSelector');
myMap.controls.remove('fullscreenControl');
myMap.controls.remove('rulerControl');
// Применяем песочный стиль
            myMap.options.set({
                custom: {
                    style: [
                        // Основной песочный фон
                        {
                            featureType: "all",
                            elementType: "all",
                            stylers: [
                                { hue: "#e6d5b3" },
                                { saturation: -40 },
                                { lightness: 20 },
                                { gamma: 0.9 }
                            ]
                        },
                        // Скрываем ненужные элементы
                        {
                            featureType: "road",
                            elementType: "all",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "transit",
                            elementType: "all",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "poi",
                            elementType: "all",
                            stylers: [{ visibility: "off" }]
                        }
                    ]
                }
            });


        // Обработка точек на карте из PHP
        if (typeof mapPoints !== 'undefined' && mapPoints.length > 0) {
            mapPoints.forEach(function (point) {
                var myPlacemark = new ymaps.Placemark([point.latitude, point.longitude], {
                    hintContent: point.info,
                    balloonContent: point.info
                });
                myMap.geoObjects.add(myPlacemark);
            });
            //Если есть точки, центрируем по первой из них
            myMap.setCenter([mapPoints[0].latitude, mapPoints[0].longitude])
        } else {
            console.log('Нет точек на карте для отображения.');
        }
    }
});

function candleVeteran() {
    var candleCountElement = document.getElementById('candle-count');
    var candleCount = parseInt(candleCountElement.innerText);
    candleCount++;

    // Отправка запроса на сервер для обновления данных в БД
    fetch('http://'+location.href.split('/')[2]+'/candle.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'veteran_id=1&candles=' + candleCount  // Замените на получение ID ветерана из PHP
    })
    .then(response => {
        if (response.ok) {
            candleCountElement.innerText = candleCount; // Обновление на странице
        } else {
            console.error('Ошибка при отправке запроса');
        }
    })
    .catch(error => {
        console.error('Ошибка:', error);
    });
}
