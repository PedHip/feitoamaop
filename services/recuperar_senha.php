<?php

require_once '../config/db.php'; // Inclua o arquivo com a classe ou função de conexão
require_once '../models/usuario.php';

header('Content-Type: application/json');

// Crie a conexão com o banco de dados
$database = new Database(); // Substitua 'Database' pela sua classe de conexão específica
$connection = $database->getConnection(); // Se necessário, adapte o método para obter a conexão

// Instancie a classe Usuario passando a conexão
$user = new Usuario($connection);

// Verifica se o campo email foi enviado
if (isset($_POST['email'])) {
    $email = trim($_POST['email']);

    // Verifica se o e-mail não está vazio
    if (!empty($email)) {
        // Verifica se o e-mail existe no banco de dados
        if ($user->verifyEmail($email)) {
            // Gera uma senha aleatória de 8 caracteres
            $senhaAleatoria = substr(bin2hex(random_bytes(8)), 0, 8);

            // Criptografa a senha antes de salvar no banco de dados
            $senhaHash = password_hash($senhaAleatoria, PASSWORD_DEFAULT);

            // Atualiza a senha no banco de dados
            if ($user->recuperarSenha($email, $senhaHash)) {
                // Envia a nova senha por e-mail
                $assunto = 'Sua nova senha';
                $mensagem = 'Sua nova senha é: ' . $senhaAleatoria;
                $headers = 'From: no-reply@seusite.com.br';

                // Envia o e-mail para o usuário
                if (mailto($email, $assunto, $mensagem, $headers)) {
                    $response = [
                        'status' => 'success',
                        'message' => 'Uma nova senha foi enviada para seu email.'
                    ];
                } else {
                    $response = [
                        'status' => 'error',
                        'message' => 'Erro ao enviar o e-mail.'
                    ];
                }
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Erro ao atualizar a senha no banco de dados.'
                ];
            }
        } else {
            // E-mail não encontrado no banco de dados
            $response = [
                'status' => 'error',
                'message' => 'Esse e-mail não existe no nosso site.'
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
