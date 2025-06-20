<?
function refreshPage($delay = 0) {
    // Проверяем, не были ли уже отправлены заголовки
    if (!headers_sent()) {
        // Если указана задержка, используем JavaScript для обновления
        if ($delay > 0) {
            echo "<script>
                setTimeout(function() {
                    window.location.reload();
                }, " . ($delay * 1000) . ");
            </script>";
        } else {
            // Немедленное обновление через PHP
            header("Refresh:0");
        }
    } else {
        // Если заголовки уже отправлены, используем JavaScript
        echo "<script>window.location.reload();</script>";
    }
    exit; // Завершаем выполнение скрипта
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    refreshPage(2);
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        global $pdo;
        
        switch ($_POST['action']) {
            case 'register':
                $name = trim($_POST['name']);
                $email = trim($_POST['email']);
                $password = $_POST['password'];
                $confirm = $_POST['confirm'];
                $phone = trim($_POST['phone'] ?? '');

                // Валидация
                $errors = [];
                if (empty($name)) $errors[] = 'Имя не может быть пустым';
                if (empty($email)) $errors[] = 'Email не может быть пустым';
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Некорректный email';
                if (empty($password)) $errors[] = 'Пароль не может быть пустым';
                if ($password !== $confirm) $errors[] = 'Пароли не совпадают';

                if (!empty($errors)) {
                    echo json_encode(['success' => false, 'message' => implode('<br>', $errors)]);
                    exit;
                }

                // Проверка существующего пользователя
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    echo json_encode(['success' => false, 'message' => 'Пользователь с таким email уже существует']);
                    exit;
                }

                // Хеширование пароля
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Создание пользователя
                $stmt = $pdo->prepare("INSERT INTO users (username, password, fuulname, email, phone) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$email, $hashedPassword, $name, $email, $phone]);

                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $name;

                echo json_encode(['success' => true, 'message' => 'Регистрация прошла успешно', 'data' => [
                    'name' => $name,
                    'email' => $email
                ]]);
                exit;

            case 'login':
                $email = trim($_POST['email']);
                $password = $_POST['password'];

                // Валидация
                if (empty($email)) {
                    echo json_encode(['success' => false, 'message' => 'Email не может быть пустым']);
                    exit;
                }
                if (empty($password)) {
                    echo json_encode(['success' => false, 'message' => 'Пароль не может быть пустым']);
                    exit;
                }

                // Поиск пользователя
                $stmt = $pdo->prepare("SELECT id, password, fuulname FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if (!$user) {
                    echo json_encode(['success' => false, 'message' => 'Пользователь с таким email не найден']);
                    exit;
                }

                // Проверка пароля
                if (!password_verify($password, $user['password'])) {
                    echo json_encode(['success' => false, 'message' => 'Неверный пароль']);
                    exit;
                }

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $user['fuulname'];

                echo json_encode(['success' => true, 'message' => 'Авторизация прошла успешно', 'data' => [
                    'name' => $user['fuulname'],
                    'email' => $email
                ]]);
                exit;

            case 'logout':
                session_unset();
                session_destroy();
                echo json_encode(['success' => true, 'message' => 'Вы успешно вышли из системы']);
                exit;

            case 'check':
                if (isset($_SESSION['user_id'])) {
                    echo json_encode(['success' => true, 'message' => 'Пользователь авторизован', 'data' => [
                        'name' => $_SESSION['user_name'],
                        'email' => $_SESSION['user_email']
                    ]]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
                }
                exit;
        }
    }
    exit;
}
