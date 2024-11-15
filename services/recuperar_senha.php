<?php

require_once '../config/db.php';
require_once '../models/usuario.php';

header('Content-Type: application/json');

$user = new usuario();

// Verifica se o campo email foi enviado
if (isset($_POST['email'])) {
    $email = trim($_POST['email']);

    // Verifica se o e-mail não está vazio
    if (!empty($email)) {
        if ($user->verifyEmail($email)) {
            // Gera uma senha aleatória
            $senhaAleatoria = substr(bin2hex(random_bytes(8)), 0, 8);

            // Atualiza a senha no banco de dados
            $senhaHash = password_hash($senhaAleatoria, PASSWORD_DEFAULT);
            $user->editarUsuario($email, $senhaHash);

            // Envia a nova senha por e-mail
            mail($email, 'Sua nova senha', 'Sua nova senha é: ' . $senhaAleatoria);

            $response = [
                'status' => 'success',
                'message' => 'Uma nova senha foi enviada para seu email.'
            ];
        } else {
            // E-mail não encontrado
            $response = [
                'status' => 'error',
                'message' => 'Esse email não existe no nosso site.'
            ];
        }
    } else {
        // E-mail vazio
        $response = [
            'status' => 'error',
            'message' => 'Email vazio.'
        ];
    }
} else {
    // Campo e-mail não enviado
    $response = [
        'status' => 'error',
        'message' => 'O campo e-mail é obrigatório.'
    ];
}

// Retorna a resposta JSON
echo json_encode($response);

?>
