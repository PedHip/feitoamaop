<?php

require_once '../config/db.php';
require_once '../models/usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idAtual = $_POST['idAtual'];
    $idNovo = $_POST['idNovo'];
    $tipo_usuario = $_POST['tipo_usuario'];
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

  // Inicializa uma variável para armazenar mensagens de erro
$mensagensErro = [];

// Verificar se o novo email já existe
if ($usuario->emailExistente($email, $idAtual)) {
    $mensagensErro[] = 'O email já está em uso.';
}

// Verificar se o novo ID já existe
if ($usuario->idExistente($idNovo, $idAtual)) {
    $mensagensErro[] = 'O ID já está em uso.';
}

// Se houver mensagens de erro, retornar todas
if (!empty($mensagensErro)) {
    echo json_encode(['status' => 'error', 'message' => implode(' ', $mensagensErro)]);
    exit; // Para evitar a execução do código de atualização
}

// Se não houver erros, continuar com a atualização
$resultado = $usuario->editarUsuario($idAtual, $idNovo, $tipo_usuario, $nome, $telefone, $email, $senha);
if ($resultado) {
    echo json_encode(['status' => 'success', 'message' => 'Usuário atualizado com sucesso!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar usuário.']);
}

}
?>
