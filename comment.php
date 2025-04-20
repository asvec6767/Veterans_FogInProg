<?php
// Проверяем, был ли отправлен POST-запрос
if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Получаем данные из POST-запроса
        $veteran_id = isset($_POST['veteran_id']) ? intval($_POST['veteran_id']) : 0;
        $name = isset($_POST['name']) ? trim($_POST['name']) : "";
        $text = isset($_POST['text']) ? trim($_POST['text']) : "";
    
        // Проверка ID ветерана
        if ($veteran_id < 0) {
            echo json_encode(['status' => 'error', 'message' => 'Некорректный ID ветерана.']);
            exit();
        }
    
        // Валидация данных
        if (empty($name) || empty($text)) {
            echo json_encode(['status' => 'error', 'message' => 'Пожалуйста, заполните все поля.']);
            exit();
        }
    
        // Подключаемся к базе данных
        require_once 'db.php';
        $conn = connectDB();
        if (!$conn) {
            echo json_encode(['status' => 'error', 'message' => 'Ошибка подключения к БД.']);
            exit();
        }
    
        // Добавляем комментарий в базу данных
        if (addVeteranComment($conn, $veteran_id, $name, $text)) {
            // Возвращаем успешный ответ в формате JSON
            echo json_encode(['status' => 'success', 'message' => 'Послание успешно добавлено.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Ошибка добавления при послания.']);
        }
    
        // Закрываем соединение с базой данных
        $conn = null;
} else {
    // Если не был отправлен POST-запрос, отображаем блок комментариев

    // Подключаемся к базе данных
    require_once 'db.php';
    $conn = connectDB();
    if (!$conn) {
        echo "Ошибка подключения к БД.";
        return;
    }  

    $veteran_id = isset($veteran_id)?$veteran_id:0;
    $comments = getVeteranComments($conn, $veteran_id);

    ?>
    <section id="comments">
        <h2>Послания</h2>
        <ul id="comment-list">
            <?php
                if ($comments) {
                    foreach ($comments as $comment) {
                        echo "<li>";
                        echo "<p style='font-size: 1.5rem; font-weight: 600;'>" . htmlspecialchars($comment["name"]) . "</p>";
                        echo "<p style='color:#6e6e6e'>" . htmlspecialchars($comment["text"]) . "</p>";
                        echo "<p style='color:#6e6e6e'>" . htmlspecialchars($comment["date"]) . "</p>"; 
                        echo "</li>";
                    }
                } else {
                    echo "Комментарии не найдены.";
                }
            ?>
        </ul>
        </section>

        <section id="add-comment">
        <h2>Память стоит того, чтобы беречь, а благодарность стоит того, чтобы нести</h2>
        <form id="comment-form">
            <div class="form-group mb-4">
                <label for="name" class="mb-2">Ваше имя</label>
                <input type="text" id="name" name="name">
            </div>

            <div class="form-group">
                <label for="text" class="mb-2">Оставьте ваше послание памяти героя...</label>
                <textarea id="text" name="text" rows="4" cols="50"></textarea>
            </div>

            <button type="button" class="btn btn-success mt-3" onclick="addComment()">Отправить</button>
        </form>
    </section>


    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Успешно</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="successMessage">
                   
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Ошибка</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="errorMessage">
 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        
    function addComment() {
        var name = document.getElementById('name').value;
        var text = document.getElementById('text').value;
        var veteranId = <?=isset($veteran_id)?$veteran_id:0?>; // Получаем ID ветерана из PHP

        // Простая валидация на стороне клиента
        // if (!name || !text) {
        //     alert('Пожалуйста, заполните все поля.');
        //     return;
        // }

        if (!name || !text) {
        showError('Пожалуйста, заполните все поля.');
        return;
        }

        var formData = new FormData();
        formData.append('veteran_id', veteranId);
        formData.append('name', name);
        formData.append('text', text);

        fetch(window.location.href.toString().split(window.location.host)[0]+window.location.host+'/'+'comment.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Очищаем форму
                document.getElementById('comment-form').reset();
                // Обновляем список комментариев
                loadComments();
                // alert('Успех: ' + data.message);
                showSuccess(data.message);
            } else {
                // alert('Ошибка: ' + data.message);
                showError(data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            // alert('Произошла ошибка при отправке комментария.');
            showError('Произошла ошибка при отправке послания.');
        });
    }

    function loadComments() {
        fetch(window.location.href) //запрос на эту же страницу, чтобы не менять id
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                // Find the comments section in the new content
                const newDoc = new DOMParser().parseFromString(data, 'text/html');
                const newCommentsSection = newDoc.getElementById('comments');

                if (newCommentsSection) {
                    // Replace the old comments section with the new one
                    document.getElementById('comments').outerHTML = newCommentsSection.outerHTML;
                } else {
                    console.error('Could not find comments section in new content');
                }
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
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
}
?>