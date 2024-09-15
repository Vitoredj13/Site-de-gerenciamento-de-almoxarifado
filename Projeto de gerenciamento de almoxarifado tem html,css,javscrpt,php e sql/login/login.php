<?php
include_once '../BackEnd/config.php'; // Inclua o arquivo de configuração do banco de dados
session_start();

$user = $_POST['username'];
$pass = $_POST['password'];

// Usando prepared statements para evitar SQL injection
$stmt = $conn->prepare("SELECT id_usuario, nomedousuario FROM usuario WHERE nomedousuario = ? AND senha = ?");
$stmt->bind_param('ss', $user, $pass);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Se as informações estiverem corretas, salva o id do usuário na sessão e redireciona para a página do dashboard
    $row = $result->fetch_assoc();
    $_SESSION['id_usuario'] = $row['id_usuario'];
    $_SESSION['nomedousuario'] = $row['nomedousuario'];
    header("Location: ../dashboard.php");
    exit();
} else {
    header("Location: ../index.php?error=1");
    exit();
}

$stmt->close();
$conn->close();
?>
