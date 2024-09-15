<?php
include_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomeCategoria = $_POST['nome'];

    if (!empty($nomeCategoria)) {
        $stmt = $conn->prepare("DELETE FROM categoria WHERE nomedacategoria = ?");
        $stmt->bind_param("s", $nomeCategoria);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Categoria excluída com sucesso!']);
        } else {
            echo json_encode(['message' => 'Erro ao excluir a categoria.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['message' => 'O nome da categoria não pode ser vazio.']);
    }
}
?>


