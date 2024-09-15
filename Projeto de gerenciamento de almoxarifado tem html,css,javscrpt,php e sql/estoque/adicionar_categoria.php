<?php
include_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomeCategoria = $_POST['nome'];

    if (!empty($nomeCategoria)) {
        $stmt = $conn->prepare("INSERT INTO categoria (nomedacategoria) VALUES (?)");
        $stmt->bind_param("s", $nomeCategoria);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Categoria adicionada com sucesso!']);
        } else {
            echo json_encode(['message' => 'Erro ao adicionar a categoria.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['message' => 'O nome da categoria não pode ser vazio.']);
    }
}
?>
