<?php
require $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
require $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/TextChecker/vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;

function getTextFromWord($filename) {
    try {
        $phpWord = IOFactory::load($filename);
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    foreach ($element->getElements() as $textPiece) {
                        if ($textPiece instanceof \PhpOffice\PhpWord\Element\Text) {
                            $text .= $textPiece->getText();
                        }
                    }
                } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextBreak) {
                    $text .= ' '; // Add a space for TextBreak to separate words
                } elseif ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                    $text .= $element->getText();
                }
            }
        }

        return $text;
    } catch (Exception $e) {
        return 'Помилка читання файлу: ' . $e->getMessage();
    }
}

function calculateSimilarity($text1, $text2) {
    similar_text($text1, $text2, $similarityPercentage);

    return $similarityPercentage;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file1 = $_FILES['file1']['tmp_name'];
    $file2 = $_FILES['file2']['tmp_name'];

    if (!file_exists($file1) || !file_exists($file2)) {
        echo "<strong>Error:</strong> One or both files do not exist.<br><br>";
    } else {
        echo "<strong>Files exist and are being uploaded correctly.</strong><br><br>";

        $text1 = getTextFromWord($file1);
        $text2 = getTextFromWord($file2);

        if (is_string($text1)) {
            echo "<strong>Error in File 1:</strong> " . $text1 . "<br><br>";
        } else {
            echo "<strong>Text from File 1:</strong><br>";
            echo nl2br(htmlspecialchars($text1)) . "<br><br>";
        }

        if (is_string($text2)) {
            echo "<strong>Error in File 2:</strong> " . $text2 . "<br><br>";
        } else {
            echo "<strong>Text from File 2:</strong><br>";
            echo nl2br(htmlspecialchars($text2)) . "<br><br>";
        }

        $similarityPercentage = calculateSimilarity($text1, $text2);

        echo "<strong>Similarity Percentage:</strong> " . $similarityPercentage . "%<br><br>";
    }
}
?>
