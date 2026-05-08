<?php
$separator = '||';
$filePath = 'applications.txt';

function safe($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function readApplications($filePath, $separator) {
    if (!file_exists($filePath)) {
        return [];
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $applications = [];

    foreach ($lines as $line) {
        $data = explode($separator, $line);

        if (count($data) !== 11) {
            continue;
        }

        $applications[] = [
            'id' => $data[0],
            'date' => $data[1],
            'ip' => $data[2],
            'name' => $data[3],
            'surname' => $data[4],
            'email' => $data[5],
            'phone' => $data[6],
            'topic' => $data[7],
            'payment' => $data[8],
            'newsletter' => $data[9],
            'status' => $data[10]
        ];
    }

    return $applications;
}

function saveApplications($filePath, $separator, $applications) {
    $lines = [];

    foreach ($applications as $application) {
        $lines[] = implode($separator, [
            $application['id'],
            $application['date'],
            $application['ip'],
            $application['name'],
            $application['surname'],
            $application['email'],
            $application['phone'],
            $application['topic'],
            $application['payment'],
            $application['newsletter'],
            $application['status']
        ]);
    }

    file_put_contents($filePath, implode(PHP_EOL, $lines) . PHP_EOL);
}

$applications = readApplications($filePath, $separator);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idsToDelete = $_POST['ids'] ?? [];

    foreach ($applications as &$application) {
        if (in_array($application['id'], $idsToDelete)) {
            $application['status'] = 'deleted';
        }
    }

    unset($application);

    saveApplications($filePath, $separator, $applications);

    $applications = readApplications($filePath, $separator);
}

$activeApplications = [];

foreach ($applications as $application) {
    if ($application['status'] !== 'deleted') {
        $activeApplications[] = $application;
    }
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

        <?php if (empty($activeApplications)): ?>
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
                            <?php foreach ($activeApplications as $application): ?>
                                <tr>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="ids[]"
                                            value="<?= safe($application['id']) ?>"
                                        >
                                    </td>
                                    <td><?= safe($application['date']) ?></td>
                                    <td><?= safe($application['ip']) ?></td>
                                    <td><?= safe($application['name']) ?></td>
                                    <td><?= safe($application['surname']) ?></td>
                                    <td><?= safe($application['email']) ?></td>
                                    <td><?= safe($application['phone']) ?></td>
                                    <td><?= safe($application['topic']) ?></td>
                                    <td><?= safe($application['payment']) ?></td>
                                    <td><?= safe($application['newsletter']) ?></td>
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
