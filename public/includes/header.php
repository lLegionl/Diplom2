    <style>
            .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .profile-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .profile-link:hover {
            color: #23d5ab;
        }

        .profile-link i {
            font-size: 18px;
        }

        .logout-btn {
            padding: 8px 20px;
            border-radius: 20px;
            border: none;
            background: white;
            color: #23a6d5;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }

    </style>
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
        <div class="auth-section">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-profile">
                    <a href="profile.php" class="profile-link">
                        <i class="fas fa-user"></i>
                        <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    </a>
                    <button id="logout-btn" class="logout-btn">Выйти</button>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <button class="login" id="login-btn">Войти</button>
                    <button class="register" id="register-btn">Регистрация</button>
                </div>
            <?php endif; ?>
        </div>
    </header>
