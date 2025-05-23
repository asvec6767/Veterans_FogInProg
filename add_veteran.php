<?php
require __DIR__ . '/kandinsky.php';
use neiro\imageGen;
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
            return ['status' => 'success', 'path' => "/img/upload/" . basename($image["name"])];
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
        if($kd = imageGen::getInstance()){
            return ['status' => 'success', 'path' => $kd::question($prompt)];
        }
    }
    
    // Функция для генерации описания
    function generateDescription($prompt): array {
        $query = $prompt;
        
        $data['text'] = require_once __DIR__ . '/gpt.php';
    
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
        $generate_image = (isset($_POST['generate_image']) AND $_POST['generate_image']=='true') ? 1 : 0;
        $image_prompt = isset($_POST['image_prompt']) ? trim($_POST['image_prompt']) : "";
        $generate_desc = (isset($_POST['generate_desc']) AND $_POST['generate_desc']=='true') ? 1 : 0;
        $desc_prompt = isset($_POST['desc_prompt']) ? trim($_POST['desc_prompt']) : "";
    
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
        } elseif (isset($_POST['generate_image']) && (bool)$_POST['generate_image'] && !empty($_POST['image_prompt'])) {
            $generateResult = generateImage($_POST['image_prompt']);
            if ($generateResult['status'] === 'success') {
                $imagePath = $generateResult['path'];
            } else {
                // echo json_encode(['status' => 'error', 'message' => $generateResult['message']]);
                // exit;
            }
        }
    
        // Генерируем описание
        if (isset($_POST['generate_desc']) && (bool)$_POST['generate_desc'] && !empty($_POST['desc_prompt'])) {
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
        $new_veteran_id = $conn->lastInsertId();
        $sql = "INSERT INTO veteran_images (veteran_id, image_url) VALUES (:veteran_id, :image_url)";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':veteran_id', $new_veteran_id, PDO::PARAM_STR);
            $stmt->bindParam(':image_url', $imagePath, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {        }

        if (!$res) echo json_encode(['status' => 'error', 'message' => 'Ошибка добавления ветерана: ' . $e->getMessage()]);
        else echo json_encode(['status' => 'success', 'message' => 'Ветеран успешно добавлен! Страница будет опубликована после обработки администратором! Предпросмотр страницы доступен по ссылке(кнопка)', 'path' => '/veteran/'.$new_veteran_id]);
    
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
                                
                                <div id="loadingSpinner" class="d-none text-center my-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Загрузка...</span>
                                    </div>
                                    <p class="mt-2">Обработка запроса...</p>
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
                    <a id="veteranLink" href="#" class="btn btn-success d-none">Перейти к странице</a>
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
            formData.append('generate_image', document.getElementById('generate_image').checked);
            formData.append('image_prompt', document.getElementById('image_prompt').value);
            formData.append('image', document.getElementById('image').files[0]);
            formData.append('generate_desc', document.getElementById('generate_desc').checked);
            formData.append('desc_prompt', document.getElementById('desc_prompt').value);

            loadingSpinner.classList.remove('d-none');
            // submitButton.disabled = true;

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                messageDiv.textContent = data.message;
                // window.alert(data.message);
                // showSuccess(data.message);
                showSuccess(data.message, data.path);
                if (data.status === 'success') {
                    form.reset();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.textContent = 'Произошла ошибка при добавлении ветерана.';
            })
            .finally(() => {
                loadingSpinner.classList.add('d-none');
                submitButton.disabled = false;
            });;
        }

        function showSuccess(message, path = null) {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            const successMessage = document.getElementById('successMessage');
            const veteranLink = document.getElementById('veteranLink');

            successMessage.textContent = message;

            if (path) {
                veteranLink.href = path;
                veteranLink.classList.remove('d-none');
            } else {
                veteranLink.classList.add('d-none');
            }

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