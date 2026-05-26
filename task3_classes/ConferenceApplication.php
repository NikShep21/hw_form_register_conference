<?php

class ConferenceApplication
{
    private static string $separator = '||';
    private static string $filePath = __DIR__ . '/applications.txt';

    public string $id;
    public string $date;
    public string $ip;
    public string $name;
    public string $surname;
    public string $email;
    public string $phone;
    public string $topic;
    public string $payment;
    public string $newsletter;
    public string $status;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? uniqid();
        $this->date = $data['date'] ?? date('d.m.Y H:i:s');
        $this->ip = $data['ip'] ?? ($_SERVER['REMOTE_ADDR'] ?? getenv('REMOTE_ADDR') ?: '');
        $this->name = trim($data['name'] ?? '');
        $this->surname = trim($data['surname'] ?? '');
        $this->email = trim($data['email'] ?? '');
        $this->phone = trim($data['phone'] ?? '');
        $this->topic = trim($data['topic'] ?? '');
        $this->payment = trim($data['payment'] ?? '');
        $this->newsletter = $data['newsletter'] ?? 'Нет';
        $this->status = $data['status'] ?? 'active';
    }

    public function validate(): array
    {
        $errors = [];

        if ($this->name === '') {
            $errors[] = 'Введите имя';
        }

        if ($this->surname === '') {
            $errors[] = 'Введите фамилию';
        }

        if ($this->email === '') {
            $errors[] = 'Введите электронную почту';
        }

        if ($this->phone === '') {
            $errors[] = 'Введите телефон';
        }

        if ($this->topic === '') {
            $errors[] = 'Выберите тематику конференции';
        }

        if ($this->payment === '') {
            $errors[] = 'Выберите способ оплаты';
        }

        foreach ($this->toArray() as $value) {
            if (str_contains($value, self::$separator)) {
                $errors[] = 'Нельзя использовать разделитель ' . self::$separator . ' в полях формы';
                break;
            }
        }

        return $errors;
    }

    public function save(): void
    {
        $line = implode(self::$separator, [
            $this->id,
            $this->date,
            $this->ip,
            $this->name,
            $this->surname,
            $this->email,
            $this->phone,
            $this->topic,
            $this->payment,
            $this->newsletter,
            $this->status
        ]);

        file_put_contents(self::$filePath, $line . PHP_EOL, FILE_APPEND);
    }

    public static function readAll(): array
    {
        if (!file_exists(self::$filePath)) {
            return [];
        }

        $lines = file(self::$filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $applications = [];

        foreach ($lines as $line) {
            $data = explode(self::$separator, $line);

            if (count($data) !== 11) {
                continue;
            }

            $applications[] = new self([
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
            ]);
        }

        return $applications;
    }

    public static function readActive(): array
    {
        return array_filter(self::readAll(), function ($application) {
            return $application->status !== 'deleted';
        });
    }

    public static function markDeleted(array $ids): void
    {
        $applications = self::readAll();
        $lines = [];

        foreach ($applications as $application) {
            if (in_array($application->id, $ids)) {
                $application->status = 'deleted';
            }

            $lines[] = implode(self::$separator, [
                $application->id,
                $application->date,
                $application->ip,
                $application->name,
                $application->surname,
                $application->email,
                $application->phone,
                $application->topic,
                $application->payment,
                $application->newsletter,
                $application->status
            ]);
        }

        file_put_contents(self::$filePath, implode(PHP_EOL, $lines) . PHP_EOL);
    }

    public function toArray(): array
    {
        return [
            $this->name,
            $this->surname,
            $this->email,
            $this->phone,
            $this->topic,
            $this->payment,
            $this->newsletter
        ];
    }

    public static function safe(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
