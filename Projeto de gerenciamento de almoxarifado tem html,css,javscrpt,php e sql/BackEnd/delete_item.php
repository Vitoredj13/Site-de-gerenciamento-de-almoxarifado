<?php
include_once 'config.php';

if (isset($_GET['id_item']) && isset($_GET['confirm'])) {
    $idItem = $_GET['id_item'];

    // Exclui todas as movimentações relacionadas ao item
    $deleteMovimentacaoStmt = $conn->prepare("DELETE FROM movimentacao WHERE id_item = ?");
    $deleteMovimentacaoStmt->bind_param("i", $idItem);
    $deleteMovimentacaoStmt->execute();
    $deleteMovimentacaoStmt->close();

    // Agora exclui o item da tabela item
    $deleteItemStmt = $conn->prepare("DELETE FROM item WHERE id_item = ?");
    $deleteItemStmt->bind_param("i", $idItem);
    $deleteItemStmt->execute();
    $deleteItemStmt->close();

    // Redireciona de volta para a página de solicitação com uma mensagem de sucesso
    header("Location: solicitacao.php?status=success&message=Item e suas movimentações relacionadas foram removidos com sucesso!");
} else {
    // Se o ID do item ou a confirmação não estiverem definidos, redireciona com uma mensagem de erro
    header("Location: solicitacao.php?status=error&message=Parâmetros inválidos.");
}
?>

