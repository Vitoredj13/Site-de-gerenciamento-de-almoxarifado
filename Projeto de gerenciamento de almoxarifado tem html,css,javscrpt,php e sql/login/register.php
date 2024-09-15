<?php
include_once '../BackEnd/config.php'; // Inclua o arquivo de configuração do banco de dados


$user = $_POST['username'];
$email = $_POST['email'];
$pass = $_POST['password'];
$phone = $_POST['phone'];

// Verifica se o nome de usuário já existe
$sql_check = "SELECT * FROM usuario WHERE nomedousuario = '$user'";
$result_check = $conn->query($sql_check);

if ($result_check->num_rows > 0) {
    // Se o usuário já existe, redireciona de volta para a página de registro com uma mensagem de erro
    header("Location: ../login/registe.php?error=1");
    exit();
} else {
    // Se o usuário não existir, insere os dados no banco
    $sql_insert = "INSERT INTO usuario (nomedousuario, email, senha, telefone) VALUES ('$user', '$email', '$pass', '$phone')";
    
    if ($conn->query($sql_insert) === TRUE) {
        // Redireciona para a página de login após o registro bem-sucedido
        header("Location: ../index.php");
        exit();
    } else {
        echo "Erro: " . $sql_insert . "<br>" . $conn->error;
    }
}

$conn->close();
?>