<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/db.php';
require_once '../models/produto.php';
require_once '../models/pedido.php';

$database = new database();
$db = $database->getConnection();
$produto = new produto($db);
$pedido = new pedido($db);

// Dados do usuário logado
$usuario_pedido = $_SESSION['nome'];
$usuario_contato = ($_SESSION['email']) . ' ' . ($_SESSION['telefone']);

// Verifica se 'produtos' foi enviado como uma string JSON ou já como um array
if (isset($_POST['produtos'])) {
    if (is_string($_POST['produtos'])) {
        // Se for uma string, decodifica em array
        $produtosSelecionados = json_decode($_POST['produtos'], true);
    } elseif (is_array($_POST['produtos'])) {
        // Se já for um array, apenas atribui
        $produtosSelecionados = $_POST['produtos'];
    } else {
        $produtosSelecionados = [];
    }
} else {
    $produtosSelecionados = [];
}

// Verificação de login
if (empty($usuario_pedido) || empty($usuario_contato)) {
    echo json_encode(['status' => 'error', 'message' => 'Faça Login para pedir.']);
    exit();
}

// Verificação de produtos selecionados
if (empty($produtosSelecionados)) {
    echo json_encode(['status' => 'error', 'message' => 'Nenhum produto selecionado.']);
    exit();
}

$produtosInfo = [];
$produtosFormatados = [];
$precoTotal = 0; // Inicializando o total

foreach ($produtosSelecionados as $produtoSelecionado) {
    $id_prod = $produtoSelecionado['id'];
    $quantidade = $produtoSelecionado['quantidade'];

    // Buscar o produto no banco de dados
    $produtoInfo = $produto->buscarProdutoPorId($id_prod);
    if ($produtoInfo) {
        // Adicionando as informações do produto
        $produtosInfo[] = [
            'nome' => $produtoInfo['nome_prod'],
            'descricao' => $produtoInfo['desc_prod'],
            'preco' => $produtoInfo['preco_prod'],
            'quantidade' => $quantidade
        ];

        // Formatar a descrição do produto no formato desejado
        $produtosFormatados[] = "Produto: " . $produtoInfo['nome_prod'] . " (Descrição: " . $produtoInfo['desc_prod'] . ", Quantidade: " . $quantidade . ")";
        
        // Calcular o preço total
        $precoTotal += (float)$produtoInfo['preco_prod'] * $quantidade;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Produto não encontrado no banco de dados.']);
        exit();
    }
}

// Garantir que o preço total seja válido
if ($precoTotal <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Preço total inválido.']);
    exit();
}

// Formatar a lista de produtos selecionados para o formato final
$produtosFormatados = implode(" // ", $produtosFormatados);

// Atribuir os valores para o pedido
$pedido->setUsuarioPedido($usuario_pedido);
$pedido->setUsuarioContato($usuario_contato);
$pedido->setProdutos($produtosFormatados);
$pedido->setPrecoTotal($precoTotal);

// Passando os IDs dos produtos selecionados para o método setId_prod
$pedido->setId_prod($produtosSelecionados); // Passar os IDs dos produtos selecionados

// Tentar cadastrar o pedido
if ($pedido->cadastrarPedido()) {
    echo json_encode(['status' => 'success', 'message' => 'Pedido realizado com sucesso!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao realizar o pedido.']);
}
?>
