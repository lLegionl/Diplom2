<?php
require_once 'db.php';
include 'config.php';

requireLogin();

if (!isset($_GET['id'])) {
    header('Location: vacancies.php');
    exit();
}

$vacancyId = (int)$_GET['id'];

// Получаем данные о вакансии
$stmt = $pdo->prepare("SELECT * FROM job WHERE id = :id");
$stmt->execute(['id' => $vacancyId]);
$vacancy = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vacancy) {
    header('Location: vacancies.php');
    exit();
}

// Получаем похожие вакансии
$stmt = $pdo->prepare("SELECT * FROM job WHERE id != :id ORDER BY RAND() LIMIT 3");
$stmt->execute(['id' => $vacancyId]);
$similarVacancies = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Обработка формы отклика
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit();
    }
    
    $userId = $_SESSION['user_id'];
    $jobId = $vacancyId;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO response (id_user, id_job, status, date_response)
            VALUES (:user_id, :job_id, 1, 'Отклик')
        ");
        $stmt->execute([
            'user_id' => $userId,
            'job_id' => $jobId
        ]);
        
        header('Location: vacancy.php?id=' . $jobId . '&applied=1');
        exit();
    } catch (PDOException $e) {
        $error = "Ошибка при отправке отклика: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frontend разработчик (React) | JobFinder</title>
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
        
        .vacancy-container {
            padding: 50px 5%;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .vacancy-header {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .vacancy-title {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .vacancy-company {
            font-size: 20px;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        .vacancy-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
        }
        
        .meta-item i {
            margin-right: 8px;
            color: #23d5ab;
        }
        
        .vacancy-salary {
            font-size: 24px;
            font-weight: bold;
            color: #23d5ab;
            margin: 20px 0;
        }
        
        .apply-btn {
            background: #23a6d5;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 20px;
        }
        
        .apply-btn:hover {
            background: #1e8ab8;
        }
        
        .vacancy-content {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 22px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .vacancy-description {
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .requirements-list, .duties-list {
            margin-left: 20px;
            margin-bottom: 30px;
        }
        
        .requirements-list li, .duties-list li {
            margin-bottom: 10px;
        }
        
        .conditions-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .condition-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 8px;
        }
        
        .condition-item i {
            margin-right: 10px;
            color: #23d5ab;
        }
        
        .similar-vacancies {
            margin-top: 50px;
        }
        
        .similar-title {
            font-size: 24px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .similar-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .similar-item {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .similar-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .similar-item h4 {
            margin-bottom: 10px;
        }
        
        .similar-item p {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 10px;
        }
        
        .similar-salary {
            color: #23d5ab;
            font-weight: bold;
        }
        
        .similar-link {
            display: inline-block;
            margin-top: 10px;
            color: #23a6d5;
            text-decoration: none;
            font-weight: 500;
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
            
            .vacancy-title {
                font-size: 24px;
            }
            
            .vacancy-company {
                font-size: 18px;
            }
            
            .vacancy-salary {
                font-size: 20px;
            }
            
            .apply-btn {
                width: 100%;
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
        <section class="vacancy-container">
            <div class="vacancy-header">
                <h1 class="vacancy-title"><?= htmlspecialchars($vacancy['profession']) ?></h1>
                
                <div class="vacancy-meta">
                    <div class="meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($vacancy['location']) ?><?= $vacancy['remote_available'] ? ' · Удалённо' : '' ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-briefcase"></i>
                        <span><?= htmlspecialchars($vacancy['experience']) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <span><?= htmlspecialchars($vacancy['employment_type']) ?></span>
                    </div>
                </div>
                
                <p class="vacancy-salary">
                    <?= $vacancy['salary_from'] ? number_format($vacancy['salary_from'], 0, '', ' ') : '' ?>
                    <?= $vacancy['salary_from'] && $vacancy['salary_to'] ? ' - ' : '' ?>
                    <?= $vacancy['salary_to'] ? number_format($vacancy['salary_to'], 0, '', ' ') : '' ?>
                    <?= ($vacancy['salary_from'] || $vacancy['salary_to']) ? $vacancy['salary_currency'] : 'Зарплата не указана' ?>
                </p>
                
                <button class="apply-btn" id="apply-btn">Откликнуться</button>
            </div>
            
            <div class="vacancy-content">
                <h3 class="section-title">Описание вакансии</h3>
                <div class="vacancy-description">
                    <?= nl2br(htmlspecialchars($vacancy['description'])) ?>
                </div>
            </div>
            
            <?php if (!empty($similarVacancies)): ?>
            <div class="similar-vacancies">
                <h3 class="similar-title">Похожие вакансии</h3>
                <div class="similar-list">
                    <?php foreach ($similarVacancies as $similar): ?>
                    <div class="similar-item">
                        <h4><?= htmlspecialchars($similar['profession']) ?></h4>
                        <p><?= htmlspecialchars($similar['location']) ?><?= $similar['remote_available'] ? ' · Удалённо' : '' ?></p>
                        <p class="similar-salary">
                            <?= $similar['salary_from'] ? number_format($similar['salary_from'], 0, '', ' ') : '' ?>
                            <?= $similar['salary_from'] && $similar['salary_to'] ? ' - ' : '' ?>
                            <?= $similar['salary_to'] ? number_format($similar['salary_to'], 0, '', ' ') : '' ?>
                            <?= ($similar['salary_from'] || $similar['salary_to']) ? $similar['salary_currency'] : 'Зарплата не указана' ?>
                        </p>
                        <a href="vacancy.php?id=<?= $similar['id'] ?>" class="similar-link">Подробнее →</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
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
    
    <!-- Модальное окно отклика -->
    <div class="modal" id="apply-modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Отклик на вакансию</h2>
            <?php if (isset($error)): ?>
            <div style="color: red; margin-bottom: 15px;"><?= $error ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['applied'])): ?>
            <div style="color: green; margin-bottom: 15px;">Ваш отклик успешно отправлен!</div>
            <?php endif; ?>
            <form id="apply-form" method="POST">
                <input type="hidden" name="apply" value="1">
                <button type="submit" class="submit-btn">Подтвердить отклик</button>
            </form>
        </div>
    </div>
    
    <script>
        // Элементы DOM
        const loginBtn = document.getElementById('login-btn');
        const registerBtn = document.getElementById('register-btn');
        const applyBtn = document.getElementById('apply-btn');
        const loginModal = document.getElementById('login-modal');
        const registerModal = document.getElementById('register-modal');
        const applyModal = document.getElementById('apply-modal');
        const closeBtns = document.querySelectorAll('.close-btn');
        const showRegister = document.getElementById('show-register');
        const showLogin = document.getElementById('show-login');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const applyForm = document.getElementById('apply-form');
        
        // Показать модальное окно входа
        loginBtn.addEventListener('click', () => {
            loginModal.style.display = 'flex';
        });
        
        // Показать модальное окно регистрации
        registerBtn.addEventListener('click', () => {
            registerModal.style.display = 'flex';
        });
        
        // Показать модальное окно отклика
        applyBtn.addEventListener('click', () => {
            applyModal.style.display = 'flex';
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
                applyModal.style.display = 'none';
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
            if (e.target === applyModal) {
                applyModal.style.display = 'none';
            }
        });
        
        // Обработка формы входа
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            
            // Здесь должна быть проверка данных на сервере
            loginModal.style.display = 'none';
            alert('Вход выполнен успешно! Теперь вы можете откликнуться на вакансию.');
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
            alert('Регистрация прошла успешно! Теперь вы можете откликнуться на вакансию.');
        });
        
        // Обработка формы отклика
        applyForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('apply-name').value;
            const email = document.getElementById('apply-email').value;
            const phone = document.getElementById('apply-phone').value;
            const message = document.getElementById('apply-message').value;
            
            // Здесь должна быть отправка данных на сервер
            applyModal.style.display = 'none';
            alert('Ваш отклик успешно отправлен! Работодатель свяжется с вами в ближайшее время.');
            
            // В реальном приложении здесь нужно добавить запрос к серверу для сохранения отклика
            // и обновления списка откликов в личном кабинете
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
        
        // Получение ID вакансии из URL
        const urlParams = new URLSearchParams(window.location.search);
        const vacancyId = urlParams.get('id');
        
        // В реальном приложении здесь должен быть запрос к серверу
        // для получения данных о конкретной вакансии по её ID
        console.log('Загрузка данных вакансии с ID:', vacancyId);
    </script>
</body>
</html>