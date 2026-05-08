<?php
$errors = [];

$name = $_POST['name'] ?? '';
$surname = $_POST['surname'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$topic = $_POST['topic'] ?? '';
$payment = $_POST['payment'] ?? '';
$newsletter = isset($_POST['newsletter']) ? 'Да' : 'Нет';

$success = false;

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

    if (empty($errors)) {
        $application = [
            'name' => $name,
            'surname' => $surname,
            'email' => $email,
            'phone' => $phone,
            'topic' => $topic,
            'payment' => $payment,
            'newsletter' => $newsletter,
            'date' => date('d.m.Y H:i:s')
        ];

        if (!is_dir('applications')) {
            mkdir('applications');
        }

        $fileName = 'applications/application_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.json';

        file_put_contents(
            $fileName,
            json_encode($application, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );

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

function safe($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
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
                    <label>Имя</label>
                    <input type="text" name="name" value="<?= safe($name) ?>">
                </div>

                <div class="field">
                    <label>Фамилия</label>
                    <input type="text" name="surname" value="<?= safe($surname) ?>">
                </div>
            </div>

            <div class="field">
                <label>Электронная почта</label>
                <input type="email" name="email" value="<?= safe($email) ?>">
            </div>

            <div class="field">
                <label>Телефон</label>
                <input type="text" name="phone" value="<?= safe($phone) ?>">
            </div>

            <div class="field">
                <label>Тематика конференции</label>
                <select name="topic">
                    <option value="">Выберите тематику</option>
                    <option value="Бизнес" <?= $topic === 'Бизнес' ? 'selected' : '' ?>>Бизнес</option>
                    <option value="Технологии" <?= $topic === 'Технологии' ? 'selected' : '' ?>>Технологии</option>
                    <option value="Реклама и Маркетинг" <?= $topic === 'Реклама и Маркетинг' ? 'selected' : '' ?>>Реклама и Маркетинг</option>
                </select>
            </div>

            <div class="field">
                <label>Способ оплаты</label>

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
