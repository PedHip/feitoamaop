<?php

require_once '../config/db.php';
require_once '../models/usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    // Verifique se o email já está cadastrado
    $query = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $response = array();
    $response['exists'] = ($stmt->fetchColumn() > 0);

    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
