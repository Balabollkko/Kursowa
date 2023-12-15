<?php
// Початок сесії
session_start();

// Перевірка, чи відправлено дані форми методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Перевірка, чи введені email і пароль
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        // Отримання даних з форми
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Підключення до бази даних (замініть на свої дані)
        $mysqli = new mysqli('localhost', 'root', 'root', 'local');

        // Перевірка з'єднання з базою даних
        if ($mysqli->connect_error) {
            die('Помилка з\'єднання з базою даних: ' . $mysqli->connect_error);
        }

        // Підготовлений SQL-запит
        $query = "SELECT * FROM users WHERE email = ? AND password = ?";
        $stmt = $mysqli->prepare($query);

        // Перевірка, чи успішно підготовлено запит
        if ($stmt) {
            // Прив'язка параметрів
            $stmt->bind_param("ss", $email, $password);

            // Виконання запиту
            $stmt->execute();

            // Отримання результатів
            $result = $stmt->get_result();

            // Перевірка, чи є результати
            if ($result->num_rows > 0) {
                // Користувача знайдено, встановлення сесії
                $_SESSION['user'] = $result->fetch_assoc();

                // Перенаправлення на головну сторінку (замініть на свою адресу)
                header("Location: /textchecker/");
                exit();
            } else {
                echo "Невірний email або пароль";
            }

            // Закриття запиту
            $stmt->close();
        } else {
            echo "Помилка підготовки SQL-запиту: " . $mysqli->error;
        }

        // Закриття з'єднання з базою даних
        $mysqli->close();
    } else {
        echo "Не всі обов'язкові поля заповнені";
    }
}
?>
