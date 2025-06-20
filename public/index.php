<?php
require_once 'db.php';
include 'config.php';
include 'auth.php';

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Работа найдётся для каждого</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/index.css">
</head>
<body>
    <?php include 'includes/header.php';?>
    
    <main>
        <section class="hero">
            <h1>Работа найдётся для каждого</h1>
            <div class="search-box">
                <input type="text" placeholder="Профессия, должность или компания">
                <button>Найти</button>
            </div>
            <div class="categories">
                <div class="category">Высокооплачиваемая работа</div>
                <div class="category">Компании для вас</div>
                <div class="category">Работа из дома</div>
                <div class="category">Подработка</div>
            </div>
        </section>
        
        <div class="stats">
            <div class="stat-item">
                <div class="stat-number">8,280,342</div>
                <div class="stat-label">активных вакансий</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">1,163,568</div>
                <div class="stat-label">компаний</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">2,229,791</div>
                <div class="stat-label">резюме</div>
            </div>
        </div>
        
        <div class="phone-input">
            <h3>Напишите телефон, чтобы работодатели могли предложить вам работу</h3>
            <input type="tel" placeholder="+7 (___) ___-____">
        </div>
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
                    <label for="register-phone">Телефон</label>
                    <input type="tel" id="register-phone" placeholder="+7 (___) ___-____">
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
        const dashboard = document.getElementById('dashboard');
        const userName = document.getElementById('user-name');
        const userEmail = document.getElementById('user-email');
        const userAvatar = document.getElementById('user-avatar');
        
        // Проверка авторизации при загрузке страницы
        document.addEventListener('DOMContentLoaded', checkAuth);
        
        // Функция проверки авторизации
        function checkAuth() {
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=check'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateUserProfile(data.data);
                    showDashboard();
                }
            });
        }
        
        // Функция обновления профиля пользователя
        function updateUserProfile(userData) {
            userName.textContent = userData.name;
            userEmail.textContent = userData.email;
            userAvatar.textContent = userData.name.substring(0, 2).toUpperCase();
        }
        
        
        function logout() {
            fetch('auth.php', {  // Изменено с index.php на auth.php
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=logout'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();  // Просто перезагружаем страницу
                }
            });
        }        
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