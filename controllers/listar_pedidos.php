<?php
require_once '../config/db.php';
require_once '../models/pedido.php';

$db = new Database(); // Assumindo que você tem uma classe Database para conexão
$conn = $db->getConnection(); // Obtém a conexão

$pedido = new Pedido($conn);

// Captura os parâmetros de página e pesquisa
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';

// Define o limite de resultados por página
$limite = 10;

// Inicializa a consulta base
$query = "SELECT * FROM pedidos WHERE 1=1"; // Usamos 1=1 para facilitar a adição de condições

// Verifica se a pesquisa não está vazia
if ($pesquisa) {
    // Adiciona os curingas para LIKE
    $pesquisaParam = "%$pesquisa%"; 
    
    // Busca por ID ou por texto (usuario_pedido ou usuario_contato)
    $query .= " AND (id_pedido LIKE :pesquisa OR usuario_pedido LIKE :pesquisa OR usuario_contato LIKE :pesquisa)";
}

// Adiciona os limites de resultados e offset
$query .= " LIMIT :limite OFFSET :offset";
$stmt = $conn->prepare($query);

// Adiciona os parâmetros
if ($pesquisa) {
    // Usando o mesmo parâmetro para todas as buscas
    $stmt->bindParam(':pesquisa', $pesquisaParam);
}

$offset = ($pagina - 1) * $limite;
$stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar o total de pedidos com base na pesquisa
$total_query = "SELECT COUNT(*) as total FROM pedidos WHERE 1=1";
if ($pesquisa) {
    $total_query .= " AND (id_pedido LIKE :pesquisa OR usuario_pedido LIKE :pesquisa OR usuario_contato LIKE :pesquisa)";
}

$total_stmt = $conn->prepare($total_query);

if ($pesquisa) {
    // Usando o mesmo parâmetro para contar
    $total_stmt->bindParam(':pesquisa', $pesquisaParam);
}

$total_stmt->execute();
$total_result = $total_stmt->fetch(PDO::FETCH_ASSOC);
$total = $total_result['total'];

// Retorna os dados em formato JSON
echo json_encode(['pedidos' => $pedidos, 'total' => $total]);
?>
