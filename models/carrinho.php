<?php
require_once '../config/db.php';  // Incluindo a conexão com o banco de dados

class Carrinho {
    private $conn;
    private $pdo;
    private $table_name = "carrinho";

    // Atributos do carrinho
    private $id;
    private $usuario_pedido;
    private $usuario_contato;
    private $produto;
    private $descricao;
    private $valor_total;
    private $quantidade;
    private $id_prod;

    // Construtor único que recebe a conexão PDO
    public function __construct($pdo) {
        $this->pdo = $pdo; // Usando a conexão PDO fornecida
    }

    // Getters e Setters (os métodos de acesso)
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUsuarioPedido() {
        return $this->usuario_pedido;
    }

    public function setUsuarioPedido($usuario_pedido) {
        $this->usuario_pedido = $usuario_pedido;
    }

    public function getUsuarioContato() {
        return $this->usuario_contato;
    }

    public function setUsuarioContato($usuario_contato) {
        $this->usuario_contato = $usuario_contato;
    }

    public function getProduto() {
        return $this->produto;
    }

    public function setProduto($produto) {
        $this->produto = $produto;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function getValorTotal() {
        return $this->valor_total;
    }

    public function setValorTotal($valor_total) {
        $this->valor_total = $valor_total;
    }

    public function getQuantidade() {
        return $this->quantidade;
    }

    public function setQuantidade($quantidade) {
        $this->quantidade = $quantidade;
    }

    public function getIdProd() {
        return $this->id_prod;
    }

    public function setIdProd($id_prod) {
        $this->id_prod = $id_prod;
    }

    // Função para adicionar um produto ao carrinho
    public function adicionarAoCarrinho() {
        // Query para inserir os dados no carrinho
        $query = "INSERT INTO " . $this->table_name . " 
                  (usuario_pedido, usuario_contato, produto, descricao, valor_total, quantidade, id_prod) 
                  VALUES (:usuario_pedido, :usuario_contato, :produto, :descricao, :valor_total, :quantidade, :id_prod)";

        $stmt = $this->pdo->prepare($query);

        // Bind de parâmetros
        $stmt->bindParam(':usuario_pedido', $this->usuario_pedido);
        $stmt->bindParam(':usuario_contato', $this->usuario_contato);
        $stmt->bindParam(':produto', $this->produto);
        $stmt->bindParam(':descricao', $this->descricao);
        $stmt->bindParam(':valor_total', $this->valor_total);
        $stmt->bindParam(':quantidade', $this->quantidade);
        $stmt->bindParam(':id_prod', $this->id_prod); // Bind para id_prod

        // Executa a query e retorna o resultado
        return $stmt->execute();
    }

    // Função para listar os produtos do carrinho de um usuário
public function listarCarrinho($email_usuario) {
    // Query para listar os itens do carrinho com base no e-mail
    $query = "SELECT c.id, c.produto, c.descricao, c.quantidade, c.valor_total, c.id_prod, p.img_prod 
              FROM " . $this->table_name . " c
              LEFT JOIN produtos p ON c.id_prod = p.id_prod
              WHERE c.usuario_contato LIKE :email_usuario";

    $stmt = $this->pdo->prepare($query);

    // Criar uma variável temporária para o email formatado
    $email_formatado = "%" . $email_usuario . "%";

    // Bind de parâmetros
    $stmt->bindParam(':email_usuario', $email_formatado);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $pedidos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pedidos[] = [
                'id' => $row['id'],
                'produto' => $row['produto'],
                'descricao' => $row['descricao'],
                'quantidade' => $row['quantidade'],
                'valor_total' => $row['valor_total'],
                'id_prod' => $row['id_prod'],
                'imagem' => $row['img_prod']  // A imagem do produto agora vem de produtos
            ];
        }
        return $pedidos;
    }

    return null;  // Caso não encontre nenhum produto no carrinho
}


	// Função para remover um produto do carrinho
	public function removerCarrinho($id) {
		// Verifica se o ID do carrinho é válido
		if (empty($id) || !is_numeric($id)) {
			throw new Exception("ID do carrinho inválido.");
		}
	
		// Query para remover o produto do carrinho
		$sql = "DELETE FROM carrinho WHERE id = :id";
	
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	
		// Executa a query e verifica se a remoção foi bem-sucedida
		if ($stmt->execute()) {
			if ($stmt->rowCount() > 0) {
				return true;  // Produto removido com sucesso
			} else {
				throw new Exception("Produto não encontrado ou já removido.");
			}
		} else {
			throw new Exception("Erro ao tentar remover o produto.");
		}
	}
	

	
	
}
