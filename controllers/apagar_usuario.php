<?php
require_once '../config/db.php';
require_once '../models/usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id']; // ID do usuário a ser apagado

    // Chama a função para apagar o usuário
    $response = $usuario->apagarUsuario($id);

    // Retorna a resposta em formato JSON
    echo json_encode($response);
    exit; // Certifique-se de encerrar o script para não enviar mais conteúdo
}
?>
