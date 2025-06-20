<?php
// profile.php
require_once 'db.php';
require_once 'config.php';
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Получение данных пользователя
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

    // Получаем отклики пользователя с информацией о вакансиях
    $responsesStmt = $pdo->prepare("
        SELECT r.id, r.date_response, r.status, 
            j.profession, j.company_id, j.location, j.created_at
        FROM response r
        JOIN job j ON r.id_job = j.id
        WHERE r.id_user = ?
        ORDER BY r.id DESC
    ");
    $responsesStmt->execute([$userId]);
    $responses = $responsesStmt->fetchAll(PDO::FETCH_ASSOC);

// Обработка POST-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_profile':
                $name = trim($_POST['name']);
                $phone = trim($_POST['phone']);
                $email = trim($_POST['email']);
                
                // Валидация
                $errors = [];
                if (empty($name)) $errors[] = 'Имя не может быть пустым';
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Некорректный email';
                
                if (!empty($errors)) {
                    echo json_encode(['success' => false, 'message' => implode('<br>', $errors)]);
                    exit;
                }
                
                // Обновление данных
                $stmt = $pdo->prepare("UPDATE users SET fuulname = ?, email = ?, phone = ? WHERE id = ?");
                $stmt->execute([$name, $email, $phone, $userId]);
                
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                
                echo json_encode(['success' => true, 'message' => 'Данные успешно обновлены']);
                exit;
                
            case 'change_password':
                $currentPassword = $_POST['current_password'];
                $newPassword = $_POST['new_password'];
                $confirmPassword = $_POST['confirm_password'];
                
                // Валидация
                if (!password_verify($currentPassword, $user['password'])) {
                    echo json_encode(['success' => false, 'message' => 'Текущий пароль неверен']);
                    exit;
                }
                
                if (empty($newPassword)) {
                    echo json_encode(['success' => false, 'message' => 'Новый пароль не может быть пустым']);
                    exit;
                }
                
                if ($newPassword !== $confirmPassword) {
                    echo json_encode(['success' => false, 'message' => 'Пароли не совпадают']);
                    exit;
                }
                
                // Обновление пароля
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashedPassword, $userId]);
                
                echo json_encode(['success' => true, 'message' => 'Пароль успешно изменен']);
                exit;
        }
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет | JobFinder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?=URL_ROOT . '/css/profile.css'?>">
</head>
<body>
    <?php include 'includes/header.php';?>
    
    <main class="dashboard">
        <div class="profile-header">
            <div class="profile-pic" id="user-avatar">
                <?= strtoupper(substr($user['fuulname'], 0, 2)) ?>
            </div>
            <div class="profile-info">
                <h2 id="user-name"><?= htmlspecialchars($user['fuulname']) ?></h2>
                <p id="user-email"><?= htmlspecialchars($user['email']) ?></p>
                <p id="user-phone"><?= htmlspecialchars($user['phone']) ?></p>
            </div>
        </div>
        
        <div class="dashboard-content">
            <div class="dashboard-section">
                <h3>Редактирование профиля</h3>
                <form id="profile-form">
                    <div class="form-group">
                        <label for="profile-name">Имя</label>
                        <input type="text" id="profile-name" value="<?= htmlspecialchars($user['fuulname']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="profile-email">Email</label>
                        <input type="email" id="profile-email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="profile-phone">Телефон</label>
                        <input type="tel" id="profile-phone" value="<?= htmlspecialchars($user['phone']) ?>" placeholder="+7 (___) ___-____">
                    </div>
                    <button type="submit" class="submit-btn">Сохранить изменения</button>
                </form>
                
                <h3 style="margin-top: 40px;">Смена пароля</h3>
                <form id="password-form">
                    <div class="form-group">
                        <label for="current-password">Текущий пароль</label>
                        <input type="password" id="current-password" required>
                    </div>
                    <div class="form-group">
                        <label for="new-password">Новый пароль</label>
                        <input type="password" id="new-password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Подтвердите новый пароль</label>
                        <input type="password" id="confirm-password" required>
                    </div>
                    <button type="submit" class="submit-btn">Изменить пароль</button>
                </form>
            </div>
            
            <div class="dashboard-section">
                <h3>Ваши отклики</h3>
                
                <?php if (empty($responses)): ?>
                    <p>У вас пока нет откликов на вакансии.</p>
                <?php else: ?>
                    <?php foreach ($responses as $response): ?>
                        <div class="vacancy-item">
                            <h4><?= htmlspecialchars($response['profession']) ?></h4>
                            <p>
                                <?= htmlspecialchars($response['location']) ?> · 
                                Отправлено <?= date('d.m.Y', strtotime($response['created_at'])) ?>
                            </p>
                            <p>Статус: 
                                <span style="color: 
                                    <?= $response['status'] == 'Собеседования' ? '#23d5ab' : 
                                       ($response['status'] == 'Отклонен' ? '#ff6b6b' : '#23a6d5') ?>">
                                    <?= htmlspecialchars($response['status']) ?>
                                </span>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <!-- Модальное окно успешного обновления -->
    <div class="modal" id="success-modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Успешно!</h2>
            <p id="success-message">Изменения сохранены</p>
        </div>
    </div>

    
    <?php include 'includes/footer.php'; ?>
        


    <script>
        // Обработка выхода
        document.getElementById('logout-btn').addEventListener('click', () => {
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=logout'
            })
            .then(() => {
                window.location.href = 'index.php';
            });
        });
        
        // Обработка формы профиля
        document.getElementById('profile-form').addEventListener('submit', (e) => {
            e.preventDefault();
            
            const name = document.getElementById('profile-name').value;
            const email = document.getElementById('profile-email').value;
            const phone = document.getElementById('profile-phone').value;
            
            fetch('profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_profile&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('user-name').textContent = name;
                    document.getElementById('user-email').textContent = email;
                    if (phone) {
                        document.getElementById('user-phone').textContent = phone;
                    }
                    
                    // Показываем модальное окно успеха
                    document.getElementById('success-message').textContent = data.message;
                    document.getElementById('success-modal').style.display = 'flex';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка при обновлении профиля');
            });
        });
        
        // Обработка формы смены пароля
        document.getElementById('password-form').addEventListener('submit', (e) => {
            e.preventDefault();
            
            const currentPassword = document.getElementById('current-password').value;
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            
            fetch('profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=change_password&current_password=${encodeURIComponent(currentPassword)}&new_password=${encodeURIComponent(newPassword)}&confirm_password=${encodeURIComponent(confirmPassword)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Очищаем поля паролей
                    document.getElementById('current-password').value = '';
                    document.getElementById('new-password').value = '';
                    document.getElementById('confirm-password').value = '';
                    
                    // Показываем модальное окно успеха
                    document.getElementById('success-message').textContent = data.message;
                    document.getElementById('success-modal').style.display = 'flex';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка при смене пароля');
            });
        });
        
        // Закрытие модального окна
        document.querySelectorAll('.close-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('success-modal').style.display = 'none';
            });
        });
        
        // Закрытие при клике вне окна
        window.addEventListener('click', (e) => {
            if (e.target === document.getElementById('success-modal')) {
                document.getElementById('success-modal').style.display = 'none';
            }
        });
    </script>
</body>
</html>