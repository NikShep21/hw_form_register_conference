<?php

session_start();

require_once 'db.php';
require_once 'ConferenceApplication.php';

$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? false;
$old = $_SESSION['old'] ?? [];

unset($_SESSION['errors'], $_SESSION['success'], $_SESSION['old']);

$name = $old['name'] ?? '';
$lastname = $old['lastname'] ?? '';
$email = $old['email'] ?? '';
$tel = $old['tel'] ?? '';
$subject_id = $old['subject_id'] ?? '';
$payment_id = $old['payment_id'] ?? '';
$mailing = $old['mailing'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application = new ConferenceApplication([
        'name' => $_POST['name'] ?? '',
        'lastname' => $_POST['lastname'] ?? '',
        'email' => $_POST['email'] ?? '',
        'tel' => $_POST['tel'] ?? '',
        'subject_id' => $_POST['subject_id'] ?? '',
        'payment_id' => $_POST['payment_id'] ?? '',
        'mailing' => isset($_POST['mailing']) ? 1 : 0
    ]);

    $errors = $application->validate();

    if (empty($errors)) {
        $application->save($pdo);
        $_SESSION['success'] = true;
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = [
            'name' => $_POST['name'] ?? '',
            'lastname' => $_POST['lastname'] ?? '',
            'email' => $_POST['email'] ?? '',
            'tel' => $_POST['tel'] ?? '',
            'subject_id' => $_POST['subject_id'] ?? '',
            'payment_id' => $_POST['payment_id'] ?? '',
            'mailing' => isset($_POST['mailing']) ? 1 : 0
        ];
    }

    header('Location: index.php');
    exit;
}

function safe($value) {
    return ConferenceApplication::safe($value);
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
                    <input type="text" name="lastname" value="<?= safe($lastname) ?>">
                </div>
            </div>

            <div class="field">
                <label>Электронный адрес</label>
                <input type="email" name="email" value="<?= safe($email) ?>">
            </div>

            <div class="field">
                <label>Телефон для связи</label>
                <input type="text" name="tel" value="<?= safe($tel) ?>">
            </div>

            <div class="field">
                <label>Интересующая тематика конференции</label>
                <select name="subject_id">
                    <option value="">Выберите тематику</option>

                    <?php foreach (ConferenceApplication::$subjects as $id => $subject): ?>
                        <option value="<?= $id ?>" <?= (int)$subject_id === (int)$id ? 'selected' : '' ?>>
                            <?= safe($subject) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label>Предпочитаемый метод оплаты</label>

                <div class="radio-group">
                    <?php foreach (ConferenceApplication::$payments as $id => $payment): ?>
                        <label>
                            <input
                                type="radio"
                                name="payment_id"
                                value="<?= $id ?>"
                                <?= (int)$payment_id === (int)$id ? 'checked' : '' ?>
                            >
                            <?= safe($payment) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <label class="checkbox">
                <input type="checkbox" name="mailing" <?= $mailing ? 'checked' : '' ?>>
                Я хочу получать рассылку о конференции
            </label>

            <button type="submit">Отправить заявку</button>
        </form>

        <a class="admin-link" href="admin.php">Перейти в админ-панель</a>
    </div>
</div>

</body>
</html>
