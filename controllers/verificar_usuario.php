<?php
session_start();

// Simulação do estado do usuário
$response = array();
$response['logado'] = false;
$response['tipo'] = 'comum'; // Tipo padrão

if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true) {
    $response['logado'] = true;
    $response['tipo'] = $_SESSION['tipo_usuario']; // 'administrador' ou 'comum'
}

// Retorna o estado do usuário em formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
