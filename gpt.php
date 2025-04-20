<?php

// Замените на свой API-ключ и URL прокси
$apiKey = ''; //proxy api
$baseUrl = 'https://api.proxyapi.ru/openai/v1'; // Или другой адрес прокси

// Запрос (prompt)
if (!(isset($query) AND !empty($query))) $query = 'Кратко опиши ветерана великой отечественной войны';

// Параметры запроса
$model = 'gpt-3.5-turbo-1106';
$temperature = 0.7;
$maxTokens = 55;

// Формируем данные для POST-запроса
$data = [
    'model' => $model,
    'messages' => [['role' => 'user', 'content' => $query]],
    'temperature' => $temperature,
    'max_tokens' => $maxTokens,
    'n' => 1,
    'stop' => null
];

// Преобразуем данные в JSON
$jsonData = json_encode($data);

// Заголовки запроса
$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey // Если API требует Bearer токен
];

// Параметры для file_get_contents (для выполнения POST-запроса)
$options = [
    'http' => [
        'method'  => 'POST',
        'header'  => implode("\r\n", $headers),
        'content' => $jsonData,
        'ignore_errors' => true // Важно для получения ответов об ошибках
    ]
];

// Создаем контекст потока
$context  = stream_context_create($options);

// URL API
$apiUrl = $baseUrl . '/chat/completions';

// Выполняем запрос
$result = file_get_contents($apiUrl, false, $context);

// Проверяем, что запрос выполнен успешно
if ($result === FALSE) {
    return;
    //return "Ошибка при выполнении запроса к OpenAI API.";
}

// Декодируем JSON-ответ
$response = json_decode($result, true);

// Обрабатываем ответ
if (isset($response['choices'][0]['message']['content'])) {
    // Получаем текст ответа
    $responseText = $response['choices'][0]['message']['content'];

    // Обрезаем текст до последней точки и добавляем точку и кавычку
    $lastDotPosition = strrpos($responseText, '.');
    if ($lastDotPosition !== false) {
        $result = substr($responseText, 0, $lastDotPosition + 1) . '"';
    } else {
        $result = $responseText . '"'; // Если нет точек, добавляем кавычку
    }

    return $result . "\n";
} else {
    // Выводим сообщение об ошибке (если API вернул ошибку)
    //echo "Ошибка от OpenAI API:\n";
    //echo json_encode($response, JSON_PRETTY_PRINT) . "\n"; // Для удобства просмотра
    return;
}

?>