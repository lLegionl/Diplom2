<? 
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                    // Уничтожаем сессию полностью
                    $_SESSION = array();
                    if (ini_get("session.use_cookies")) {
                        $params = session_get_cookie_params();
                        setcookie(session_name(), '', time() - 42000,
                            $params["path"], $params["domain"],
                            $params["secure"], $params["httponly"]
                        );
                    }
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
