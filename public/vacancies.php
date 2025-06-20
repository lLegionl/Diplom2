<?php
require_once 'db.php';
include 'config.php';

requireLogin();

// Получаем все вакансии из базы данных
$stmt = $pdo->query("SELECT * FROM job ORDER BY created_at DESC");
$vacancies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все вакансии | JobFinder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: #dde1e6;
            background-image: radial-gradient(#989ea5 1px, transparent 1px);
            background-size: 20px 20px;
            color: #333;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        header {
            padding: 20px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        
        .logo i {
            margin-right: 10px;
            color: #23d5ab;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 30px;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        nav ul li a:hover {
            color: #23d5ab;
        }
        
        .auth-buttons button {
            padding: 8px 20px;
            border-radius: 20px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .login {
            background: transparent;
            color: white;
            border: 1px solid white;
            margin-right: 10px;
        }
        
        .register {
            background: white;
            color: #23a6d5;
        }
        
        .auth-buttons button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .vacancies-container {
            padding: 50px 5%;
        }
        
        .vacancies-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .vacancies-header h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }
        
        .search-box {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
        }
        
        .search-box input {
            width: 70%;
            padding: 15px;
            border: none;
            border-radius: 30px 0 0 30px;
            font-size: 16px;
            outline: none;
        }
        
        .search-box button {
            width: 30%;
            padding: 15px;
            background: #23a6d5;
            color: white;
            border: none;
            border-radius: 0 30px 30px 0;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .search-box button:hover {
            background: #1e8ab8;
        }
        
        .vacancies-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }
        
        .vacancy-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 25px;
            transition: all 0.3s;
        }
        
        .vacancy-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .vacancy-title {
            font-size: 20px;
            margin-bottom: 10px;
            color: #fff;
        }
        
        .vacancy-company {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        
        .vacancy-location {
            display: flex;
            align-items: center;
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 15px;
        }
        
        .vacancy-location i {
            margin-right: 5px;
        }
        
        .vacancy-salary {
            font-size: 18px;
            font-weight: bold;
            color: #23d5ab;
            margin-bottom: 20px;
        }
        
        .vacancy-description {
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .vacancy-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            font-size: 12px;
            opacity: 0.7;
        }
        
        .vacancy-date {
            font-style: italic;
        }
        
        .details-btn {
            background: #23a6d5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: 500;
        }
        
        .details-btn:hover {
            background: #1e8ab8;
        }
        
        footer {
            background: rgba(0, 0, 0, 0.3);
            padding: 50px 5%;
            margin-top: 100px;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .footer-section {
            flex: 1;
            min-width: 200px;
        }
        
        .footer-section h3 {
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        .footer-section ul {
            list-style: none;
        }
        
        .footer-section ul li {
            margin-bottom: 10px;
        }
        
        .footer-section ul li a {
            color: white;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .footer-section ul li a:hover {
            opacity: 1;
        }
        
        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-icons a {
            color: white;
            font-size: 20px;
            transition: transform 0.3s;
        }
        
        .social-icons a:hover {
            transform: translateY(-5px);
        }
        
        .copyright {
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            opacity: 0.7;
        }
        
        /* Модальные окна */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            color: #333;
            position: relative;
            animation: modalFadeIn 0.3s;
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
            color: #777;
        }
        
        .modal h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #23a6d5;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .submit-btn {
            width: 100%;
            padding: 12px;
            background: #23a6d5;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .submit-btn:hover {
            background: #1e8ab8;
        }
        
        .form-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .form-footer a {
            color: #23a6d5;
            text-decoration: none;
        }
        
        /* Адаптивность */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin: 20px 0;
                justify-content: center;
            }
            
            .auth-buttons {
                margin-top: 20px;
            }
            
            .vacancies-header h1 {
                font-size: 28px;
            }
            
            .search-box input,
            .search-box button {
                width: 100%;
                border-radius: 30px;
                margin-bottom: 10px;
            }
            
            .vacancies-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <i class="fas fa-briefcase"></i>
            <span>JobFinder</span>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="vacancies.php" class="active">Вакансии</a></li>
                <li><a href="#" id="about-link">О нас</a></li>
                <li><a href="#" id="contacts-link">Контакты</a></li>
            </ul>
        </nav>
        <div class="auth-buttons">
            <button class="login" id="login-btn">Войти</button>
            <button class="register" id="register-btn">Регистрация</button>
        </div>
    </header>
    
    <main>
        <section class="vacancies-container">
            <div class="vacancies-header">
                <h1>Найдите работу своей мечты</h1>
                <div class="search-box">
                    <input type="text" placeholder="Профессия, должность или компания">
                    <button>Найти</button>
                </div>
            </div>
            
            <div class="vacancies-list">
                <?php foreach ($vacancies as $vacancy): ?>
                <div class="vacancy-card">
                    <h3 class="vacancy-title"><?= htmlspecialchars($vacancy['profession']) ?></h3>
                    <div class="vacancy-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($vacancy['location']) ?><?= $vacancy['remote_available'] ? ' · Удалённо' : '' ?></span>
                    </div>
                    <p class="vacancy-salary">
                        <?= $vacancy['salary_from'] ? number_format($vacancy['salary_from'], 0, '', ' ') : '' ?>
                        <?= $vacancy['salary_from'] && $vacancy['salary_to'] ? ' - ' : '' ?>
                        <?= $vacancy['salary_to'] ? number_format($vacancy['salary_to'], 0, '', ' ') : '' ?>
                        <?= ($vacancy['salary_from'] || $vacancy['salary_to']) ? $vacancy['salary_currency'] : 'Зарплата не указана' ?>
                    </p>
                    <p class="vacancy-description">
                        <?= mb_substr(htmlspecialchars($vacancy['description']), 0, 200) ?>...
                    </p>
                    <div class="vacancy-meta">
                        <span class="vacancy-date">Опубликовано <?= date('d.m.Y', strtotime($vacancy['created_at'])) ?></span>
                        <button class="details-btn" onclick="location.href='vacancy.php?id=<?= $vacancy['id'] ?>'">Подробнее</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
    
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>О компании</h3>
                <p>JobFinder - современная платформа для поиска работы и сотрудников. Мы помогаем людям находить работу мечты, а компаниям - лучших специалистов.</p>
            </div>
            <div class="footer-section">
                <h3>Навигация</h3>
                <ul>
                    <li><a href="hh.php">Главная</a></li>
                    <li><a href="vacancies.php">Вакансии</a></li>
                    <li><a href="#">О нас</a></li>
                    <li><a href="#">Контакты</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Контакты</h3>
                <ul>
                    <li><i class="fas fa-phone"></i> +7 (495) 123-45-67</li>
                    <li><i class="fas fa-envelope"></i> info@jobfinder.ru</li>
                    <li><i class="fas fa-map-marker-alt"></i> Москва, ул. Тверская, 15</li>
                </ul>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-vk"></i></a>
                    <a href="#"><i class="fab fa-telegram"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2023 JobFinder. Все права защищены.</p>
        </div>
    </footer>
    
    <!-- Модальное окно входа -->
    <div class="modal" id="login-modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Вход в аккаунт</h2>
            <form id="login-form">
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Пароль</label>
                    <input type="password" id="login-password" required>
                </div>
                <button type="submit" class="submit-btn">Войти</button>
                <div class="form-footer">
                    Нет аккаунта? <a href="#" id="show-register">Зарегистрироваться</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Модальное окно регистрации -->
    <div class="modal" id="register-modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Регистрация</h2>
            <form id="register-form">
                <div class="form-group">
                    <label for="register-name">Имя</label>
                    <input type="text" id="register-name" required>
                </div>
                <div class="form-group">
                    <label for="register-email">Email</label>
                    <input type="email" id="register-email" required>
                </div>
                <div class="form-group">
                    <label for="register-password">Пароль</label>
                    <input type="password" id="register-password" required>
                </div>
                <div class="form-group">
                    <label for="register-confirm">Подтвердите пароль</label>
                    <input type="password" id="register-confirm" required>
                </div>
                <button type="submit" class="submit-btn">Зарегистрироваться</button>
                <div class="form-footer">
                    Уже есть аккаунт? <a href="#" id="show-login">Войти</a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Элементы DOM
        const loginBtn = document.getElementById('login-btn');
        const registerBtn = document.getElementById('register-btn');
        const loginModal = document.getElementById('login-modal');
        const registerModal = document.getElementById('register-modal');
        const closeBtns = document.querySelectorAll('.close-btn');
        const showRegister = document.getElementById('show-register');
        const showLogin = document.getElementById('show-login');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        
        // Показать модальное окно входа
        loginBtn.addEventListener('click', () => {
            loginModal.style.display = 'flex';
        });
        
        // Показать модальное окно регистрации
        registerBtn.addEventListener('click', () => {
            registerModal.style.display = 'flex';
        });
        
        // Переключение между окнами входа и регистрации
        showRegister.addEventListener('click', (e) => {
            e.preventDefault();
            loginModal.style.display = 'none';
            registerModal.style.display = 'flex';
        });
        
        showLogin.addEventListener('click', (e) => {
            e.preventDefault();
            registerModal.style.display = 'none';
            loginModal.style.display = 'flex';
        });
        
        // Закрытие модальных окон
        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                loginModal.style.display = 'none';
                registerModal.style.display = 'none';
            });
        });
        
        // Закрытие при клике вне окна
        window.addEventListener('click', (e) => {
            if (e.target === loginModal) {
                loginModal.style.display = 'none';
            }
            if (e.target === registerModal) {
                registerModal.style.display = 'none';
            }
        });
        
        // Обработка формы входа
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            
            // Здесь должна быть проверка данных на сервере
            // Для демонстрации просто закроем модальное окно
            loginModal.style.display = 'none';
            alert('Вход выполнен успешно!');
        });
        
        // Обработка формы регистрации
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('register-name').value;
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;
            const confirm = document.getElementById('register-confirm').value;
            
            if (password !== confirm) {
                alert('Пароли не совпадают!');
                return;
            }
            
            // Здесь должна быть отправка данных на сервер
            registerModal.style.display = 'none';
            alert('Регистрация прошла успешно!');
        });
        
        // Обработчики для навигации
        document.getElementById('about-link').addEventListener('click', (e) => {
            e.preventDefault();
            alert('Раздел "О нас" в разработке');
        });
        
        document.getElementById('contacts-link').addEventListener('click', (e) => {
            e.preventDefault();
            alert('Раздел "Контакты" в разработке');
        });
    </script>
</body>
</html>