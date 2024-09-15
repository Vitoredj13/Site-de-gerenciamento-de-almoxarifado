<?php
include_once 'config.php';

if (isset($_POST['query'])) {
    $inputText = $_POST['query'];
    $stmt = $conn->prepare("SELECT nomedoitem FROM item WHERE nomedoitem LIKE CONCAT(?, '%') LIMIT 5");
    $stmt->bind_param("s", $inputText);
    $stmt->execute();
    $stmt->bind_result($nomeDoItem);
    
    $output = '<ul style="list-style: none; padding: 0; margin: 0;">';
    while ($stmt->fetch()) {
        $output .= '<li style="padding: 8px; cursor: pointer;">' . htmlspecialchars($nomeDoItem) . '</li>';
    }
    $output .= '</ul>';

    echo $output;

    $stmt->close();
}
?>
