<?php
session_start(); // Inicia a sessão

$response = array();

if (isset($_SESSION['usuario_logado'])) {
    // Destrói todas as variáveis da sessão
    $_SESSION = array();
    session_destroy();
    
    $response['status'] = 'success';
    $response['message'] = 'Logout realizado com sucesso!';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Nenhum usuário logado.';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
