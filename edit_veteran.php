<?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){

    // session_start();
    // if (!isset($_SESSION['admin'])) {
    //     echo json_encode(['status' => 'error', 'message' => 'Нет доступа.']);
    //     exit;
    // }
    
    require_once 'db.php';
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $veteran_id = isset($_POST['veteran_id']) ? intval($_POST['veteran_id']) : 0;
        $name = isset($_POST['name']) ? trim($_POST['name']) : "";
        $birth_year = isset($_POST['birth_year']) ? intval($_POST['birth_year']) : null;
        $death_year = isset($_POST['death_year']) ? intval($_POST['death_year']) : null;
        $desc = isset($_POST['desc']) ? trim($_POST['desc']) : "";
        $biography = isset($_POST['biography']) ? trim($_POST['biography']) : "";
        $published = isset($_POST['published']) ? 1 : 0;
    
        if ($veteran_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Некорректный ID ветерана.']);
            exit;
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
    
        $res = updateVeteran($conn, $veteran_id, $name, $birth_year, $death_year, $desc, $biography, $published);
        if (!$res) echo json_encode(['status' => 'error', 'message' => 'Ошибка обновления ветерана: ' . $e->getMessage()]);
        else echo json_encode(['status' => 'success', 'message' => 'Ветеран успешно обновлен!']);
    
        $conn = null;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Некорректный запрос.']);
    }

} else if (isset($_GET['id']) ){

// session_start();
// if (!isset($_SESSION['admin'])) {
//     header('Location: admin_login.php');
//     exit;
// }
require_once 'db.php';
$conn = connectDB();
if (!$conn) {
    die("Ошибка подключения к БД.");
}

$veteran_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($veteran_id < 0) {
    echo "Некорректный ID ветерана.";
    exit;
}

$veteran = getVeteranInfo($conn, $veteran_id);

$conn = null;

$title = 'Редактирование Ветерана';
include_once('header.php');
?>
    <main>
        <!-- <section id="edit-veteran-form">
            <form id="edit-veteran-form">
                <input type="hidden" id="veteran_id" name="veteran_id" value="<?php echo htmlspecialchars($veteran['id']); ?>">

                <label for="name">Имя:</label><br>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($veteran['name']); ?>" required><br><br>

                <label for="birth_year">Год рождения:</label><br>
                <input type="number" id="birth_year" name="birth_year" value="<?php echo htmlspecialchars($veteran['birth_year']); ?>"><br><br>

                <label for="death_year">Год смерти:</label><br>
                <input type="number" id="death_year" name="death_year" value="<?php echo htmlspecialchars($veteran['death_year']); ?>"><br><br>

                <label for="desc">Краткое описание:</label><br>
                <textarea id="desc" name="desc" rows="4" cols="50"><?php echo htmlspecialchars($veteran['desc']); ?></textarea><br><br>

                <label for="biography">Биография:</label><br>
                <textarea id="biography" name="biography" rows="8" cols="50"><?php echo htmlspecialchars($veteran['biography']); ?></textarea><br><br>

                <label for="published">Опубликовать:</label>
                <input type="checkbox" id="published" name="published" <?php echo $veteran['published'] ? 'checked' : ''; ?>><br><br>

                <button type="button" onclick="editVeteran()">Сохранить</button>
            </form>
            <div id="message"></div>
        </section> -->

        <section id="edit-veteran" class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="shadow">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title mb-0">Редактирование ветерана</h3>
                        </div>
                        <div class="card-body">
                            <form id="edit-veteran-form">
                                <input type="hidden" id="veteran_id" name="veteran_id" value="<?php echo htmlspecialchars($veteran['id']); ?>">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label fw-bold">ФИО<span style="color: red">*<span></label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($veteran['name']); ?>" required placeholder="Введите полное имя">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="birth_year" class="form-label fw-bold">Год рождения</label>
                                        <input type="number" maxlength="4" class="form-control" id="birth_year" name="birth_year" value="<?php echo htmlspecialchars($veteran['birth_year']); ?>" placeholder="ГГГГ">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="death_year" class="form-label fw-bold">Год смерти</label>
                                        <input type="number" class="form-control" id="death_year" name="death_year" value="<?php echo htmlspecialchars($veteran['death_year']); ?>" placeholder="ГГГГ">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="desc" class="form-label fw-bold">Краткое описание</label>
                                    <textarea class="form-control" id="desc" name="desc" rows="3" placeholder="Краткая информация о ветеране"><?php echo htmlspecialchars($veteran['desc']); ?></textarea>
                                    <div class="form-text">Не более 200 символов</div>
                                </div>

                                <div class="mb-4">
                                    <label for="biography" class="form-label fw-bold">Биография</label>
                                    <textarea class="form-control" id="biography" name="biography" rows="6" placeholder="Подробная биография ветерана"><?php echo htmlspecialchars($veteran['biography']); ?></textarea>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="published" name="published" <?php echo $veteran['published'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-bold" for="published">Опубликовать</label>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-primary px-4 py-2" onclick="editVeteran()">
                                        <i class="bi bi-save me-2"></i>Сохранить
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
        function editVeteran() {
            const form = document.getElementById('edit-veteran-form');
            const messageDiv = document.getElementById('message');
            const formData = new FormData();
            formData.append('veteran_id', document.getElementById('veteran_id').value);
            formData.append('name', document.getElementById('name').value);
            formData.append('birth_year', document.getElementById('birth_year').value);
            formData.append('death_year', document.getElementById('death_year').value);
            formData.append('desc', document.getElementById('desc').value);
            formData.append('biography', document.getElementById('biography').value);
            formData.append('published', document.getElementById('published').value);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // window.alert(data.message);
                showSuccess(data.message);
                messageDiv.textContent = data.message;
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.textContent = 'Произошла ошибка при обновлении ветерана.';
            });
        }

        function showSuccess(message) {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            document.getElementById('successMessage').textContent = message;
            successModal.show();
        }
    </script>
    <?include_once('footer.php');?>

<?} else {
//     echo "Спсиок ветеранрв";
// session_start();
// if (!isset($_SESSION['admin'])) {
//     header('Location: admin_login.php');
//     exit;
// }
require_once 'db.php';
$conn = connectDB();
if (!$conn) {
    die("Ошибка подключения к БД.");
}
$sql = "SELECT id, name, published FROM veterans ORDER BY name";
$result = $conn->query($sql);

$title = 'Список ветеранов';
include_once('header.php');
?>
    <main>
       <?/* <section id="veteran-list">
            <table>
                <thead>
                    <tr>
                        <th>Имя</th>
                        <th>Опубликовано</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->rowCount() > 0) {
                        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                            echo "<td>" . ($row["published"] ? "Да" : "Нет") . "</td>";
                            echo "<td>
                                    <a href='edit_veteran.php?id=" . htmlspecialchars($row["id"]) . "'>Редактировать</a>
                                    </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>Ветераны не найдены.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section> */?>
        <section id="veteran-list" class="container mt-5">
            <div class="shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Список ветеранов</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" class="w-50">ФИО</th>
                                    <th scope="col" class="w-25">Опубликовано</th>
                                    <th scope="col" class="w-25">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->rowCount() > 0) {
                                    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                                        echo "<td><span class='badge " . ($row["published"] ? "bg-success" : "bg-secondary") . "'>" . ($row["published"] ? "Да" : "Нет") . "</span></td>";
                                        echo "<td>
                                                <a href='edit_veteran.php?id=" . htmlspecialchars($row["id"]) . "' class='btn btn-sm btn-outline-primary'>
                                                    <i class='fas fa-edit'></i> Редактировать
                                                </a>
                                                <a class='btn btn-sm btn-outline-danger ms-1'>
                                                    <i class='fas fa-trash-alt'></i> Удалить
                                                </a>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3' class='text-center py-4 text-muted'>Ветераны не найдены</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?include_once('footer.php');?>
<?php $conn = null;
}?>