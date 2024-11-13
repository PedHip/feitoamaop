<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário não está logado.']);
    exit();
}

// Inclua as classes necessárias
include_once '../config/db.php';
include_once '../models/carrinho.php';
include_once '../models/pedido.php';

$database = new Database();
$db = $database->getConnection();
$pedido = new Pedido($db);
$carrinho = new Carrinho($db);

// Verificar se os dados foram enviados corretamente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['produtos'])) {
    // Dados dos produtos enviados via AJAX
    $produtos = $_POST['produtos'];
    $email_usuario = $_POST['email_usuario'];
    $telefone_usuario = $_POST['telefone_usuario'];
    $nome_usuario = $_POST['nome_usuario'];

    // Construir o valor para o campo usuario_contato
    $usuario_contato = $email_usuario . ' ' . $telefone_usuario;

    // Enviar os dados do carrinho para o banco de dados
    if ($pedido->registrarPedidoDoCarrinho($nome_usuario, $usuario_contato, $produtos)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Pedido realizado com sucesso!'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao realizar o pedido. Tente novamente mais tarde.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Dados inválidos enviados.'
    ]);
}
?>
