<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/css/style.css">
        <link rel="stylesheet" href="/css/reset.css">
        <link rel="icon" type="image/x-icon" href="/img/favicon.ico">
        <script type="text/javascript" src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
        <script type="text/javascript" src="https://unpkg.com/jquery@3.7.1/dist/jquery.min.js"></script>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />


        <title><?=isset($title)?$title:'Страницы памяти'?></title>
    </head>
    <body>
        <header class="header">
            <div class="header-wrapper">
                <div class="header__logo">
                    <a href="/" class="header__logo-link">
                        <img class="header__logo-img" src="/img/logo_header.jpg" alt="logo">
                    </a>
                </div>
                <nav class="nav">
                    <ul class="nav__list">
                        <li style="transform: translateX(20px);">
                            <h3 class="nav__list-item-link">Страницы памяти.РФ</h1>
                        </li>
                        <li class="nav__list-item">
                            <a href="/veteran/" class="nav__list-item-link">Стена памяти</a>
                        </li>
                        <li class="nav__list-item">
                            <a href="/add_veteran.php" class="nav__list-item-link">Добавить историю</a>
                        </li>
                        <li class="nav__list-item">
                            <a href="#" class="nav__list-item-link">Карта памяти</a>
                        </li>
                        <li class="nav__list-item">
                            <div class="btn-group">
                                  <button type="button" class="btn btn-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Дополнительно
                                  </button>
                                  <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">В разработке</a></li>
                                    <li><a class="dropdown-item" href="#">В разработке</a></li>
                                    <li><a class="dropdown-item" href="#">В разработке</a></li>
                                  </ul>
                            </div>
                        </li>
                    </ul>
                </nav>
            </div>
        </header>