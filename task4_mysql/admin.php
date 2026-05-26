<?php

session_start();

require_once 'db.php';
require_once 'ConferenceApplication.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idsToDelete = $_POST['ids'] ?? [];

    if (!empty($idsToDelete)) {
        ConferenceApplication::markDeleted($pdo, $idsToDelete);
        $_SESSION['admin_message'] = 'Выбранные заявки удалены';
    }

    header('Location: admin.php');
    exit;
}

$applications = ConferenceApplication::readActive($pdo);

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
                                    <td><?= safe($application->created_at) ?></td>
                                    <td><?= safe($application->name) ?></td>
                                    <td><?= safe($application->lastname) ?></td>
                                    <td><?= safe($application->email) ?></td>
                                    <td><?= safe($application->tel) ?></td>
                                    <td><?= safe($application->getSubjectName()) ?></td>
                                    <td><?= safe($application->getPaymentName()) ?></td>
                                    <td><?= safe($application->getMailingText()) ?></td>
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
