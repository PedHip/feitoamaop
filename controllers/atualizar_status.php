<?php
require_once '../config/db.php'; // Inclua o arquivo de conexão com o banco de dados
require_once '../models/pedido.php'; // Inclua a classe Pedido

// Cria uma nova conexão
$db = new Database(); // Assumindo que você tem uma classe Database para conexão
$conn = $db->getConnection(); // Obtém a conexão

// Cria uma nova instância da classe Pedido
$pedido = new Pedido($conn);

// Obtém os dados do pedido
$id_pedido = isset($_POST['id_pedido']) ? $_POST['id_pedido'] : null;
$status = isset($_POST['status']) ? $_POST['status'] : null;

// Se o ID do pedido e o status forem válidos, tenta atualizar o status
if ($id_pedido && $status) {
    $resultado = $pedido->atualizarStatus($id_pedido, $status);
    if ($resultado) {
        echo json_encode(['status' => 'sucesso', 'mensagem' => 'Status atualizado com sucesso!']);
    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao atualizar o status.']);
    }
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID do pedido ou status não informado.']);
}
?>
