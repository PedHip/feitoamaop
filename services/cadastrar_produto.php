<?php

require_once '../config/db.php';
require_once '../models/produto.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $produto = new Produto($db);

    $produto->setNomeProd($_POST['nome_prod']);
    $produto->setDescProd($_POST['desc_prod']);
    $produto->setTipoProd($_POST['tipo_prod']);
    $produto->setPrecoProd($_POST['preco_prod']);

    if (isset($_FILES['img_prod']) && $_FILES['img_prod']['error'] == 0) {
        $img_prod = '../uploads/' . basename($_FILES['img_prod']['name']);
        if (move_uploaded_file($_FILES['img_prod']['tmp_name'], $img_prod)) {
            $produto->setImgProd($img_prod);
            echo $produto->cadastrar();
        } else {
            echo "Erro ao fazer upload da imagem.";
        }
    } else {
        echo "Imagem não enviada ou erro no envio.";
    }
}
?>