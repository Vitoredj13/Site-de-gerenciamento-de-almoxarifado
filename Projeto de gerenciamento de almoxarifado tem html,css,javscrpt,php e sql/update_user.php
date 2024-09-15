<?php
session_start();
include_once 'config.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login/login.php');
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$senha = $_POST['senha'];

if (!empty($senha)) {
    // Criptografe a senha aqui antes de salvar
    $senha_criptografada = password_hash($senha, PASSWORD_BCRYPT);
    $query = "UPDATE usuario SET nomedousuario='$nome', email='$email', telefone='$telefone', senha='$senha_criptografada' WHERE id_usuario=$id_usuario";
} else {
    $query = "UPDATE usuario SET nomedousuario='$nome', email='$email', telefone='$telefone' WHERE id_usuario=$id_usuario";
}

if ($conn->query($query) === TRUE) {
    // Redirecione para o dashboard com uma mensagem de sucesso
    header('Location: ../dashboard.php');
} else {
    echo "Erro ao atualizar: " . $conn->error;
}

$conn->close();
?>
