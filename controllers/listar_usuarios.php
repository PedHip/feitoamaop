<?php
session_start();

require_once '../config/db.php';
require_once '../models/usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

// Verifica se o usuário está logado e se é um administrador
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    http_response_code(403);
    echo json_encode(['message' => 'Acesso negado.']);
    exit;
}

// Recebe o termo de busca
$term = isset($_GET['term']) ? $_GET['term'] : '';

// Verifica se uma página foi especificada para a listagem
if (isset($_GET['pagina'])) {
    // Pega a página da requisição
    $pagina = intval($_GET['pagina']);
    $usuarios = $usuario->listarUsuarios($pagina);
    $totalUsuarios = $usuario->contarUsuarios();

    echo json_encode(['usuarios' => $usuarios, 'total' => $totalUsuarios]);
} else {
    // Se não houver página, realiza a busca de usuários
    $usuarios = $usuario->buscarUsuarios($term);
    echo json_encode($usuarios);
}
?>
