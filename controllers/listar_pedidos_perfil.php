<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['email']) || !isset($_SESSION['telefone'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário não logado.']);
    exit();
}

require_once '../config/db.php';
require_once '../models/pedido.php';

$db = new Database();
$conn = $db->getConnection();
$conn->exec("SET NAMES 'utf8'");
$pedido = new Pedido($conn);

// Obtém o email e o telefone do usuário logado
$email_usuario = $_SESSION['email'];
$telefone_usuario = $_SESSION['telefone'];  // Telefone armazenado na sessão

// Chama a função para listar os pedidos
$pedidos = $pedido->listarPedidosPorUsuario($email_usuario, $telefone_usuario);

// Verificar o resultado (depuração)
// Aqui vamos garantir que o retorno para o frontend esteja no formato JSON
header('Content-Type: application/json');  // Definindo o cabeçalho de resposta como JSON

if ($pedidos) {
    // Retorna os pedidos no formato JSON para o frontend
    echo json_encode([
        'status' => 'success',
        'pedidos' => $pedidos
    ]);
} else {
    // Caso não haja pedidos, retorna um status de 'empty'
    echo json_encode([
        'status' => 'empty',
        'message' => 'Nenhum pedido encontrado.'
    ]);
}
?>
