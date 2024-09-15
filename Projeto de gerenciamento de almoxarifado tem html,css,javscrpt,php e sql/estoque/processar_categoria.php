<?php
session_start();
include_once 'config.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login/login.php');
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Puxando os dados do usuário
$user_query = "SELECT nomedousuario, email, telefone FROM usuario WHERE id_usuario = $id_usuario";
$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();

if (!$user) {
    echo "Usuário não encontrado.";
    exit();
}

// Pegando as iniciais do nome do usuário
$nome = $user['nomedousuario'];
$iniciais = strtoupper($nome[0]);

// Verifica se o telefone existe, caso contrário, define um valor padrão
$telefone = isset($user['telefone']) ? $user['telefone'] : 'Telefone não cadastrado';

// Variável para armazenar as opções do select
$opcoesCategoria = "";

// Consulta para buscar todas as categorias
$sql = "SELECT id_categoria, nomedacategoria FROM categoria";
$result = $conn->query($sql);

// Preencher as opções do select
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $opcoesCategoria .= "<option value='" . $row['id_categoria'] . "'>" . $row['nomedacategoria'] . "</option>";
    }
} else {
    $opcoesCategoria = "<option value=''>Nenhuma categoria encontrada</option>";
}

// Processar o envio do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['movimentacao'])) {
        $acao = $_POST['movimentacao'];

        if ($acao == "1") { // Criar categoria
            $nomeCategoria = $_POST['nomeCategoria'];
            $sqlInserir = "INSERT INTO categoria (nomedacategoria) VALUES ('$nomeCategoria')";
            if ($conn->query($sqlInserir) === TRUE) {
                echo "Categoria criada com sucesso!";
            } else {
                echo "Erro ao criar categoria: " . $conn->error;
            }
        } elseif ($acao == "0") { // Excluir categoria
            $idCategoriaExcluir = $_POST['nomeCategoriaExcluir'];
            $sqlExcluir = "DELETE FROM categoria WHERE id_categoria = '$idCategoriaExcluir'";
            if ($conn->query($sqlExcluir) === TRUE) {
                echo "Categoria excluída com sucesso!";
            } else {
                echo "Erro ao excluir categoria: " . $conn->error;
            }
        } elseif ($acao == "2") { // Excluir item
            $idItemExcluir = $_POST['idItemExcluir'];
            $sqlExcluirItem = "DELETE FROM item WHERE id_item = '$idItemExcluir'";
            if ($conn->query($sqlExcluirItem) === TRUE) {
                echo "Item excluído com sucesso!";
            } else {
                echo "Erro ao excluir item: " . $conn->error;
            }
        }
    }
}
?>
