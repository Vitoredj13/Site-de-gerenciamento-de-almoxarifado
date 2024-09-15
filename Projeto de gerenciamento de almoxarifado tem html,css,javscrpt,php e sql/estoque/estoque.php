<?php
include_once 'config.php';

ob_start();  // Inicia o buffer de saída
header('Content-Type: application/json');  // Define o cabeçalho para JSON

$response = ['status' => '', 'message' => ''];

if (isset($_POST['nome'], $_POST['tipo'], $_POST['movimentacao'])) {
    $nome = $_POST['nome'];
    $categoria = $_POST['tipo'];
    $movimentacao = $_POST['movimentacao'];
    $quantidade = $_POST['quantidade'];

    if ($movimentacao == 1) { // Adicionar item
        // Verifica se a categoria já existe
        $categoriaStmt = $conn->prepare("SELECT id_categoria FROM categoria WHERE nomedacategoria = ?");
        $categoriaStmt->bind_param("s", $categoria);
        $categoriaStmt->execute();
        $categoriaStmt->store_result();
        
        if ($categoriaStmt->num_rows == 0) {
            // Insere nova categoria se não existir
            $inserirCategoriaStmt = $conn->prepare("INSERT INTO categoria (nomedacategoria) VALUES (?)");
            $inserirCategoriaStmt->bind_param("s", $categoria);
            $inserirCategoriaStmt->execute();
            $idCategoria = $conn->insert_id;
            $inserirCategoriaStmt->close();
        } else {
            // Recupera o ID da categoria existente
            $categoriaStmt->bind_result($idCategoria);
            $categoriaStmt->fetch();
        }
        $categoriaStmt->close();
        
        // Verifica se o item já existe
        $itemStmt = $conn->prepare("SELECT id_item, quantidade FROM item WHERE nomedoitem = ?");
        $itemStmt->bind_param("s", $nome);
        $itemStmt->execute();
        $itemStmt->store_result();

        if ($itemStmt->num_rows > 0) {
            // Se o item já existir, atualiza a quantidade
            $itemStmt->bind_result($idItem, $quantidadeAtual);
            $itemStmt->fetch();
            $novaQuantidade = $quantidadeAtual + $quantidade;

            $updateItemStmt = $conn->prepare("UPDATE item SET quantidade = ? WHERE id_item = ?");
            $updateItemStmt->bind_param("ii", $novaQuantidade, $idItem);
            $updateItemStmt->execute();

            $response['status'] = 'success';
            $response['message'] = 'Quantidade do item atualizada com sucesso!';
            $updateItemStmt->close();
        } else {
            // Se o item não existir, insere um novo
            $insertItemStmt = $conn->prepare("INSERT INTO item (nomedoitem, quantidade, id_categoria) VALUES (?, ?, ?)");
            $insertItemStmt->bind_param("sii", $nome, $quantidade, $idCategoria);
            $insertItemStmt->execute();
            $response['status'] = 'success';
            $response['message'] = 'Item inserido com sucesso!';
            $insertItemStmt->close();
        }
        $itemStmt->close();
        
    } else { // Remover item
        $itemStmt = $conn->prepare("DELETE FROM item WHERE nomedoitem = ?");
        $itemStmt->bind_param("s", $nome);
        
        if ($itemStmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Item excluído com sucesso!';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Erro ao excluir item: ' . $conn->error;
        }
        $itemStmt->close();
    }
    
    ob_clean();  // Limpa o buffer de saída para garantir que apenas JSON seja enviado
    echo json_encode($response);
    exit;
} else {
    http_response_code(400);
    $response['status'] = 'error';
    $response['message'] = 'Dados do formulário não foram enviados corretamente.';
    ob_clean();
    echo json_encode($response);
    exit;
}

$conn->close();
ob_end_flush();  // Envia o conteúdo final (JSON)
?>
