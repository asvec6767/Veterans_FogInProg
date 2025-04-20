<?php
/** Серверная часть страницы, обработка запроса */
if ($_SERVER["REQUEST_METHOD"] == "POST"){








    
    require_once 'db.php';
    
    // Функция для загрузки изображения
    function uploadImage($image): array {
        $targetDir = $_SERVER['DOCUMENT_ROOT']."/img/upload/";
        $targetFile = $targetDir . basename($image["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedFormats = ["jpg", "png", "jpeg"];
    
        if (!in_array($imageFileType, $allowedFormats)) {
            return ['status' => 'error', 'message' => 'Недопустимый формат изображения.'];
        }
    
        if ($image["size"] > 5000000) {
            return ['status' => 'error', 'message' => 'Размер изображения не должен превышать 5MB.'];
        }
    
        if (move_uploaded_file($image["tmp_name"], $targetFile)) {
            return ['status' => 'success', 'path' => $targetFile];
        } else {
            return ['status' => 'error', 'message' => 'Ошибка при загрузке изображения.'];
        }
    }
    
    // Функция для отправки запроса к API (заменяет Guzzle)
    function httpRequest($url, $method, $headers = [], $data = null): array {
        $options = [
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'ignore_errors' => true, // Чтобы получать ответы с кодами ошибок
            ],
        ];
    
        if ($data !== null) {
            if (is_array($data)) {
                $data = json_encode($data);
            }
            $options['http']['content'] = $data;
        }
    
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
    
        // Разбираем заголовки ответа
        $responseHeaders = [];
        if (isset($http_response_header)) {
            foreach ($http_response_header as $header) {
                $parts = explode(':', $header, 2);
                if (count($parts) == 2) {
                    $responseHeaders[trim($parts[0])] = trim($parts[1]);
                } else {
                    $responseHeaders[] = $header; // Для HTTP/1.0
                }
            }
        }
    
        return ['body' => $result, 'headers' => $responseHeaders];
    }
    
    // Функция для генерации изображения
    function generateImage($prompt): array {
        $apiKey = '510C197089AEB9806D57C58493AD3E7B'; //Заменить
    
        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ];
    
        $data = ['prompt' => $prompt];
    
        $response = httpRequest('https://api.kandinsky.one/generate', 'POST', $headers, $data);
    
        if ($response['body'] === false) {
            return ['status' => 'error', 'message' => 'Ошибка при генерации изображения.'];
        }
    
        $data = json_decode($response['body'], true);
    
        if (isset($data['image'])) {
            $image = base64_decode($data['image']);
            $file = '/img/generated/' . uniqid() . '.png';
    
            if (file_put_contents($file, $image) !== false) {
                return ['status' => 'success', 'path' => $file];
            } else {
                return ['status' => 'error', 'message' => 'Не удалось сохранить изображение.'];
            }
        } else {
            return ['status' => 'error', 'message' => 'Ошибка при генерации изображения.'];
        }
    }
    
    // Функция для генерации описания
    function generateDescription($prompt): array {
        $apiKey = 'YOUR_CHAT_API_KEY'; //Заменить
    
        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ];
    
        $data = ['prompt' => $prompt];
    
        $response = httpRequest('https://api.chat.com/generate', 'POST', $headers, $data);
    
        if ($response['body'] === false) {
            return ['status' => 'error', 'message' => 'Ошибка при генерации описания.'];
        }
    
        $data = json_decode($response['body'], true);
    
        if (isset($data['text'])) {
            return ['status' => 'success', 'text' => $data['text']];
        } else {
            return ['status' => 'error', 'message' => 'Ошибка при генерации описания.'];
        }
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = isset($_POST['name']) ? trim($_POST['name']) : "";
        $birth_year = isset($_POST['birth_year']) ? intval($_POST['birth_year']) : null;
        $death_year = isset($_POST['death_year']) ? intval($_POST['death_year']) : null;
        $desc = isset($_POST['desc']) ? trim($_POST['desc']) : "";
        $biography = isset($_POST['biography']) ? trim($_POST['biography']) : "";
    
        // Обрабатываем изображение
        $imagePath = null;
        if (!empty($_FILES["image"]["name"])) {
            $uploadResult = uploadImage($_FILES["image"]);
            if ($uploadResult['status'] === 'success') {
                $imagePath = $uploadResult['path'];
            } else {
                // echo json_encode(['status' => 'error', 'message' => $uploadResult['message']]);
                // exit;
            }
        } elseif (isset($_POST['generate_image']) && !empty($_POST['image_prompt'])) {
            $generateResult = generateImage($_POST['image_prompt']);
            if ($generateResult['status'] === 'success') {
                $imagePath = $generateResult['path'];
            } else {
                // echo json_encode(['status' => 'error', 'message' => $generateResult['message']]);
                // exit;
            }
        }
    
        // Генерируем описание
        if (isset($_POST['generate_desc']) && !empty($_POST['desc_prompt'])) {
            $descriptionResult = generateDescription($_POST['desc_prompt']);
    
            if ($descriptionResult['status'] === 'success') {
                $desc = $descriptionResult['text'];
            } else {
                // echo json_encode(['status' => 'error', 'message' => $descriptionResult['message']]);
                // exit;
            }
        }
    
        if (empty($name)) {
            echo json_encode(['status' => 'error', 'message' => 'Имя обязательно для заполнения.']);
            exit;
        }
    
        $conn = connectDB();
        if (!$conn) {
            echo json_encode(['status' => 'error', 'message' => 'Ошибка подключения к БД.']);
            exit;
        }

        /**Добавление ветерана */
        $res = addVeteran($conn, $name, $birth_year, $death_year, $desc, $biography);
    
        // $sql = "INSERT INTO veteran_images (veteran_id, image_url) VALUES (:veteran_id, :image_url)";
        // try {
        //     $stmt = $conn->prepare($sql);
        //     $stmt->bindParam(':veteran_id', 0, PDO::PARAM_STR);
        //     $stmt->bindParam(':image_url', $imagePath, PDO::PARAM_STR);
        //     $stmt->execute();
        // } catch (PDOException $e) {        }

        if (!$res) echo json_encode(['status' => 'error', 'message' => 'Ошибка добавления ветерана: ' . $e->getMessage()]);
        else echo json_encode(['status' => 'success', 'message' => 'Ветеран успешно добавлен! Страница будет опубликована после обработки администратором!']);
    
        $conn = null;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Некорректный запрос.']);
    }

} else {
/** Визуальная часть страницы */

// session_start();
// if (!isset($_SESSION['admin'])) {
//     header('Location: admin_login.php');
//     exit;
// }
$title = 'Добавление ветерана';
include_once('header.php');

?>
<div class="form-group"></div>
    <!-- <h1>Добавить ветерана</h1> -->
    <main>
        <!-- <section id="add-veteran">
            <form id="add-veteran-form">
                <div class="form-group mb-3">
                    <label class="form-label" for="name">Имя</label>
                    <input class="form-control" type="text" id="name" name="name" required>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label"  for="birth_year">Год рождения</label>
                    <input class="form-control" type="number" id="birth_year" name="birth_year">
                </div>

                <div class="form-group mb-3">
                    <label class="form-label"  for="death_year">Год смерти</label>
                    <input class="form-control" type="number" id="death_year" name="death_year">
                </div>

                <div class="form-group mb-3">
                    <label class="form-label" for="desc">Краткое описание</label>
                    <textarea class="form-control" id="desc" name="desc" rows="4" cols="50"></textarea>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label"  for="biography">Биография</label>
                    <textarea class="form-control" id="biography" name="biography" rows="8" cols="50"></textarea>
                </div>

                <button type="button" onclick="addVeteran()">Добавить</button>
            </form>
            <div id="message"></div>
        </section> -->

        <section id="add-veteran" class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="shadow">
                        <div class="card-header bg-success text-white">
                            <h3 class="card-title mb-0">Добавление ветерана</h3>
                        </div>
                        <div class="card-body">
                            <form id="add-veteran-form">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label fw-bold">Имя<span style="color: red">*<span></label>
                                        <input type="text" class="form-control" id="name" name="name" required placeholder="Введите полное имя">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="birth_year" class="form-label fw-bold">Год рождения</label>
                                        <input type="number" maxlength="4" class="form-control" id="birth_year" name="birth_year" placeholder="ГГГГ">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="death_year" class="form-label fw-bold">Год смерти</label>
                                        <input type="number" class="form-control" id="death_year" name="death_year" placeholder="ГГГГ">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="desc" class="form-label fw-bold">Краткое описание</label>
                                    <textarea class="form-control" id="desc" name="desc" rows="3" placeholder="Краткая информация о ветеране"></textarea>
                                    <div class="form-text">Не более 200 символов</div>
                                </div>

                                <div class="mb-4">
                                    <label for="biography" class="form-label fw-bold">Биография</label>
                                    <textarea class="form-control" id="biography" name="biography" rows="6" placeholder="Подробная биография ветерана"></textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="image" class="form-label fw-bold">Изображение</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                </div>

                                <div class="mb-3">
                                    <label for="image_prompt" class="form-label fw-bold">Генерация изображения</label>
                                    <input type="checkbox" id="generate_image" name="generate_image">
                                    <textarea class="form-control" id="image_prompt" name="image_prompt" rows="3" placeholder="Сгенерировать изображение из промпта"></textarea>
                                    <div class="form-text">Не более 200 символов</div>
                                </div>

                                <div class="mb-3">
                                    <label for="desc_prompt" class="form-label fw-bold">Генерация описания</label>
                                    <input type="checkbox" id="generate_desc" name="generate_desc">
                                    <textarea class="form-control" id="desc_prompt" name="desc_prompt" rows="3" placeholder="Сгенерировать описание из промпта"></textarea>
                                    <div class="form-text">Не более 200 символов</div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-primary px-4 py-2" onclick="addVeteran()">
                                        <i class="bi bi-person-plus me-2"></i>Добавить
                                    </button>
                                </div>
                            </form>

                            <div id="message" class="mt-3 alert d-none"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">Внимание!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="successMessage">
                   
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <!-- <style> input, textarea, button{border:solid 1px #000;} </style> -->
    <script>
        function addVeteran() {
            const form = document.getElementById('add-veteran-form');
            const messageDiv = document.getElementById('message');
            const formData = new FormData();
            formData.append('name', document.getElementById('name').value);
            formData.append('birth_year', document.getElementById('birth_year').value);
            formData.append('death_year', document.getElementById('death_year').value);
            formData.append('desc', document.getElementById('desc').value);
            formData.append('biography', document.getElementById('biography').value);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                messageDiv.textContent = data.message;
                // window.alert(data.message);
                showSuccess(data.message);
                if (data.status === 'success') {
                    form.reset();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.textContent = 'Произошла ошибка при добавлении ветерана.';
            });
        }

        function showSuccess(message) {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            document.getElementById('successMessage').textContent = message;
            successModal.show();
        }

        function showError(message) {
            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            document.getElementById('errorMessage').textContent = message;
            errorModal.show();
        }
    </script>

<?
    include_once('footer.php');
}?>