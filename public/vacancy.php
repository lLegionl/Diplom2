<?php
// vacancy.php
require_once 'db.php';
require_once 'config.php';

requireLogin();

if (!isset($_GET['id'])) {
    header('Location: vacancies.php');
    exit();
}

$vacancyId = (int)$_GET['id'];
$userId = $_SESSION['user_id'];
$error = '';
$success = false;

// Получаем данные о вакансии
try {
    $stmt = $pdo->prepare("SELECT * FROM job WHERE id = ?");
    $stmt->execute([$vacancyId]);
    $vacancy = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vacancy) {
        header('Location: vacancies.php');
        exit();
    }
} catch (PDOException $e) {
    die("Ошибка при получении данных вакансии: " . $e->getMessage());
}

// Проверяем, отправлял ли уже пользователь отклик
try {
    $checkStmt = $pdo->prepare("SELECT id FROM response WHERE id_user = ? AND id_job = ?");
    $checkStmt->execute([$userId, $vacancyId]);
    $alreadyApplied = $checkStmt->fetch();
} catch (PDOException $e) {
    $error = "Ошибка при проверке отклика: " . $e->getMessage();
}

// Обработка формы отклика
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    if ($alreadyApplied) {
        $error = "Вы уже откликались на эту вакансию";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO response (id_user, id_job, status) VALUES (?, ?, 'Отклик')");
            $stmt->execute([$userId, $vacancyId]);
            $success = true;
            $alreadyApplied = true; // Обновляем состояние после успешного отклика
            
            // Записываем в лог
            error_log("User $userId applied to job $vacancyId at " . date('Y-m-d H:i:s'));
        } catch (PDOException $e) {
            $error = "Ошибка при отправке отклика: " . $e->getMessage();
            error_log("Error applying to job: " . $e->getMessage());
        }
    }
}

// Получаем похожие вакансии
try {
    $stmt = $pdo->prepare("SELECT * FROM job WHERE id != ? ORDER BY RAND() LIMIT 3");
    $stmt->execute([$vacancyId]);
    $similarVacancies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $similarVacancies = [];
    $error = "Ошибка при получении похожих вакансий: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($vacancy['profession']) ?> | JobFinder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?=URL_ROOT . '/css/vacancy.css'?>">
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        .apply-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .apply-btn:hover {
            background-color: #45a049;
        }
        .apply-btn.applied {
            background-color: #cccccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
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
                </div>
                
                <p class="vacancy-salary">
                    <?= $vacancy['salary_from'] ? number_format($vacancy['salary_from'], 0, '', ' ') : '' ?>
                    <?= $vacancy['salary_from'] && $vacancy['salary_to'] ? ' - ' : '' ?>
                    <?= $vacancy['salary_to'] ? number_format($vacancy['salary_to'], 0, '', ' ') : '' ?>
                    <?= ($vacancy['salary_from'] || $vacancy['salary_to']) ? $vacancy['salary_currency'] : 'Зарплата не указана' ?>
                </p>
                
                <?php if (!$alreadyApplied): ?>
                    <button class="apply-btn" id="apply-btn">Откликнуться</button>
                <?php else: ?>
                    <button class="apply-btn applied" disabled>Вы уже откликнулись</button>
                <?php endif; ?>
            </div>
            
            <div class="vacancy-content">
                <h3 class="section-title">Описание вакансии</h3>
                <div class="vacancy-description">
                    <?= nl2br(htmlspecialchars($vacancy['description'])) ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Модальное окно отклика -->
    <div class="modal" id="apply-modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px;">
            <span class="close-btn" style="float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            <h2>Отклик на вакансию</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    Ваш отклик успешно отправлен!
                </div>
                <script>
                    setTimeout(() => {
                        document.getElementById('apply-modal').style.display = 'none';
                        window.location.reload(); // Обновляем страницу для обновления состояния кнопки
                    }, 2000);
                </script>
            <?php else: ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="apply-form">
                    <input type="hidden" name="apply" value="1">
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Вакансия:</label>
                        <p style="margin: 0; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                            <?= htmlspecialchars($vacancy['profession']) ?>
                        </p>
                    </div>
                    
                    <button type="submit" class="submit-btn" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                        <i class="fas fa-paper-plane"></i> Подтвердить отклик
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Элементы DOM
        const applyBtn = document.getElementById('apply-btn');
        const applyModal = document.getElementById('apply-modal');
        const closeBtn = document.querySelector('#apply-modal .close-btn');
        
        // Показать модальное окно отклика
        if (applyBtn) {
            applyBtn.addEventListener('click', () => {
                applyModal.style.display = 'block';
            });
        }
        
        // Закрытие модального окна
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                applyModal.style.display = 'none';
            });
        }
        
        // Закрытие при клике вне окна
        window.addEventListener('click', (e) => {
            if (e.target === applyModal) {
                applyModal.style.display = 'none';
            }
        });
    </script>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>