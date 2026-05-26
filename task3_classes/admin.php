<?php

session_start();

require_once 'ConferenceApplication.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idsToDelete = $_POST['ids'] ?? [];

    if (!empty($idsToDelete)) {
        ConferenceApplication::markDeleted($idsToDelete);
        $_SESSION['admin_message'] = 'Выбранные заявки помечены как удаленные';
    }

    header('Location: admin.php');
    exit;
}

$applications = ConferenceApplication::readActive();

$message = $_SESSION['admin_message'] ?? '';
unset($_SESSION['admin_message']);

function safe($value) {
    return ConferenceApplication::safe($value);
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заявки участников</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="page">
    <div class="admin-card wide">
        <h1>Заявки участников</h1>
        <p class="subtitle">Список активных заявок на конференцию</p>

        <?php if ($message !== ''): ?>
            <div class="success">
                <?= safe($message) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($applications)): ?>
            <div class="empty">
                Активных заявок пока нет.
            </div>
        <?php else: ?>
            <form method="post">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Дата</th>
                                <th>IP-адрес</th>
                                <th>Имя</th>
                                <th>Фамилия</th>
                                <th>Email</th>
                                <th>Телефон</th>
                                <th>Тематика</th>
                                <th>Оплата</th>
                                <th>Рассылка</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($applications as $application): ?>
                                <tr>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="ids[]"
                                            value="<?= safe($application->id) ?>"
                                        >
                                    </td>
                                    <td><?= safe($application->date) ?></td>
                                    <td><?= safe($application->ip) ?></td>
                                    <td><?= safe($application->name) ?></td>
                                    <td><?= safe($application->surname) ?></td>
                                    <td><?= safe($application->email) ?></td>
                                    <td><?= safe($application->phone) ?></td>
                                    <td><?= safe($application->topic) ?></td>
                                    <td><?= safe($application->payment) ?></td>
                                    <td><?= safe($application->newsletter) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <button class="delete-btn" type="submit">Удалить выбранные</button>
            </form>
        <?php endif; ?>

        <a class="admin-link" href="index.php">Вернуться к форме</a>
    </div>
</div>

</body>
</html>
