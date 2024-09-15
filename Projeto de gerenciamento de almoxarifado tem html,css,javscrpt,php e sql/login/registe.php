<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/logo_fundo_transparente.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/login.css">
    <title>Novo Registro</title>
</head>
<body>
    <div class="container">
        <h2>Crie sua conta</h2>
        <?php
        // Exibe a mensagem de erro se o usuário já existir
        if (isset($_GET['error']) && $_GET['error'] == 1) {
            echo '<div class="error-message">Este usuário já existe. Tente Logar de novo.</div>';
        }
        ?>
        <form action="../login/register.php" method="POST">
            <label for="username">Nome de Usuário:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>
            
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="phone">Telefone:</label>
            <input type="text" id="phone" name="phone" required>
            
            <div class="buttons">
                <input type="submit" value="Registrar">
            </div>
        </form>
    </div>
</body>
</html>

