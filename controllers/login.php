<?php
session_start();
require_once '../config/db.php';
require_once '../models/usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados do formulário
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Configura os dados do usuário para autenticação
    $usuario->setEmail($email);
    $usuario->setSenha($senha);

    // Chama o método de autenticação
    $authResult = $usuario->autenticar();

    // Decodifica o JSON retornado
    $authResult = json_decode($authResult, true);

    if ($authResult['status'] === 'success') {
        // Aqui você deve buscar as informações do usuário no banco de dados
        $query = "SELECT nome, email, telefone FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() === 1) {
            $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            // Definindo as variáveis de sessão
            $_SESSION['usuario_logado'] = true;
            $_SESSION['nome'] = $userInfo['nome'];
            $_SESSION['email'] = $userInfo['email'];
            $_SESSION['telefone'] = $userInfo['telefone'];
            $_SESSION['tipo_usuario'] = $authResult['tipo_usuario']; // Usando o tipo de usuário retornado

            echo json_encode(['status' => 'success', 'message' => '<div style="color:green;">Login realizado com sucesso!</div>']);
        } else {
            echo json_encode(['status' => 'error', 'message' => '<div style="color:red;">Usuário não encontrado.</div>']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => '<div style="color:red;">Email ou senha inválidos.</div>']);
    }
}
?>
