<?php
// Conexão com o banco de dados
include_once 'config.php';

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $stmt = $conn->prepare("SELECT nomedoitem FROM item WHERE nomedoitem LIKE ? LIMIT 10");
    $searchQuery = "%{$query}%";
    $stmt->bind_param("s", $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<ul>';
        while ($row = $result->fetch_assoc()) {
            echo '<li>' . $row['nomedoitem'] . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<ul><li>Item não encontrado</li></ul>';
    }
}
?>

