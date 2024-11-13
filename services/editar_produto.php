<?php

require_once '../config/db.php';
require_once '../models/produto.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $produto = new Produto($db);

    // Coleta os dados do formulário
    $produto->setIdNovo($_POST['id_novo']);
    $produto->setIdAtual($_POST['id_atual']);
    $produto->setNomeProd($_POST['nome_prod']);
    $produto->setDescProd($_POST['desc_prod']);
    $produto->setPrecoProd($_POST['preco_prod']);
    
    // Coleta o tipo de produto de forma segura
    $tipo_prod = isset($_POST['tipo_prod']) ? $_POST['tipo_prod'] : null;
    $produto->setTipoProd($tipo_prod);

    // Inicializa a variável da imagem
    $img_prod = null;

    // Lida com o upload da nova imagem se existir
    if (isset($_FILES['img_prod']) && $_FILES['img_prod']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/'; // Diretório onde as imagens serão salvas
        $img_prod = $uploadDir . basename($_FILES['img_prod']['name']);
        
        // Move a imagem para o diretório
        if (!move_uploaded_file($_FILES['img_prod']['tmp_name'], $img_prod)) {
            echo "Erro ao fazer upload da imagem.";
            exit;
        }
        $produto->setImgProd($img_prod);
    } else {
        // Se nenhuma nova imagem foi enviada, manter a imagem atual
        $produto->setImgProd($produto->getImagemAtual($produto->getIdAtual()));
    }

    // Verifica se o novo ID já existe no banco de dados
    if ($produto->getIdNovo() !== $produto->getIdAtual()) {
        if ($produto->idExistente($produto->getIdNovo())) {
            echo "Erro: O novo ID do produto já existe. Por favor, escolha um ID diferente.";
            exit;
        }
    }

    // Atualiza os dados do produto
    if ($produto->atualizarProduto()) {
        echo "Produto atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar produto.";
    }
} else {
    echo "Método não permitido.";
}

?>