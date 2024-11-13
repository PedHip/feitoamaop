<?php

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    // Verifica se a chave "id" está definida no array POST
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        // Prepara a consulta para apagar o produto
        $query = "DELETE FROM produtos WHERE id_prod = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo "Produto apagado com sucesso!";
        } else {
            echo "Erro ao apagar produto.";
        }
    } else {
        echo "ID do produto não fornecido.";
    }
} else {
    echo "Método não permitido.";
}
