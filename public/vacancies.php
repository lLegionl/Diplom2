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
    <link rel="stylesheet" href="<?=URL_ROOT . '/css/vacansies.css'?>">
</head>
<body>
    <?php include 'includes/header.php';?>
    
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
    
    <script>
        // Обработчик выхода (универсальный для всех страниц)
        function handleLogout() {
            fetch('auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=logout'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'index.php'; // Перенаправляем на главную
                }
            })
            .catch(error => {
                console.error('Ошибка при выходе:', error);
            });
        }

        // Назначаем обработчик на кнопку выхода
        document.addEventListener('DOMContentLoaded', function() {
            const logoutBtn = document.getElementById('logout-btn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    handleLogout();
                });
            }
        });

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
            
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=login&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loginModal.style.display = 'none';
                    updateUserProfile(data.data);
                    showDashboard();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                window.location.reload();
                console.error('Error:', error);
            });
        });
        
        // Обработка формы регистрации
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('register-name').value;
            const email = document.getElementById('register-email').value;
            const phone = document.getElementById('register-phone').value;
            const password = document.getElementById('register-password').value;
            const confirm = document.getElementById('register-confirm').value;
            
            if (password !== confirm) {
                alert('Пароли не совпадают!');
                return;
            }
            
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=register&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}&password=${encodeURIComponent(password)}&confirm=${encodeURIComponent(confirm)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    registerModal.style.display = 'none';
                    updateUserProfile(data.data);
                    showDashboard();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                window.location.reload();
                console.error('Error:', error);
            });
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