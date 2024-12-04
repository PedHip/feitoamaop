<?php
session_start();
require_once '../config/db.php';
require_once '../models/carrinho.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Email não fornecido.']);
    exit();
}

$email_usuario = $_SESSION['email'];  // Pega o e-mail diretamente da sessão

// Criar uma instância da classe Carrinho
$database = new Database();
$db = $database->getConnection();
$carrinho = new Carrinho($db);

// Chamar o método listarCarrinho para obter os pedidos
$pedidos = $carrinho->listarCarrinho($email_usuario);

// Retornar os dados como JSON
if ($pedidos !== null && count($pedidos) > 0) {
    echo json_encode(['status' => 'success', 'pedidos' => $pedidos]);
} else {
    echo json_encode(['status' => 'empty', 'message' => 'Nenhum produto encontrado no carrinho.']);
}
?>
