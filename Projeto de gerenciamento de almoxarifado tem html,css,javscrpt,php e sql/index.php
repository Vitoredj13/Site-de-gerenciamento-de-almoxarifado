<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/logo_fundo_transparente.png" type="image/x-icon">
    <title>StockMate Almoxarifado</title>
    <link rel="stylesheet" href="../css/login.css">
        
</head>
<body>

<div class="container">
    <img src="../img/logo_fundo_transparente.png" alt="logo" width="45%">
    <h2>Bem-vindo ao site do Almoxarifado</h2>
    <?php
    if (isset($_GET['error']) && $_GET['error'] == 1) {
        echo '<div class="error-message">Usuário ou senha estão errados. Tente novamente.</div>';
    }
    ?>
    <form action="../login/login.php" method="POST">
        <label for="username">Nome:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required>

        <div class="buttons">
            <button type="button" onclick="window.location.href='../login/registe.php'">Novo Registro</button>
            <input type="submit" value="Entrar">
        </div>
    </form>
</div>

</body>
</html>
