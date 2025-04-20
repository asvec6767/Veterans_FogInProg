
<?php
function renderFooter() {
    $currentYear = date('Y');
    echo <<<HTML
<footer class="site-footer" style="
    background: linear-gradient(to right, #C72727, #C72727, rgb(167, 17, 17));
    color:rgb(255, 255, 255);
    padding: 30px 0;
    border-top: 1px solid rgb(209, 174, 146);
">
    <div class="footer-container" style="
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    ">
        <!-- Логотип и описание -->
        <div class="footer-section" style="flex: 1; min-width: 250px; margin-bottom: 20px;">
            <h3 style="color:rgb(255, 255, 255); font-size: 1.5rem; margin-bottom: 15px;">
                <img src="/img/logo_header.jpg" style="height: 40px; vertical-align: middle;">
                <span style="vertical-align: middle;">Страницы памяти.РФ</span>
            </h3>
            <p style="line-height: 1.6; font-size: 0.9rem;">
                Проект посвящен сохранению памяти<br> о героях Великой Отечественной войны.<br>
                Мы помним каждого.
            </p>
        </div>

        <!-- Навигация -->
        <div class="footer-section" style="flex: 1; min-width: 250px; margin-bottom: 20px;">
            <h4 style="color:rgb(255, 255, 255); font-size: 1.2rem; margin-bottom: 15px;">Навигация</h4>
            <ul style="list-style: none; padding: 0; line-height: 2;">
                <li><a href="#" style="color: rgb(255, 255, 255); text-decoration: none;">Главная</a></li>
                <li><a href="#" style="color: rgb(255, 255, 255); text-decoration: none;">Ветераны</a></li>
                <li><a href="#" style="color:rgb(255, 255, 255); text-decoration: none;">Карта памяти</a></li>
                <li><a href="#" style="color: rgb(255, 255, 255); text-decoration: none;">О проекте</a></li>
            </ul>
        </div>

        <!-- Контакты -->
        <div class="footer-section" style="flex: 1; min-width: 250px; margin-bottom: 20px;">
            <h4 style="color: rgb(255, 255, 255); font-size: 1.2rem; margin-bottom: 15px;">Контакты</h4>
            <address style="font-style: normal; line-height: 1.6;">
                <p><i class="fas fa-envelope" style="margin-right: 10px;"></i> info@pamyat-naroda.ru</p>
                <p><i class="fas fa-map-marker-alt" style="margin-right: 10px;"></i> г. Тула</p>
            </address>
            <div class="social-links" style="margin-top: 15px;">
                <a href="#" style="color: rgb(255, 255, 255); margin-right: 15px; font-size: 1.2rem;"><i class="fab fa-vk"></i></a>
                <a href="#" style="color: rgb(255, 255, 255); margin-right: 15px; font-size: 1.2rem;"><i class="fab fa-telegram"></i></a>
                <a href="#" style="color: rgb(255, 255, 255); margin-right: 15px; font-size: 1.2rem;"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>

    <!-- Копирайт -->
    <div class="footer-bottom" style="
        text-align: center;
        padding-top: 20px;
        margin-top: 20px;
        border-top: 1px solid #5a3a1f;
        font-size: 0.9rem;
    ">
        <p>&copy; $currentYear Проект "Страницы памяти.РФ". Все права защищены.</p>
        <p style="margin-top: 10px; font-size: 0.8rem;">
            <a href="/privacy" style="color: #e6d5b3; text-decoration: none;">Политика конфиденциальности</a> | 
            <a href="/terms" style="color: #e6d5b3; text-decoration: none;">Условия использования</a>
        </p>
    </div>
</footer>

<!-- Подключение иконок Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
HTML;
}

// Использование футера на странице
renderFooter();
?>