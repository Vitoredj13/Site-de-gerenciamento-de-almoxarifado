<?php
include_once '../BackEnd/config.php'; // Certifique-se de que o caminho para o arquivo config está correto

$search = isset($_GET['search']) ? $_GET['search'] : ''; // Captura o termo de busca, se existir

// Consulta SQL para buscar itens com a categoria relacionada
$query = "SELECT e.id_item, e.nomedoitem, e.quantidade, c.id_categoria, c.nomedacategoria 
          FROM item e
          JOIN categoria c ON e.id_categoria = c.id_categoria";
$params = []; // Inicializa o array de parâmetros
$types = ''; // Inicializa a string de tipos

if (!empty($search)) {
    $query .= " WHERE e.nomedoitem LIKE ? OR c.nomedacategoria LIKE ?";
    $params[] = '%' . $search . '%'; // Adiciona o parâmetro de busca para o nome do item
    $params[] = '%' . $search . '%'; // Adiciona o parâmetro de busca para o nome da categoria
    $types .= 'ss'; // Define os tipos dos parâmetros como strings
}

$query .= " ORDER BY CASE WHEN e.nomedoitem LIKE ? THEN 1 ELSE 2 END, e.nomedoitem ASC";
$params[] = $search . '%'; // Adiciona o segundo parâmetro para ordenação
$types .= 's'; // Define o tipo do segundo parâmetro como string

$itensStmt = $conn->prepare($query);

// Se houver parâmetros, fazemos o bind_param
if (!empty($params)) {
    $itensStmt->bind_param($types, ...$params);
}

$itensStmt->execute();
$itensResult = $itensStmt->get_result();

if ($itensResult->num_rows > 0) {
    while ($item = $itensResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($item['nomedoitem']) . "</td>";
        echo "<td>" . htmlspecialchars($item['quantidade']) . "</td>";
        echo "<td>" . htmlspecialchars($item['nomedacategoria']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>Nenhum item encontrado</td></tr>";
}

$itensStmt->close();
$conn->close();
?>
