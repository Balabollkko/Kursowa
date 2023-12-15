<?php
// Перевірка, чи форма відправлена методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Підключення до бази даних
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "local";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Перевірка підключення до бази даних
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Отримання даних з форми
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
// Перевірка чи існує користувач з такою поштою
$check_existing_user = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($check_existing_user);

if ($result->num_rows > 0) {
    $message = "Користувач з таким іменем вже існує";
} else {
    // Підготовка запиту на додавання користувача до локальної

    // Хешування пароля перед збереженням у базі даних
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Запит для вставки даних у базу даних
    $sql = "INSERT INTO users (full_name, email, password) VALUES ('$full_name', '$email', '$hashed_password')";

    // Виконання запиту та перевірка на помилки
    if ($conn->query($sql) === TRUE) {
        echo "Registration successful";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
    // Закриття підключення до бази даних
    $conn->close();
}



header("Location: /textchecker/"); // Оновлено шлях до головної сторінки
exit;
?>
