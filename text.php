<?php
session_start(); // Початок сесії

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

$cx = '10a21105446d54821';
$apiKey = 'AIzaSyAp63asQitcGh_jyhfzqVIYTsYAac6CBKs';

$inputText = isset($_POST['inputText']) ? $_POST['inputText'] : (isset($_SESSION['inputText']) ? $_SESSION['inputText'] : "");
$result = [];

if (!empty($inputText)) {
    $searchQuery = urlencode($inputText);
    $searchUrl = "https://www.googleapis.com/customsearch/v1?q={$searchQuery}&key={$apiKey}&cx={$cx}";

    $searchResults = file_get_contents($searchUrl);

    if ($searchResults === false) {
        die('Error fetching search results');
    }

    $searchResults = json_decode($searchResults, true);

    if (!empty($searchResults['items'][0]['snippet'])) {
        $firstResult = $searchResults['items'][0]['snippet'];

        // Обробка результатів для виведення
        $similarity = calculateCosineSimilarity($inputText, $firstResult);

        $result['similarity'] = $similarity;
        $result['inputText'] = nl2br($inputText);
        $result['highlightedText'] = $firstResult;
        $result['link'] = isset($searchResults['items'][0]['link']) ? $searchResults['items'][0]['link'] : "Посилання недоступне";
    }
}

// Збереження введеного тексту в сесії
$_SESSION['inputText'] = $inputText;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text Comparison</title>
    <style>
        body {
            text-align: center;
        }

        form {
            margin: 20px auto;
            width: 80%;
            max-width: 600px;
        }

        textarea {
            width: 100%;
        }
    </style>
</head>
<body>

<form method="post">
    <label for="inputText">Введіть текст:</label><br>
    <textarea id="inputText" name="inputText" rows="4" cols="50"><?php echo htmlspecialchars($inputText); ?></textarea><br>
    <input type="submit" value="Порівняти">
</form>

<div id="result-container">
    <?php
    if (!empty($result)) {
        echo "<strong>Ступінь схожості:</strong> " . $result['similarity'] . "<br><br>";
        echo "<strong>Введений текст:</strong><br>";
        echo $result['inputText'] . "<br><br>";

        echo "<strong>Результати з Інтернету:</strong><br>";

        // Виведення тексту без виділення
        echo $result['highlightedText'] . "<br><br>";

        echo "<strong>Посилання на сайт:</strong><br>";
        echo $result['link'];
    }
    ?>
</div>

</body>
</html>
