<?php
/*echo $_SERVER['DOCUMENT_ROOT'];
// Замените на свой API-ключ
$apiKey = '510C197089AEB9806D57C58493AD3E7B';

// Промт для генерации изображения
$prompt = 'A majestic eagle soaring over a snow-capped mountain, realistic painting';

// URL API Kandinsky
$apiUrl = 'https://api.kandinsky.one/generate';

// Заголовки запроса
$headers = [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json'
];

// Данные для отправки (промт)
$data = json_encode(['prompt' => $prompt]);

// Настройки для file_get_contents (аналог curl)
$options = [
    'http' => [
        'method'  => 'POST',
        'header'  => implode("\r\n", $headers),
        'content' => $data,
        'ignore_errors' => true // Важно для получения ответов об ошибках
    ]
];

// Создаем контекст потока
$context  = stream_context_create($options);

// Выполняем запрос
$result = file_get_contents($apiUrl, false, $context);

// Проверяем, что запрос выполнен успешно
if ($result === FALSE) {
    echo "Ошибка при выполнении запроса к API Kandinsky.\n";
    exit;
}

// Декодируем JSON-ответ
$response = json_decode($result, true);

// Обрабатываем ответ
if (isset($response['image'])) {
    // Получаем base64-encoded изображение
    $imageData = base64_decode($response['image']);

    // Сохраняем изображение в файл (например, в папку 'generated')
    $filename = 'generated/kandinsky_image_' . time() . '.png'; // Уникальное имя
    file_put_contents($filename, $imageData);

    echo "Изображение успешно сгенерировано и сохранено в: " . $filename . "\n";
} else {
    // Выводим сообщение об ошибке (если API вернул ошибку)
    echo "Ошибка от API Kandinsky:\n";
    echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
}*/

require __DIR__ . '/kandinsky.php';
use neiro\imageGen;

if($kd = imageGen::getInstance()){
   echo $kd::question('Зеленый кот');
}

?>