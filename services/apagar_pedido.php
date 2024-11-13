<?php
require_once '../config/db.php'; // Inclua o arquivo de conexão com o banco de dados
require_once '../models/pedido.php'; // Inclua a classe Pedido

// Cria uma nova conexão
$db = new Database(); // Assumindo que você tem uma classe Database para conexão
$conn = $db->getConnection(); // Obtém a conexão

// Cria uma nova instância da classe Pedido
$pedido = new Pedido($conn);

// Obtém o ID do pedido a ser apagado
$id_pedido = isset($_POST['id_pedido']) ? $_POST['id_pedido'] : null;

// Se o ID do pedido for válido, tenta apagar o pedido
if ($id_pedido) {
    $resultado = $pedido->apagarPedido($id_pedido);
    if ($resultado) {
        echo json_encode(['status' => 'sucesso', 'mensagem' => 'Pedido apagado com sucesso!']);
    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao apagar o pedido.']);
    }
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID do pedido não informado.']);
}
?>
