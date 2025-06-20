<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Работа найдётся для каждого</title>
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
        
        .hero {
            text-align: center;
            padding: 100px 20px;
        }
        
        .hero h1 {
            font-size: 48px;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .search-box {
            max-width: 800px;
            margin: 0 auto 50px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 30px;
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
        
        .categories {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
        }
        
        .category {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 15px 25px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .category:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-5px);
        }
        
        .stats {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 50px 0;
            flex-wrap: wrap;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .phone-input {
            text-align: center;
            margin: 50px 0;
        }
        
        .phone-input h3 {
            margin-bottom: 20px;
            font-size: 24px;
        }
        
        .phone-input input {
            padding: 15px;
            width: 300px;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            text-align: center;
            outline: none;
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
        
        /* Личный кабинет */
        .dashboard {
            display: none;
            padding: 50px 5%;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 10px;
        }
        
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #23a6d5;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 30px;
            color: white;
            margin-right: 20px;
        }
        
        .profile-info h2 {
            margin-bottom: 5px;
        }
        
        .profile-info p {
            opacity: 0.8;
        }
        
        .dashboard-content {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .dashboard-section {
            flex: 1;
            min-width: 300px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 10px;
        }
        
        .dashboard-section h3 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .vacancy-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .vacancy-item h4 {
            margin-bottom: 5px;
        }
        
        .vacancy-item p {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 10px;
        }
        
        .vacancy-item .salary {
            color: #23d5ab;
            font-weight: bold;
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
            
            .hero h1 {
                font-size: 36px;
            }
            
            .search-box {
                padding: 20px;
            }
            
            .search-box input,
            .search-box button {
                width: 100%;
                border-radius: 30px;
                margin-bottom: 10px;
            }
            
            .phone-input input {
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
        
        <!-- Личный кабинет (скрыт по умолчанию) -->
        <section class="dashboard" id="dashboard">
            <div class="profile-header">
                <div class="profile-pic" id="user-avatar">ИИ</div>
                <div class="profile-info">
                    <h2 id="user-name">Иван Иванов</h2>
                    <p id="user-email">ivan@example.com</p>
                </div>
            </div>
            
            <div class="dashboard-content">
                <div class="dashboard-section">
                    <h3>Рекомендованные вакансии</h3>
                    <div class="vacancy-item">
                        <h4>Frontend разработчик (React)</h4>
                        <p>ООО "Технологии будущего" · Москва · Опыт от 3 лет</p>
                        <p class="salary">150 000 - 220 000 ₽</p>
                    </div>
                    <div class="vacancy-item">
                        <h4>Менеджер по продажам</h4>
                        <p>ПАО "Сбербанк" · Санкт-Петербург · Опыт от 1 года</p>
                        <p class="salary">90 000 - 120 000 ₽ + бонусы</p>
                    </div>
                    <div class="vacancy-item">
                        <h4>UX/UI дизайнер</h4>
                        <p>Яндекс · Удалённо · Опыт от 2 лет</p>
                        <p class="salary">140 000 - 180 000 ₽</p>
                    </div>
                </div>
                
                <div class="dashboard-section">
                    <h3>Ваши отклики</h3>
                    <div class="vacancy-item">
                        <h4>Маркетолог</h4>
                        <p>ООО "МаркетПром" · Москва · Отправлено 12.05.2023</p>
                        <p>Статус: <span style="color: #23a6d5;">Рассматривается</span></p>
                    </div>
                    <div class="vacancy-item">
                        <h4>Аналитик данных</h4>
                        <p>Тинькофф · Москва · Отправлено 05.05.2023</p>
                        <p>Статус: <span style="color: #23d5ab;">Приглашение на собеседование</span></p>
                    </div>
                </div>
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
                    <li><a href="#">Главная</a></li>
                    <li><a href="#">О нас</a></li>
                    <li><a href="#">Контакты</a></li>
                    <li><a href="#">Соискателям</a></li>
                    <li><a href="#">Работодателям</a></li>
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
            
            fetch('auth.php', {
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
                console.error('Error:', error);
                alert('Произошла ошибка при авторизации');
            });
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
            // Для демонстрации просто закроем модальное окно и покажем личный кабинет
            registerModal.style.display = 'none';
            
            // Заполняем данные пользователя
            userName.textContent = name;
            userEmail.textContent = email;
            userAvatar.textContent = name.substring(0, 2).toUpperCase();
            
            // Показываем личный кабинет
            dashboard.style.display = 'block';
            
            // Скрываем кнопки входа/регистрации и показываем кнопку выхода
            document.querySelector('.auth-buttons').innerHTML = `
                <button class="logout" id="logout-btn">Выйти</button>
            `;
            
            // Обработка выхода
            document.getElementById('logout-btn').addEventListener('click', () => {
                dashboard.style.display = 'none';
                document.querySelector('.auth-buttons').innerHTML = `
                    <button class="login" id="login-btn">Войти</button>
                    <button class="register" id="register-btn">Регистрация</button>
                `;
                
                // Перепривязываем события после пересоздания кнопок
                document.getElementById('login-btn').addEventListener('click', () => {
                    loginModal.style.display = 'flex';
                });
                document.getElementById('register-btn').addEventListener('click', () => {
                    registerModal.style.display = 'flex';
                });
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
        
        document.getElementById('seekers-link').addEventListener('click', (e) => {
            e.preventDefault();
            alert('Раздел "Соискателям" в разработке');
        });
        
        document.getElementById('employers-link').addEventListener('click', (e) => {
            e.preventDefault();
            alert('Раздел "Работодателям" в разработке');
        });
    </script>
</body>
</html>