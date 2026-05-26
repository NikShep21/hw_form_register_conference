<?php

class ConferenceApplication
{
    public static $subjects = [
        1 => 'Бизнес и коммуникации',
        2 => 'Технологии',
        3 => 'Реклама',
        4 => 'Маркетинг',
        5 => 'Проектирование'
    ];

    public static $payments = [
        1 => 'WebMoney',
        2 => 'Яндекс.Деньги',
        3 => 'PayPal',
        4 => 'Кредитная карта',
        5 => 'Робокасса'
    ];

    public $id;
    public $name;
    public $lastname;
    public $email;
    public $tel;
    public $subject_id;
    public $payment_id;
    public $mailing;
    public $created_at;
    public $updated_at;
    public $deleted_at;

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->name = trim($data['name'] ?? '');
        $this->lastname = trim($data['lastname'] ?? '');
        $this->email = trim($data['email'] ?? '');
        $this->tel = trim($data['tel'] ?? '');
        $this->subject_id = $data['subject_id'] ?? '';
        $this->payment_id = $data['payment_id'] ?? '';
        $this->mailing = isset($data['mailing']) ? (int)$data['mailing'] : 0;
        $this->created_at = $data['created_at'] ?? '';
        $this->updated_at = $data['updated_at'] ?? '';
        $this->deleted_at = $data['deleted_at'] ?? null;
    }

    public function validate()
    {
        $errors = [];

        if ($this->name === '') {
            $errors[] = 'Введите имя';
        }

        if ($this->lastname === '') {
            $errors[] = 'Введите фамилию';
        }

        if ($this->email === '') {
            $errors[] = 'Введите электронную почту';
        }

        if ($this->tel === '') {
            $errors[] = 'Введите телефон';
        }

        if ($this->subject_id === '' || !isset(self::$subjects[(int)$this->subject_id])) {
            $errors[] = 'Выберите тематику конференции';
        }

        if ($this->payment_id === '' || !isset(self::$payments[(int)$this->payment_id])) {
            $errors[] = 'Выберите способ оплаты';
        }

        return $errors;
    }

    public function save($pdo)
    {
        $sql = "
            INSERT INTO participants
            (name, lastname, email, tel, subject_id, payment_id, mailing, created_at, updated_at)
            VALUES
            (:name, :lastname, :email, :tel, :subject_id, :payment_id, :mailing, NOW(), NOW())
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':name' => $this->name,
            ':lastname' => $this->lastname,
            ':email' => $this->email,
            ':tel' => $this->tel,
            ':subject_id' => (int)$this->subject_id,
            ':payment_id' => (int)$this->payment_id,
            ':mailing' => (int)$this->mailing
        ]);
    }

    public static function readActive($pdo)
    {
        $sql = "
            SELECT *
            FROM participants
            WHERE deleted_at IS NULL
            ORDER BY created_at DESC
        ";

        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $applications = [];

        foreach ($rows as $row) {
            $applications[] = new self($row);
        }

        return $applications;
    }

    public static function markDeleted($pdo, $ids)
    {
        if (empty($ids)) {
            return;
        }

        $ids = array_map('intval', $ids);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sql = "
            UPDATE participants
            SET deleted_at = NOW(), updated_at = NOW()
            WHERE id IN ($placeholders)
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($ids);
    }

    public static function safe($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function getSubjectName()
    {
        return self::$subjects[(int)$this->subject_id] ?? 'Неизвестно';
    }

    public function getPaymentName()
    {
        return self::$payments[(int)$this->payment_id] ?? 'Неизвестно';
    }

    public function getMailingText()
    {
        return $this->mailing ? 'Да' : 'Нет';
    }
}
