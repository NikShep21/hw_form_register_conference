<?php
$separator = '||';
$filePath = 'applications.txt';

$errors = [];
$success = false;

$name = $_POST['name'] ?? '';
$surname = $_POST['surname'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$topic = $_POST['topic'] ?? '';
$payment = $_POST['payment'] ?? '';
$newsletter = isset($_POST['newsletter']) ? 'Да' : 'Нет';

function safe($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function containsSeparator($value, $separator) {
    return strpos($value, $separator) !== false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($name === '') {
        $errors[] = 'Введите имя';
    }

    if ($surname === '') {
        $errors[] = 'Введите фамилию';
    }

    if ($email === '') {
        $errors[] = 'Введите электронную почту';
    }

    if ($phone === '') {
        $errors[] = 'Введите телефон';
    }

    if ($topic === '') {
        $errors[] = 'Выберите тематику конференции';
    }

    if ($payment === '') {
        $errors[] = 'Выберите способ оплаты';
    }

    $fields = [$name, $surname, $email, $phone, $topic, $payment];

    foreach ($fields as $field) {
        if (containsSeparator($field, $separator)) {
            $errors[] = 'Нельзя использовать разделитель ' . $separator . ' в полях формы';
            break;
        }
    }

    if (empty($errors)) {
        $id = uniqid();
        $date = date('d.m.Y H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? getenv('REMOTE_ADDR');
        $status = 'active';

        $line = implode($separator, [
            $id,
            $date,
            $ip,
            $name,
            $surname,
            $email,
            $phone,
            $topic,
            $payment,
            $newsletter,
            $status
        ]);

        file_put_contents($filePath, $line . PHP_EOL, FILE_APPEND);

        $success = true;

        $name = '';
        $surname = '';
        $email = '';
        $phone = '';
        $topic = '';
        $payment = '';
        $newsletter = 'Нет';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заявка на конференцию</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="page">
    <div class="form-card">
        <h1>Заявка на конференцию</h1>
        <p class="subtitle">Заполните форму для участия в мероприятии</p>

        <?php if ($success): ?>
            <div class="success">
                Ваша заявка успешно принята!
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <p>Пожалуйста, исправьте ошибки:</p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= safe($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="row">
                <div class="field">
                    <label>Имя участника</label>
                    <input type="text" name="name" value="<?= safe($name) ?>">
                </div>

                <div class="field">
                    <label>Фамилия</label>
                    <input type="text" name="surname" value="<?= safe($surname) ?>">
                </div>
            </div>

            <div class="field">
                <label>Электронный адрес</label>
                <input type="email" name="email" value="<?= safe($email) ?>">
            </div>

            <div class="field">
                <label>Телефон для связи</label>
                <input type="text" name="phone" value="<?= safe($phone) ?>">
            </div>

            <div class="field">
                <label>Интересующая тематика конференции</label>
                <select name="topic">
                    <option value="">Выберите тематику</option>
                    <option value="Бизнес" <?= $topic === 'Бизнес' ? 'selected' : '' ?>>Бизнес</option>
                    <option value="Технологии" <?= $topic === 'Технологии' ? 'selected' : '' ?>>Технологии</option>
                    <option value="Реклама и Маркетинг" <?= $topic === 'Реклама и Маркетинг' ? 'selected' : '' ?>>Реклама и Маркетинг</option>
                </select>
            </div>

            <div class="field">
                <label>Предпочитаемый метод оплаты</label>

                <div class="radio-group">
                    <label>
                        <input type="radio" name="payment" value="WebMoney" <?= $payment === 'WebMoney' ? 'checked' : '' ?>>
                        WebMoney
                    </label>

                    <label>
                        <input type="radio" name="payment" value="Яндекс.Деньги" <?= $payment === 'Яндекс.Деньги' ? 'checked' : '' ?>>
                        Яндекс.Деньги
                    </label>

                    <label>
                        <input type="radio" name="payment" value="PayPal" <?= $payment === 'PayPal' ? 'checked' : '' ?>>
                        PayPal
                    </label>

                    <label>
                        <input type="radio" name="payment" value="Кредитная карта" <?= $payment === 'Кредитная карта' ? 'checked' : '' ?>>
                        Кредитная карта
                    </label>
                </div>
            </div>

            <label class="checkbox">
                <input type="checkbox" name="newsletter" <?= $newsletter === 'Да' ? 'checked' : '' ?>>
                Я хочу получать рассылку о конференции
            </label>

            <button type="submit">Отправить заявку</button>
        </form>

        <a class="admin-link" href="admin.php">Перейти в админ-панель</a>
    </div>
</div>

</body>
</html>
