<?php
$applicationsDir = 'applications';

if (!is_dir($applicationsDir)) {
    mkdir($applicationsDir);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filesToDelete = $_POST['files'] ?? [];

    foreach ($filesToDelete as $file) {
        $filePath = $applicationsDir . '/' . basename($file);

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}

$files = glob($applicationsDir . '/*.json');

function safe($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
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
    <div class="admin-card">
        <h1>Заявки участников</h1>
        <p class="subtitle">Список всех отправленных заявок</p>

        <?php if (empty($files)): ?>
            <div class="empty">
                Пока нет ни одной заявки.
            </div>
        <?php else: ?>
            <form method="post">
                <div class="applications-list">
                    <?php foreach ($files as $file): ?>
                        <?php
                        $data = json_decode(file_get_contents($file), true);
                        $fileName = basename($file);
                        ?>

                        <div class="application-item">
                            <label class="application-check">
                                <input type="checkbox" name="files[]" value="<?= safe($fileName) ?>">
                            </label>

                            <div class="application-content">
                                <h2>
                                    <?= safe($data['name']) ?>
                                    <?= safe($data['surname']) ?>
                                </h2>

                                <p><b>Email:</b> <?= safe($data['email']) ?></p>
                                <p><b>Телефон:</b> <?= safe($data['phone']) ?></p>
                                <p><b>Тематика:</b> <?= safe($data['topic']) ?></p>
                                <p><b>Оплата:</b> <?= safe($data['payment']) ?></p>
                                <p><b>Рассылка:</b> <?= safe($data['newsletter']) ?></p>
                                <p><b>Дата отправки:</b> <?= safe($data['date']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button class="delete-btn" type="submit">Удалить выбранные</button>
            </form>
        <?php endif; ?>

        <a class="admin-link" href="index.php">Вернуться к форме</a>
    </div>
</div>

</body>
</html>
