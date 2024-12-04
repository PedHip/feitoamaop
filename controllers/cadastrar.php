<?php

require_once '../config/db.php';
require_once '../models/usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados do formulário
    $usuario->setNome($_POST['nome']);
    $usuario->setTelefone($_POST['telefone']);
    $usuario->setEmail($_POST['email']);
    $usuario->setSenha($_POST['senha']); // Senha não criptografada aqui

    // Chame o método para cadastrar o usuário
    echo $usuario->cadastrar();
}
?>
