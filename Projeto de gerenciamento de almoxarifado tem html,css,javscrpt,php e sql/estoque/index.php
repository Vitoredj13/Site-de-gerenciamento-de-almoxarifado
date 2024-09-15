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
    <title>Estoque - Stockmate</title>
    <link rel="stylesheet" href="../css/estoque.css">
    <link rel="stylesheet" href="../css/gerenciar.css">
    <link rel="stylesheet" href="../css/styles.css"> <!-- Arquivo de menu img separados -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


</head>
<body>
    <div class="navbar">
        <div class="logo">
            <img src="../img/logo_fundo_transparente.png" alt="Logo" id="profile-img">
        </div>
        <div class="menu">
            <a class="butao" href="../dashboard.php">DashBoard</a>
            <a class="butao" href="../registro/registro.php">Registro</a>
            <a class="active" href="../estoque/index.php">Estoque</a>
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
            <a href="#">Termos de Serviço</a>
        </div>
    </div>

    <!-- Modal de Edição -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <!-- <span class="clp">&times;</span> -->
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
    <input type="text" id="search-query" placeholder="Pesquisar...">
    
    <!-- Botão de pesquisa com ícone de lupa -->
    <button id="search-button" class="btn">
        <i class="fas fa-search search-icon"></i> <!-- Ícone de lupa -->
        <span class="search-text">Pesquisar</span> <!-- Texto "Pesquisar" -->
    </button>
    
    <button id="gerencia-button" class="btng">Gerencia</button>
</div>

    
<div class="tab-container">
    <div class="tab">
        <button class="close">&times;</button>
        <div class="content">
            <h2>Página para criar e excluir categorias:</h2>

            <div class="form-group">
                <label>O que você irá fazer:</label>
                <div class="radio-group">
                    <input type="radio" id="entrada" name="movimentacao" value="1">
                    <label for="entrada" class="entrada">Criar</label>
                    
                    <input type="radio" id="saida" name="movimentacao" value="0">
                    <label for="saida" class="saida">Excluir</label>
                </div>
            </div>

            <form id="estoque-form" method="post" target="_self">
                <div id="tirarNome" style="display: none;">
                    <div class="form-group">
                        <label for="nomeCategoria">Nome da Categoria:</label>
                        <input type="text" id="nomeCategoria" name="nomeCategoria">
                    </div>
                </div>
                
                <div id="gerenciar-categoria" style="display: none;">
                    <div class="form-group">
                        <label for="nomeCategoriaExcluir">Escolha a Categoria para Excluir:</label>
                        <select id="nomeCategoriaExcluir" name="nomeCategoriaExcluir" class="form-control">
                            <?php
                            // Query para pegar todas as categorias
                            $categoria_query = "SELECT id_categoria, nomedacategoria FROM categoria";
                            $categoria_result = $conn->query($categoria_query);

                            // Verifica se há categorias e as insere no select
                            if ($categoria_result->num_rows > 0) {
                                while($categoria = $categoria_result->fetch_assoc()) {
                                    echo '<option value="' . $categoria['nomedacategoria'] . '">' . $categoria['nomedacategoria'] . '</option>';
                                }
                            } else {
                                echo '<option value="">Nenhuma categoria encontrada</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <input type="submit" value="Enviar" class="Enviar">
                </div>
            </form>
        </div>
    </div>
</div>



    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th>QUANTIDADE</th>
                    <th>NOME DA CATEGORIA</th>
                </tr>
            </thead>
            <tbody id="tabela-estoque">
                <!-- Dados do banco de dados serão inseridos aqui -->
            </tbody>
        </table>
    </div>
    
    <script>
        $(document).ready(function(){
            // Mostra ou esconde os campos com base no tipo de movimentação
            $('input[name="movimentacao"]').change(function() {
                if ($(this).val() == '1') { // Adicionar
                    $('#tirarNome').show();
                    $('#gerenciar-categoria').hide();
                } else if ($(this).val() == '0') { // Excluir
                    $('#tirarNome').hide();
                    $('#gerenciar-categoria').show();
                }
            });

            // Função para carregar os dados do estoque
            function carregarEstoque(query = '') {
                $.ajax({
                    url: '../estoque/carregar_estoque.php',
                    method: 'GET',
                    data: { search: query },
                    success: function(data) {
                        $('#tabela-estoque').html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro ao carregar estoque:', error);
                        alert('Erro ao carregar estoque.');
                    }
                });
            }

            // Carrega os dados quando a página é carregada
            carregarEstoque();

            // Função para pesquisar os dados ao clicar no botão Pesquisar
            $('#search-button').click(function(){
                var query = $('#search-query').val();
                carregarEstoque(query);
            });

            // Permite pesquisa ao pressionar Enter na barra de pesquisa
            $('#search-query').keypress(function(e) {
                if (e.which == 13) { // 13 é o código da tecla Enter
                    $('#search-button').click();
                }
            });

            // Função para enviar o formulário de estoque
            $('#estoque-form').submit(function(event){
                event.preventDefault();

                var movimentacao = $('input[name="movimentacao"]:checked').val();
                var nomeCategoria = $('#nomeCategoria').val();
                var nomeCategoriaExcluir = $('#nomeCategoriaExcluir').val();
                
                if (movimentacao == '1') { // Adicionar
                    if (confirm("Tem certeza que deseja criar a categoria '" + nomeCategoria + "'?")) {
                        $.ajax({
                            url: 'adicionar_categoria.php',
                            method: 'POST',
                            data: { nome: nomeCategoria },
                            dataType: 'json',
                            success: function(response) {
                                alert(response.message);
                                carregarEstoque(); // Recarrega os dados após inserção
                            },
                            error: function(xhr, status, error) {
                                console.error('Erro ao criar a categoria:', xhr.responseText);
                                alert('Erro ao criar a categoria: ' + error);
                            }
                        });
                    }
                } else if (movimentacao == '0') { // Excluir
                    $.ajax({
                        url: 'verificar_categoria.php',
                        method: 'POST',
                        data: { nome: nomeCategoriaExcluir },
                        dataType: 'json',
                        success: function(response) {
                            if (response.existe) {
                                if (confirm("Tem certeza que deseja excluir a categoria '" + nomeCategoriaExcluir + "'?")) {
                                    $.ajax({
                                        url: '../estoque/excluir_categoria.php',
                                        method: 'POST',
                                        data: { nome: nomeCategoriaExcluir },
                                        dataType: 'json',
                                        success: function(response) {
                                            alert(response.message);
                                            carregarEstoque(); // Recarrega os dados após exclusão
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Erro ao excluir a categoria:', xhr.responseText);
                                            alert('Erro ao excluir a categoria: ' + error);
                                        }
                                    });
                                }
                            } else {
                                alert("A categoria '" + nomeCategoriaExcluir + "' não existe.");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro ao verificar a categoria:', xhr.responseText);
                            alert('Erro ao verificar a categoria: ' + error);
                        }
                    });
                }
            });

            // Função para modificar o nome do item ao clicar no nome
            // Função para modificar o nome do item ao clicar no nome
$(document).on('click', '#tabela-estoque td:first-child', function() {
    var originalName = $(this).text();
    var newName = prompt("Modificar nome do item:", originalName);

    if (newName !== null && newName !== '' && newName !== originalName) {
        if (confirm("Tem certeza que deseja mudar o nome do item de '" + originalName + "' para '" + newName + "'?")) {
            $.ajax({
                url: 'alterar_nome_item.php',
                method: 'POST',
                data: { nomeOriginal: originalName, nomeNovo: newName },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        carregarEstoque(); // Recarrega os dados após a alteração
                    } else {
                        console.log('Erro na resposta:', response);
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao alterar o nome do item:', xhr.responseText);
                    alert('Erro ao alterar o nome do item: ' + xhr.responseText);
                }
            });
        }
    }
});

        });
    </script>
    <script>
    $(document).ready(function() {
        $('#nomeCategoriaExcluir').select2({
            placeholder: "Selecione uma categoria",
            allowClear: true
        });
    });
</script>

    <script src="../estoque/js/script.js"></script>
    <script src="../js/menu.js"></script> <!-- Arquivo de script separado -->
</body>
</html>
