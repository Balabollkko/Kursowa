<?php
session_start();

$servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "local";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputText = isset($_POST['inputText']) ? $_POST['inputText'] : "";
    
    if (!empty($inputText)) {
        // Додавання тексту в базу даних
        $sql = "INSERT INTO texts (content) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $inputText);
        
        if ($stmt->execute()) {
            echo "Текст успішно додано в базу даних.<br>";
        } else {
            echo "Помилка при додаванні тексту в базу даних: " . $stmt->error . "<br>";
        }
        
        $stmt->close();
    } else {
        echo "Будь ласка, введіть текст для порівняння.<br>";
    }

    // Порівняння введеного тексту зі збереженими в базі даних
    $sql = "SELECT id, content FROM texts";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $savedText = $row["content"];
            $similarity = calculateCosineSimilarity($inputText, $savedText);
            
            echo "Ступінь схожості між введеним текстом і текстом ID {$row["id"]}: {$similarity}<br>";
        }
    } else {
        echo "У базі даних немає збережених текстів для порівняння.<br>";
    }
}

$conn->close();

function calculateCosineSimilarity($text1, $text2) {
    $vector1 = array_count_values(str_word_count(strtolower($text1), 1));
    $vector2 = array_count_values(str_word_count(strtolower($text2), 1));

    $dotProduct = 0;
    $magnitude1 = 0;
    $magnitude2 = 0;

    foreach ($vector1 as $word => $count) {
        $dotProduct += $count * ($vector2[$word] ?? 0);
        $magnitude1 += $count ** 2;
    }

    foreach ($vector2 as $word => $count) {
        $magnitude2 += $count ** 2;
    }

    $magnitude1 = sqrt($magnitude1);
    $magnitude2 = sqrt($magnitude2);

    if ($magnitude1 == 0 || $magnitude2 == 0) {
        return 0; // Уникнення ділення на нуль
    }

    return $dotProduct / ($magnitude1 * $magnitude2);
}
?>
