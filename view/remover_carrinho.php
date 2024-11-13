<?php
session_start();
require_once '../config/db.php';
require_once '../models/carrinho.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário não logado.']);
    exit();
}

$email_usuario = $_SESSION['email'];  // Pega o e-mail diretamente da sessão

// Criar uma instância da classe Carrinho
$database = new Database();
$db = $database->getConnection();
$carrinho = new Carrinho($db);

// Verifica se os dados foram passados
if (isset($_POST['produtos']) && !empty($_POST['produtos'])) {
    $produtos = $_POST['produtos'];

    try {
        // Loop para remover os produtos
        foreach ($produtos as $idCarrinho) {
            $result = $carrinho->removerCarrinho($idCarrinho);  // Chama a função para remover do banco de dados
        }

        // Se a remoção foi bem-sucedida
        echo json_encode(['status' => 'success', 'message' => 'Produto(s) removido(s) com sucesso!']);
    } catch (Exception $e) {
        // Se houver algum erro ao tentar remover o produto
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Nenhum produto foi selecionado para remoção.']);
}
?>
