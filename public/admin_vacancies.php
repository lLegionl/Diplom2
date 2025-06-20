<?php
// admin_vacancies.php
require_once 'db.php';
require_once 'config.php';

// Проверка прав администратора (добавьте свою логику проверки)
requireLogin();
// if (!isAdmin()) { header('Location: index.php'); exit(); }

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        // Добавление новой вакансии
        $data = [
            'profession' => $_POST['profession'],
            'description' => $_POST['description'],
            'salary_from' => $_POST['salary_from'] ?: null,
            'salary_to' => $_POST['salary_to'] ?: null,
            'salary_currency' => $_POST['salary_currency'],
            'location' => $_POST['location'],
            'remote_available' => isset($_POST['remote_available']) ? 1 : 0,
            'experience' => $_POST['experience'],
            'employment_type' => $_POST['employment_type'],
            'work_schedule' => $_POST['work_schedule']
        ];
        
        try {
            $stmt = $pdo->prepare("INSERT INTO job (profession, description, salary_from, salary_to, salary_currency, 
                                      location, remote_available, experience, employment_type, work_schedule) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(array_values($data));
            $success = "Вакансия успешно добавлена!";
        } catch (PDOException $e) {
            $error = "Ошибка при добавлении вакансии: " . $e->getMessage();
        }
    } elseif (isset($_POST['edit'])) {
        // Редактирование вакансии
        $id = (int)$_POST['id'];
        $data = [
            'profession' => $_POST['profession'],
            'description' => $_POST['description'],
            'salary_from' => $_POST['salary_from'] ?: null,
            'salary_to' => $_POST['salary_to'] ?: null,
            'salary_currency' => $_POST['salary_currency'],
            'location' => $_POST['location'],
            'remote_available' => isset($_POST['remote_available']) ? 1 : 0,
            'experience' => $_POST['experience'],
            'employment_type' => $_POST['employment_type'],
            'work_schedule' => $_POST['work_schedule'],
            'id' => $id
        ];
        
        try {
            $stmt = $pdo->prepare("UPDATE job SET 
                profession = ?, description = ?, salary_from = ?, salary_to = ?, salary_currency = ?,
                company_id = ?, location = ?, remote_available = ?, experience = ?, employment_type = ?, work_schedule = ?
                WHERE id = ?");
            $stmt->execute(array_values($data));
            $success = "Вакансия успешно обновлена!";
        } catch (PDOException $e) {
            $error = "Ошибка при обновлении вакансии: " . $e->getMessage();
        }
    }
} elseif (isset($_GET['delete'])) {
    // Удаление вакансии
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM job WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Вакансия успешно удалена!";
    } catch (PDOException $e) {
        $error = "Ошибка при удалении вакансии: " . $e->getMessage();
    }
}

// Получение списка вакансий
try {
    $stmt = $pdo->query("SELECT * FROM job ORDER BY created_at DESC");
    $vacancies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка при получении вакансий: " . $e->getMessage());
}

// Получение данных для редактирования (если передан ID)
$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM job WHERE id = ?");
        $stmt->execute([$id]);
        $editData = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Ошибка при получении данных вакансии: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление вакансиями | JobFinder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        .alert-danger {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
        }
        .checkbox-group input {
            width: auto;
            margin-right: 10px;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-primary:hover {
            background-color: #45a049;
        }
        .btn-danger {
            background-color: #f44336;
            color: white;
        }
        .btn-danger:hover {
            background-color: #d32f2f;
        }
        .btn-edit {
            background-color: #2196F3;
            color: white;
        }
        .btn-edit:hover {
            background-color: #0b7dda;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Управление вакансиями</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <h2><?= $editData ? 'Редактировать вакансию' : 'Добавить новую вакансию' ?></h2>
        
        <form method="POST">
            <?php if ($editData): ?>
                <input type="hidden" name="id" value="<?= $editData['id'] ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="profession">Профессия/Должность:</label>
                <input type="text" id="profession" name="profession" 
                       value="<?= htmlspecialchars($editData['profession'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Описание:</label>
                <textarea id="description" name="description" required><?= 
                    htmlspecialchars($editData['description'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Зарплата:</label>
                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label for="salary_from">От:</label>
                        <input type="number" id="salary_from" name="salary_from" 
                               value="<?= $editData['salary_from'] ?? '' ?>">
                    </div>
                    <div style="flex: 1;">
                        <label for="salary_to">До:</label>
                        <input type="number" id="salary_to" name="salary_to" 
                               value="<?= $editData['salary_to'] ?? '' ?>">
                    </div>
                    <div style="flex: 1;">
                        <label for="salary_currency">Валюта:</label>
                        <select id="salary_currency" name="salary_currency">
                            <option value="RUB" <?= ($editData['salary_currency'] ?? 'RUB') === 'RUB' ? 'selected' : '' ?>>RUB</option>
                            <option value="USD" <?= ($editData['salary_currency'] ?? '') === 'USD' ? 'selected' : '' ?>>USD</option>
                            <option value="EUR" <?= ($editData['salary_currency'] ?? '') === 'EUR' ? 'selected' : '' ?>>EUR</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="company_id">ID компании:</label>
                <input type="number" id="company_id" name="company_id" 
                       value="<?= $editData['company_id'] ?? '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="location">Местоположение:</label>
                <input type="text" id="location" name="location" 
                       value="<?= htmlspecialchars($editData['location'] ?? '') ?>">
            </div>
            
            <div class="form-group checkbox-group">
                <input type="checkbox" id="remote_available" name="remote_available" 
                       <?= ($editData['remote_available'] ?? 0) ? 'checked' : '' ?>>
                <label for="remote_available">Доступна удалённая работа</label>
            </div>
            
            <div class="form-group">
                <label for="experience">Требуемый опыт:</label>
                <input type="text" id="experience" name="experience" 
                       value="<?= htmlspecialchars($editData['experience'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="employment_type">Тип занятости:</label>
                <input type="text" id="employment_type" name="employment_type" 
                       value="<?= htmlspecialchars($editData['employment_type'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="work_schedule">График работы:</label>
                <input type="text" id="work_schedule" name="work_schedule" 
                       value="<?= htmlspecialchars($editData['work_schedule'] ?? '') ?>">
            </div>
            
            <button type="submit" name="<?= $editData ? 'edit' : 'add' ?>" class="btn btn-primary">
                <?= $editData ? 'Обновить вакансию' : 'Добавить вакансию' ?>
            </button>
            
            <?php if ($editData): ?>
                <a href="admin_vacancies.php" class="btn btn-danger">Отмена</a>
            <?php endif; ?>
        </form>
        
        <h2>Список вакансий</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Профессия</th>
                    <th>Зарплата</th>
                    <th>Местоположение</th>
                    <th>Дата создания</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vacancies as $vacancy): ?>
                <tr>
                    <td><?= $vacancy['id'] ?></td>
                    <td><?= htmlspecialchars($vacancy['profession']) ?></td>
                    <td>
                        <?= $vacancy['salary_from'] ? number_format($vacancy['salary_from'], 0, '', ' ') : '' ?>
                        <?= $vacancy['salary_from'] && $vacancy['salary_to'] ? ' - ' : '' ?>
                        <?= $vacancy['salary_to'] ? number_format($vacancy['salary_to'], 0, '', ' ') : '' ?>
                        <?= ($vacancy['salary_from'] || $vacancy['salary_to']) ? $vacancy['salary_currency'] : 'не указана' ?>
                    </td>
                    <td><?= htmlspecialchars($vacancy['location']) ?><?= $vacancy['remote_available'] ? ' (удалённо)' : '' ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($vacancy['created_at'])) ?></td>
                    <td class="action-buttons">
                        <a href="admin_vacancies.php?edit=<?= $vacancy['id'] ?>" class="btn btn-edit">
                            <i class="fas fa-edit"></i> Изменить
                        </a>
                        <a href="admin_vacancies.php?delete=<?= $vacancy['id'] ?>" class="btn btn-danger" 
                           onclick="return confirm('Вы уверены, что хотите удалить эту вакансию?')">
                            <i class="fas fa-trash"></i> Удалить
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>