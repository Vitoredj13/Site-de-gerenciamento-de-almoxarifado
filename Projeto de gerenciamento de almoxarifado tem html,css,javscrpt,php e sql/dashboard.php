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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/logo_fundo_transparente.png" type="image/x-icon">
    <title>Dashboard - Stockmate</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/styles.css"> <!-- Arquivo de estilos separados -->
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <img src="../img/logo_fundo_transparente.png" alt="Logo" id="profile-img">
        </div>
        <div class="menu">
            <a class="active" href="../dashboard.php">DashBoard</a>
            <a class="butao" href="../registro/registro.php">Registro</a>
            <a class="butao" href="../estoque/index.php">Estoque</a>
        </div>
        <a class="solicitacao-btn" href="../BackEnd/solicitacao.php">Solicitação</a>
    </div>

    <!-- Menu de Perfil -->
    <div class="menu-perfil" id="menu-perfil">
        <div class="inicial"><?php echo $iniciais; ?></div>
        <div class="username">Olá, <?php echo $nome; ?>!</div>
        <div class="email"><?php echo $user['email']; ?></div>
        <div class="action-btn">
            <a href="../login/registe.php">Adicionar conta</a>
            <a href="../index.php">Sair</a>
        </div>
        <div class="footer-links">
            <a href="../termosepoliticas/politica_de_privacidade.html">Política de Privacidade</a>
            <a href="../termosepoliticas/termos_de_servico_stockmate.html">Termos de Serviço</a>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="editForm" method="POST" action="update_user.php">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo $user['nomedousuario']; ?>" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                
                <label for="telefone">Telefone:</label>
                <input type="tel" id="telefone" name="telefone" value="<?php echo $telefone; ?>" required>
                
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Deixe em branco para manter a senha atual">
                
                <input type="submit" value="Salvar Alterações">
            </form>
        </div>
    </div>

    <div class="container">
    <?php
    // Fetch data for Devoluções (Entries)
    $devolucoes_query = "SELECT COUNT(*) AS total_devolucoes FROM movimentacao WHERE tipo_movimentacao = 1";
    $devolucoes_result = $conn->query($devolucoes_query);
    $devolucoes = $devolucoes_result->fetch_assoc()['total_devolucoes'];

    // Fetch data for Saídas (Exits)
    $saidas_query = "SELECT COUNT(*) AS total_saidas FROM movimentacao WHERE tipo_movimentacao = 0";
    $saidas_result = $conn->query($saidas_query);
    $saidas = $saidas_result->fetch_assoc()['total_saidas'];

    // Fetch data for Nº de Materiais
    $materiais_query = "SELECT SUM(quantidade) AS total_materiais FROM item";
    $materiais_result = $conn->query($materiais_query);
    $materiais = $materiais_result->fetch_assoc()['total_materiais'];

    // Fetch data for Solicitações (New Entries)
    $solicitacoes_query = "SELECT COUNT(*) AS total_solicitacoes FROM movimentacao";
    $solicitacoes_result = $conn->query($solicitacoes_query);
    $solicitacoes = $solicitacoes_result->fetch_assoc()['total_solicitacoes'];
    ?>
    <div class="info-cards-row">
        <a href="../registro/registro.php?filter=entradas" style="text-decoration: none;">
            <div class="info-card">
                <div class="progress-bar" style="width: <?php echo min($devolucoes, 100); ?>%;"></div>
                <span>Entradas</span>
                <span class="number"><?php echo $devolucoes; ?></span>
            </div>
        </a>
        <a href="../registro/registro.php?filter=saidas" style="text-decoration: none;">
            <div class="info-card">
                <div class="progress-bar" style="width: <?php echo min($saidas, 100); ?>%;"></div>
                <span>Saídas</span>
                <span class="number"><?php echo $saidas; ?></span>
            </div>
        </a>
    </div>
    <div class="info-cards-row-saida">
        <a href="../estoque/index.php" style="text-decoration: none;">
            <div class="info-card">
                <div class="progress-bar" style="width: <?php echo min($materiais, 100); ?>%;"></div>
                <span>Nº de Materiais</span>
                <span class="number"><?php echo $materiais; ?></span>
            </div>
        </a>
        <a href="../BackEnd/solicitacao.php" style="text-decoration: none;">
            <div class="info-card">
                <div class="progress-bar" style="width: <?php echo min($solicitacoes, 100); ?>%;"></div>
                <span>Solicitações</span>
                <span class="number"><?php echo $solicitacoes; ?></span>
            </div>
        </a>
    </div>
</div>


    <div class="atraso-table">
        <h2>Solicitações</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Responsável</th>
                    <th>Descrição</th>
                    <th>Data</th>
                    <th>Hora</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch the most recent movements, ordered by date and time descending
                $solicitacoes_detalhes_query = "SELECT id_movimentacao, responsavel, descricaomovimentacao, data, horadamovimentacao FROM movimentacao ORDER BY data DESC, horadamovimentacao DESC";
                $solicitacoes_detalhes_result = $conn->query($solicitacoes_detalhes_query);

                while ($row = $solicitacoes_detalhes_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id_movimentacao'] . "</td>";
                    echo "<td>" . $row['responsavel'] . "</td>";
                    echo "<td>" . $row['descricaomovimentacao'] . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['data'])) . "</td>";
                    echo "<td>" . $row['horadamovimentacao'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="../js/menu.js"></script> <!-- Arquivo de script separado -->
</body>
</html>
