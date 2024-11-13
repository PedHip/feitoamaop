<?php
require_once '../config/db.php';
require_once '../models/produto.php'; // Certifique-se de que a classe Produto está devidamente implementada

// Crie uma nova instância do banco de dados e obtenha a conexão
$db = new Database(); // Certifique-se de que a classe de conexão ao banco de dados está correta
$conn = $db->getConnection();

// Crie uma nova instância da classe Produto
$produto = new Produto($conn);

// Obtenha o termo da query string
$term = isset($_GET['term']) ? $_GET['term'] : '';

// Adicione logs para verificar o que está acontecendo
error_log("Termo de busca: " . $term);

// Obtenha a página atual da query string
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$produtosPorPagina = 10;
$offset = ($paginaAtual - 1) * $produtosPorPagina;

// Chama a função de busca com paginação
$produtosComPaginacao = $produto->buscarProdutosComPaginacao($term, $paginaAtual, $produtosPorPagina);

// Se não houver produtos com a busca paginada, tente a busca sem paginação
if (empty($produtosComPaginacao)) {
    // Consulta SQL que busca por nome, descrição ou ID do produto
    $query = "SELECT * FROM produtos 
              WHERE tipo_prod LIKE :term 
              OR nome_prod LIKE :term
              OR desc_prod LIKE :term 
              OR id_prod LIKE :term";

    $stmt = $conn->prepare($query);
    $stmt->bindValue(':term', '%' . $term . '%', PDO::PARAM_STR);
    $stmt->execute();

    // Busca todos os produtos correspondentes
    $produtosSemPaginacao = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retorna os resultados em formato JSON
    echo json_encode(['produtos' => $produtosSemPaginacao, 'total' => count($produtosSemPaginacao)]);
} else {
    // Adiciona lógica para contar o total de produtos
    $totalQuery = "SELECT COUNT(*) FROM produtos 
                   WHERE tipo_prod LIKE :term 
                   OR nome_prod LIKE :term
                   OR desc_prod LIKE :term 
                   OR id_prod LIKE :term";
    $totalStmt = $conn->prepare($totalQuery);
    $totalStmt->bindValue(':term', '%' . $term . '%', PDO::PARAM_STR);
    $totalStmt->execute();
    
    // Obtém o total de produtos
    $total = $totalStmt->fetchColumn();

    // Retorna os resultados paginados
    echo json_encode(['produtos' => $produtosComPaginacao, 'total' => $total]);
}
?>
