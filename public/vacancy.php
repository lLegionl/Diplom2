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
    <link rel="stylesheet" href="<?=URL_ROOT . '/css/vacancy.css'?>">
</head>
<body>
    <?php include 'includes/header.php';?>
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
    
    
    <?php include 'includes/footer.php'; ?>
        
    
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
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>
            <form id="apply-form" method="POST">
                <input type="hidden" name="apply" value="1">
                <div class="form-group">
                    <label>Вакансия: <?= htmlspecialchars($vacancy['profession']) ?></label>
                </div>
                <div class="form-group">
                    <label>Компания: <?= htmlspecialchars($vacancy['company_id']) ?></label>
                </div>
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