<?php
session_start();

require_once '../config/db.php';
require_once '../models/carrinho.php';
require_once '../models/produto.php';

// Inicia a conexão com o banco de dados
$database = new database();
$db = $database->getConnection();
$carrinho = new carrinho($db);
$produto = new produto($db);

$usuario_pedido = $_SESSION['nome'];
$usuario_contato = ($_SESSION['email']) . ' ' . ($_SESSION['telefone']);
$produtosSelecionados = $_POST['produtos'];

header('Content-Type: application/json');

// Verifique se o usuário está logado e se o nome está armazenado corretamente na sessão
if (!isset($_SESSION['nome']) || empty($_SESSION['nome'])) {
    echo json_encode(['status' => 'error', 'message' => 'Faça Login para adicionar ao carrinho.']);
    exit();
}

// Agora, o nome do usuário estará sendo capturado corretamente
// Insere os produtos no carrinho
foreach ($produtosSelecionados as $produtoSelecionado) {
    $produtoId = $produtoSelecionado['id']; // ID do produto
    $quantidade = $produtoSelecionado['quantidade']; // Quantidade
    $nomeProduto = $produtoSelecionado['nome']; // Nome do produto
    $descricaoProduto = $produtoSelecionado['descricao']; // Descrição do produto
    $valorTotal = $produtoSelecionado['valor_total']; // Valor total do produto

    // Configura os dados do carrinho
    $carrinho->setUsuarioPedido($usuario_pedido);
    $carrinho->setUsuarioContato($usuario_contato);
    $carrinho->setProduto($nomeProduto);
    $carrinho->setDescricao($descricaoProduto);
    $carrinho->setValorTotal($valorTotal);
    $carrinho->setQuantidade($quantidade);
    $carrinho->setIdProd($produtoId); // Atribui o id_prod para o carrinho

    // Tenta adicionar o produto ao carrinho
    if (!$carrinho->adicionarAoCarrinho()) {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao adicionar ao carrinho.']);
        exit();
    }
}

// Resposta de sucesso
echo json_encode(['status' => 'success', 'message' => 'Produtos adicionados ao carrinho com sucesso!']);
exit();
?>
