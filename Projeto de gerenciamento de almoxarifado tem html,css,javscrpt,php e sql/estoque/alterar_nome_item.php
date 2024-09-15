<?php
// Configuração da conexão com o banco de dados
$host = 'localhost'; // ou o host onde seu banco de dados está rodando
$db   = 'tcc2'; // nome do banco de dados
$user = 'root'; // nome de usuário do banco de dados
$pass = ''; // senha do banco de dados

try {
    // Cria uma nova conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    // Configura o PDO para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Em caso de erro na conexão, exibe a mensagem de erro
    echo 'Erro na conexão com o banco de dados: ' . $e->getMessage();
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe os dados do AJAX
    $nomeOriginal = $_POST['nomeOriginal'];
    $nomeNovo = $_POST['nomeNovo'];

    try {
        // Verifica se o novo nome é diferente do original apenas na capitalização
        if (strcasecmp($nomeOriginal, $nomeNovo) === 0) {
            // Apenas atualiza o nome com a nova capitalização
            $query = $pdo->prepare("UPDATE item SET nomedoitem = :nomeNovo WHERE nomedoitem = :nomeOriginal");
            $query->bindParam(':nomeNovo', $nomeNovo);
            $query->bindParam(':nomeOriginal', $nomeOriginal);
            $query->execute();

            $response = [
                'status' => 'success',
                'message' => 'Nome do item alterado com sucesso'
            ];
        } else {
            // Verifica se o novo nome já existe
            $query = $pdo->prepare("SELECT id_item FROM item WHERE LOWER(nomedoitem) = LOWER(:nomeNovo)");
            $query->bindParam(':nomeNovo', $nomeNovo);
            $query->execute();

            if ($query->rowCount() > 0) {
                $response = [
                    'status' => 'error',
                    'message' => 'Já existe um item com o nome ' . $nomeNovo
                ];
            } else {
                // Atualiza o nome do item
                $query = $pdo->prepare("UPDATE item SET nomedoitem = :nomeNovo WHERE nomedoitem = :nomeOriginal");
                $query->bindParam(':nomeNovo', $nomeNovo);
                $query->bindParam(':nomeOriginal', $nomeOriginal);
                $query->execute();

                $response = [
                    'status' => 'success',
                    'message' => 'Nome do item alterado com sucesso'
                ];
            }
        }

        // Retorna a resposta em JSON
        echo json_encode($response);
    } catch (PDOException $e) {
        // Em caso de erro, retorna uma mensagem de erro
        $response = [
            'status' => 'error',
            'message' => 'Erro ao alterar o nome do item: ' . $e->getMessage()
        ];
        echo json_encode($response);
    }
} else {
    // Método não permitido
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
}

