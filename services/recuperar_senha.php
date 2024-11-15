<?php

require_once: '../config/db.php';
require_once: '../models/usuario.php';

header('Content-Type: application/json');

$user = new Usuario();

// Verifica se o campo email foi enviado
if (isset($_POST['email'])) {
    $email = trim($_POST['email']);

    // Verifica se o e-mail não está vazio e tem um formato válido
    if (!empty($email)) {
        if ($user->verifyEmail($_POST['email'])){
          $senhaAleatória = 'sskjashkjashkas'
          $user->editarUsuario( 'senha=\''. password_hash($senhaAleatoria ).'\'' );
          mailto( $_POST['email'], 'Sua nova senha', 'Sua nova senha é:'. $senhaAleatoria ); 
          $response = [
            'status' => 'success',
            'message' => 'Uma nova senha foi enviada para seu email'
          ];
        }
        else{
        'message' => 'Esse email não exite no nosso site'
        }
    }
    else {
        // E-mail inválido ou não encontrado
        $response = [
            'status' => 'error',
            'message' => 'Email vazio.'
        ];
    }
} else {
    // Caso o campo email não tenha sido enviado
    $response = [
        'status' => 'error',
        'message' => 'O campo e-mail é obrigatório.'
    ];
}

// Retorna a resposta JSON
echo json_encode($response);
?>


?>
