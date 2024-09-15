<?php
session_start();
include_once '../BackEnd/config.php';// Inclua o arquivo de configuração do banco de dados

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

$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

// Ajuste a consulta SQL com base no filtro
$query = "
    SELECT 
        movimentacao.id_movimentacao AS codigo, 
        CASE 
            WHEN movimentacao.tipo_movimentacao = 1 THEN 'ENTRADA'
            WHEN movimentacao.tipo_movimentacao = 0 THEN 'SAÍDA'
        END AS movimentacao,
        movimentacao.responsavel, 
        movimentacao.data, 
        item.nomedoitem AS Item, 
        movimentacao.quantidade_movimentacao AS quantidade, 
        movimentacao.id_item
    FROM movimentacao
    INNER JOIN item ON movimentacao.id_item = item.id_item
";

// Adiciona o filtro na consulta SQL
if ($filter === 'entradas') {
    $query .= " WHERE movimentacao.tipo_movimentacao = 1";
} elseif ($filter === 'saidas') {
    $query .= " WHERE movimentacao.tipo_movimentacao = 0";
}

$query .= " ORDER BY movimentacao.data DESC, movimentacao.horadamovimentacao DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/logo_fundo_transparente.png" type="image/x-icon">
    <title>Registro - Stockmate</title>
    <link rel="stylesheet" href="../css/Registro.css">
    <link rel="stylesheet" href="../css/styles.css"> <!-- Arquivo de estilos separados -->
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <img src="../img/logo_fundo_transparente.png" alt="Logo" id="profile-img">
        </div>
        <div class="menu">
            <a class="butao" href="../dashboard.php">DashBoard</a>
            <a class="active" href="../registro/registro.php">Registro</a>
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
            <a href="#">Política de Privacidade</a>
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

    <div class="search-bar">
        <input type="text" id="filterInput" placeholder="Pesquisar...">
    </div>

    <div class="table-container">
        <table id="movimentacaoTable">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Movimentação</th>
                    <th>Responsável</th>
                    <th>Data</th>
                    <th>Item</th>
                    <th>Quantidade</th>
                    <th>ID Do Item</th>
                </tr>
            </thead>
            <tbody>
    <?php
    if ($result->num_rows > 0) {
        // Loop através dos resultados e exibição dos dados
        while($row = $result->fetch_assoc()) {
            // Verifica se o tipo de movimentação é entrada (1) ou saída (0)
            $tipo_movimentacao = $row['movimentacao'] == 'ENTRADA' ? 'entrada' : 'saida';
            echo "<tr class='$tipo_movimentacao'>";
            echo "<td>" . htmlspecialchars($row['codigo']) . "</td>";
            echo "<td>" . htmlspecialchars($row['movimentacao']) . "</td>";
            echo "<td>" . htmlspecialchars($row['responsavel']) . "</td>";
            echo "<td>" . htmlspecialchars($row['data']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Item']) . "</td>";
            echo "<td>" . htmlspecialchars($row['quantidade']) . "</td>";
            echo "<td>" . htmlspecialchars($row['id_item']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>Nenhuma movimentação encontrada</td></tr>";
    }

    // Fecha a conexão
    $conn->close();
    ?>
</tbody>

        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var filter = "<?php echo $filter; ?>";
            var filterInput = document.getElementById('filterInput');

            if (filter === 'entradas') {
                filterInput.value = 'ENTRADA';
            } else if (filter === 'saidas') {
                filterInput.value = 'SAÍDA';
            }

            filterTable(); // Aplica o filtro automaticamente ao carregar a página

            filterInput.addEventListener('keyup', filterTable);

            function filterTable() {
                var filter = filterInput.value.toUpperCase();
                var table = document.getElementById('movimentacaoTable');
                var tr = table.getElementsByTagName('tr');

                for (var i = 1; i < tr.length; i++) { // Começa em 1 para pular o cabeçalho
                    var td = tr[i].getElementsByTagName('td')[1]; // A segunda coluna é a de movimentação
                    if (td) {
                        var txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                        } else {
                            tr[i].style.display = "none";
                        }
                    }
                }
            }
        });
    </script>
    <script src="../js/menu.js"></script> <!-- Arquivo de script separado -->
</body>
</html>
