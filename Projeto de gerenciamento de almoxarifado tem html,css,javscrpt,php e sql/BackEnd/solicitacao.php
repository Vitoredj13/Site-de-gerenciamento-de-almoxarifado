<?php
// Configurar o fuso horário para o Horário de Brasília
date_default_timezone_set('America/Sao_Paulo');

session_start();
// Conexão com o banco de dados
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
    <title>StockMate</title>
    <link rel="stylesheet" href="../css/socilitacao.css">
    <link rel="stylesheet" href="../css/styles.css"> <!-- Arquivo de menu img separados -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <img src="../img/logo_fundo_transparente.png" alt="Logo" id="profile-img">
        </div>
        <div class="menu">
            <a class="butao" href="../dashboard.php">DashBoard</a>
            <a class="butao" href="../registro/registro.php">Registro</a>
            <a class="butao" href="../estoque/index.php">Estoque</a>
        </div>
        <a class="solicitacao-btn" href="solicitacao.php">Solicitação</a>
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
            <a href="#">Termos de Serviço</a>
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

    <div class="content">
        <h2>Dados de identificação:</h2>
        <form action="solicitacao.php" method="POST">
            <div class="form-group">
                <label for="tipo">Categoria:</label>
                <select id="tipo" name="tipo" required>
                    <option value="" disabled selected>Selecione uma categoria</option>
                    <?php
                    // Busca todas as categorias do banco de dados
                    $categoriaStmt = $conn->prepare("SELECT id_categoria, nomedacategoria FROM categoria");
                    $categoriaStmt->execute();
                    $categoriaStmt->bind_result($idCategoria, $nomeCategoria);

                    // Preenche o dropdown com as categorias disponíveis
                    while ($categoriaStmt->fetch()) {
                        echo '<option value="' . htmlspecialchars($nomeCategoria) . '">' . htmlspecialchars($nomeCategoria) . '</option>';
                    }

                    $categoriaStmt->close();
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="nome2">Nome do Item:</label>
                <input type="text" id="nome2" name="nome2" required autocomplete="off">

                <div id="itemLista" style="position: absolute; background-color: white; z-index: 1000;"></div>
            </div>  

            <div class="form-group">
                <label for="quantidade">Quantidade:</label>
                <input type="number" id="quantidade" name="quantidade" required>
            </div>
            <div class="form-group">
                <label for="responsavel">Responsável:</label>
                <input type="text" id="responsavel" name="responsavel" required>
            </div>
            <div class="form-group">
                <label for="data">Data:</label>
                <input type="date" id="data" name="data" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" required></textarea>
            </div>
            <div class="form-group">
                <label>Tipo de Movimentação:</label>
                <div class="radio-group">
                    <input type="radio" id="entrada" name="movimentacao" value="1" required>
                    <label for="entrada" class="entrada">Adicionar</label>
                    
                    <input type="radio" id="saida" name="movimentacao" value="0" required>
                    <label for="saida" class="saida">Remover</label>
                </div>
            </div>

            <!-- Botão de Enviar -->
            <button id="enviar-btn" class="Enviar">Enviar</button>

        </form>
        <script>
            // Seleciona os inputs de Adicionar e Remover
            const btnEntrada = document.getElementById('entrada');
            const btnSaida = document.getElementById('saida');

            // Seleciona o botão de Enviar
            const enviarBtn = document.getElementById('enviar-btn');

            // Adiciona eventos de clique para os inputs
            btnEntrada.addEventListener('change', function() {
                if (btnEntrada.checked) {
                    enviarBtn.classList.add('adicionar');
                    enviarBtn.classList.remove('remover');
                }
            });

            btnSaida.addEventListener('change', function() {
                if (btnSaida.checked) {
                    enviarBtn.classList.add('remover');
                    enviarBtn.classList.remove('adicionar');
                }
            });

        </script>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nome = $_POST['nome2'];
            $categoria = $_POST['tipo'];
            $movimentacao = $_POST['movimentacao'];
            $quantidade = $_POST['quantidade'];
            $responsavel = $_POST['responsavel'];
            $data = $_POST['data'];
            $hora = date("H:i:s"); // Captura a hora atual com base no fuso horário definido
            $descricao = $_POST['descricao']; // Captura a descrição fornecida
        
            $response = ['status' => '', 'message' => ''];
        
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
        
            if ($movimentacao == 1) { // Adicionar item
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
                    $idItem = $conn->insert_id; // Captura o ID do novo item
                    $response['status'] = 'success';
                    $response['message'] = 'Item inserido com sucesso!';
                    $insertItemStmt->close();
                }
            } else { // Remover item
                if ($itemStmt->num_rows > 0) {
                    // Se o item existir, atualiza a quantidade
                    $itemStmt->bind_result($idItem, $quantidadeAtual);
                    $itemStmt->fetch();
        
                    if ($quantidadeAtual >= $quantidade) {
                        $novaQuantidade = $quantidadeAtual - $quantidade;

                        if ($novaQuantidade == 0) {
                            // Confirmar antes de excluir o último item
                            echo '<script>
                                    if (confirm("Você está prestes a remover o último item e todas as movimentações dele com este nome. Deseja continuar?")) {
                                        window.location.href = "delete_item.php?id_item=' . $idItem . '&confirm=true";
                                    }
                                  </script>';
                        } else {
                            // Atualiza a quantidade do item
                            $updateItemStmt = $conn->prepare("UPDATE item SET quantidade = ? WHERE id_item = ?");
                            $updateItemStmt->bind_param("ii", $novaQuantidade, $idItem);
                            $updateItemStmt->execute();
                            $updateItemStmt->close();
                            $response['status'] = 'success';
                            $response['message'] = 'Quantidade do item atualizada com sucesso!';
                        }
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = 'Quantidade insuficiente para a remoção!';
                    }
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Item não encontrado!';
                }
            }

            // Registra a movimentação
            if ($itemStmt->num_rows > 0 || isset($idItem)) {
                $movimentacaoStmt = $conn->prepare("INSERT INTO movimentacao (id_item, tipo_movimentacao, quantidade_movimentacao, responsavel, data, horadamovimentacao, descricaomovimentacao, id_categoria) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $movimentacaoStmt->bind_param("iiissssi", $idItem, $movimentacao, $quantidade, $responsavel, $data, $hora, $descricao, $idCategoria);
                $movimentacaoStmt->execute();
                $movimentacaoStmt->close();
            }
        
            $itemStmt->close();
        
            // Exibir resposta com o balão de confirmação
            if (!empty($response['status'])) {
                $alertMessage = htmlspecialchars($response['message']);
                echo "<script>document.addEventListener('DOMContentLoaded', function() { showConfirmation('$alertMessage'); });</script>";
            }
        }
        ?>
    </div>

    <script>
        function showConfirmation(message) {
            const confirmation = document.createElement('div');
            confirmation.style.position = 'fixed';
            confirmation.style.top = '50%';
            confirmation.style.left = '50%';
            confirmation.style.transform = 'translate(-50%, -50%)';
            confirmation.style.padding = '20px';
            confirmation.style.backgroundColor = '#333';
            confirmation.style.color = '#fff';
            confirmation.style.borderRadius = '5px';
            confirmation.style.zIndex = '1000';
            confirmation.style.textAlign = 'center';

            const messageNode = document.createElement('p');
            messageNode.textContent = message;
            confirmation.appendChild(messageNode);

            const button = document.createElement('button');
            button.textContent = 'OK';
            button.style.marginTop = '10px';
            button.style.padding = '10px 20px';
            button.style.backgroundColor = '#4CAF50';
            button.style.color = '#fff';
            button.style.border = 'none';
            button.style.borderRadius = '5px';
            button.style.cursor = 'pointer';
            confirmation.appendChild(button);

            document.body.appendChild(confirmation);

            button.addEventListener('click', function() {
                document.body.removeChild(confirmation);
            });
        }

        $(document).ready(function() {
        $('#nome2').on('input', function() {
        var query = $(this).val();
        if (query.length > 1) {
            $.ajax({
                url: "autocomplete.php",
                method: "POST",
                data: { query: query },
                success: function(data) {
                    $('#itemLista').fadeIn();
                    $('#itemLista').html(data);
                }
            });
        } else {
            $('#itemLista').fadeOut();
        }
    });

    // Adiciona evento de blur para esconder a lista quando o campo perde o foco
    $('#nome2').on('blur', function() {
        setTimeout(function() {
            $('#itemLista').fadeOut();
        }, 200);
    });

    // Preenche o campo com o item selecionado e esconde a lista
    $(document).on('click', '#itemLista li', function() {
        $('#nome2').val($(this).text());
        $('#itemLista').fadeOut();
    });
});
    </script>
    <script src="../js/menu.js"></script> <!-- Arquivo de script separado -->
</body>
</html>
