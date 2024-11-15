<?php

require_once '../config/db.php';

class Usuario {
    private $conn;
    private $table_name = "usuarios";
    private $nome;
    private $telefone;
    private $email;
    private $senha;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Getters e Setters
    public function getNome() {
        return $this->nome;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function getTelefone() {
        return $this->telefone;
    }

    public function setTelefone($telefone) {
        $this->telefone = $telefone;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function setSenha($senha) {
        $this->senha = $senha;
    }

    public function cadastrar() {
    // Verifica se o email já está cadastrado
    $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE email = :email";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':email', $this->email);
    $stmt->execute();

    if ($stmt->fetchColumn() > 0) {
        return json_encode(['status' => 'error', 'message' => 'Email já cadastrado.']);
    }

    // Criptografa a senha antes de salvar
    $senhaHash = password_hash($this->senha, PASSWORD_DEFAULT);

    // Se não existir, insira o novo usuário
    $query = "INSERT INTO " . $this->table_name . " (nome, telefone, email, senha) VALUES (:nome, :telefone, :email, :senha)";
    $stmt = $this->conn->prepare($query);

    // Bind dos parâmetros
    $stmt->bindParam(':nome', $this->nome);
    $stmt->bindParam(':telefone', $this->telefone);
    $stmt->bindParam(':email', $this->email);
    $stmt->bindParam(':senha', $senhaHash); // Insere a senha criptografada

    // Executa a query
    if ($stmt->execute()) {
        return json_encode(['status' => 'success', 'message' => 'Cadastro realizado com sucesso!']);
    } else {
        // Captura o erro do banco de dados
        $errorInfo = $stmt->errorInfo();
        return json_encode(['status' => 'error', 'message' => 'Erro ao realizar o cadastro: ' . $errorInfo[2]]);
    }
}	

        public function verifyEmail($email) {
        $query = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Método para editar a senha do usuário
    public function recuperarSenha($email, $senhaHash) {
        $query = "UPDATE usuarios SET senha = :senha WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->bindParam(':email', $email);

        return $stmt->execute();
    }


    public function autenticar() {
        $query = "SELECT senha, tipo_usuario FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
    
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Verifica se a senha fornecida corresponde ao hash armazenado
            if (password_verify($this->senha, $row['senha'])) {
                return json_encode(['status' => 'success', 'tipo_usuario' => $row['tipo_usuario']]);
            }
        }
        return json_encode(['status' => 'error', 'message' => 'Login falhou.']);
    }
    
    public function editarUsuario($idAtual, $idNovo, $tipo_usuario, $nome, $telefone, $email, $senha = null) {
    // Verifica se o novo email já existe
    if ($this->emailExistente($email, $idAtual)) {
        return json_encode(['status' => 'error', 'message' => 'Erro: O email já está em uso.']);
    }

    // Verifica se o novo ID já existe
    if ($this->idExistente($idNovo, $idAtual)) {
        return json_encode(['status' => 'error', 'message' => 'Erro: O ID já está em uso.']);
    }

    // Prepara a query de atualização
    $query = "UPDATE " . $this->table_name . " SET id = :idNovo, tipo_usuario = :tipo_usuario, nome = :nome, telefone = :telefone, email = :email";

    // Se uma nova senha for fornecida, criptografa e adiciona à query
    if (!empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $query .= ", senha = :senha";
    }

    $query .= " WHERE id = :idAtual";

    $stmt = $this->conn->prepare($query);

    // Bind dos parâmetros
    $stmt->bindParam(':idAtual', $idAtual);
    $stmt->bindParam(':idNovo', $idNovo);
    $stmt->bindParam(':tipo_usuario', $tipo_usuario);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);

    // Se uma nova senha for fornecida, faz o bind dela
    if (!empty($senha)) {
        $stmt->bindParam(':senha', $senhaHash);
    }

    // Executa a query
    if ($stmt->execute()) {
        return json_encode(['status' => 'success', 'message' => 'Usuário atualizado com sucesso!']);
    } else {
        return json_encode(['status' => 'error', 'message' => 'Erro ao atualizar usuário.']);
    }
}

	

    public function apagarUsuario($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
    
        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Usuário apagado com sucesso!'];
        } else {
            return ['status' => 'error', 'message' => 'Erro ao apagar usuário.'];
        }
    }
    
	

    public function emailExistente($email, $idAtual) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE email = :email AND id <> :idAtual";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':idAtual', $idAtual);
        $stmt->execute();
        return $stmt->fetchColumn() > 0; // Retorna true se o email já existe
    }

    public function idExistente($idNovo, $idAtual) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE id = :idNovo AND id <> :idAtual";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idNovo', $idNovo);
        $stmt->bindParam(':idAtual', $idAtual);
        $stmt->execute();
        return $stmt->fetchColumn() > 0; // Retorna true se o ID já existe
    }

    public function buscarUsuarios($term) {
        // Prepara a consulta
        $query = "SELECT * FROM " . $this->table_name . " WHERE 
                  nome LIKE :term OR 
                  email LIKE :term OR 
                  telefone LIKE :term";

        // Adiciona a pesquisa por ID com LIKE
        if (is_numeric($term)) {
            $query .= " OR id LIKE :id";
        }

        $stmt = $this->conn->prepare($query);

        // Prepara o termo de pesquisa
        $likeTerm = "%" . $term . "%"; // Para busca parcial
        $stmt->bindParam(':term', $likeTerm);

        // Para a pesquisa por ID
        if (is_numeric($term)) {
            $stmt->bindParam(':id', $likeTerm); // Usa o mesmo LIKE
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarUsuarios($pagina = 1, $limite = 15) {
        $offset = ($pagina - 1) * $limite;
        $query = "SELECT * FROM " . $this->table_name . " LIMIT :limite OFFSET :offset";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarUsuarios() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }
}
?>
