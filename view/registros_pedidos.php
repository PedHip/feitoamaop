<?php
session_start();

if (isset($_SESSION['nome'])) {
    $nome_usuario = $_SESSION['nome'];
} else {
    $nome_usuario = 'logar';
}
// Verifica se o usuário está logado e se é um administrador
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../view/index.php");
    exit; // Certifique-se de usar exit após o redirecionamento
}

require_once '../config/db.php';
require_once '../models/usuario.php';


$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);
$usuarios = $usuario->listarUsuarios();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="shortcut icon" type="imagex/png" href="../src/imagens/website/balloon.png">
    <link rel="stylesheet" href="../src/styles/backend.css">
    <link rel="stylesheet" href="../src/styles/styles.css">
    <link rel="stylesheet" href="../src/styles/index.css"> 
    <title>Registros de Usuários</title>
    <script src="../src/js/verificarusuario.js"></script>
<style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Style+Script&display=swap');
    </style>
</head>

<body>
    <?php
    include '../module/header.php';

    ?>
    <main>
        <div class="secaoregistros">
            <h1 class="h1r" >Registros de Pedidos</h1>
    
            <input type="text" id="campo-pesquisa" class="form-control2" placeholder="Pesquisar pedidos...">
    
            <div class="table-container">
                <table id="pedidosTable" class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuário</th>
                            <th>Contato</th>
                            <th>Produtos</th>
                            <th>Preço Total</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Os dados dos pedidos serão inseridos aqui pelo AJAX -->
                    </tbody>
                </table>
            </div>
    
            <nav aria-label="Page navigation">
                <ul class="pagination" id="pagination">

                </ul>
            </nav>
        </div>
    
    </main>
    <?php
    include '../module/footer.php';
    include '../module/navmobile.php';
    ?>
</body>

 <div class="modal fade" id="modalDescricao" tabindex="-1" aria-labelledby="modalDescricaoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDescricaoLabel">Descrição do Pedido</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <textarea id="modalDescText" class="form-control" rows="10" readonly></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-form btn-modal" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

    <script>
        var nomeUsuario = "<?php echo $nome_usuario; ?>";
        document.getElementById("mensagemnome").innerHTML = nomeUsuario;
        
        $(document).ready(function() {
            // Variáveis globais
            let paginaAtual = 1;
            let timeout; // Variável para debounce

            function carregarPedidos(pagina = 1, pesquisa = '') {
                $.ajax({
                    url: '../controllers/listar_pedidos.php',
                    type: 'GET',
                    data: {
                        pagina: pagina,
                        pesquisa: pesquisa
                    },
                    dataType: 'json',
                    success: function(data) {
                        var tbody = $('#pedidosTable tbody');
                        tbody.empty(); // Limpa os dados existentes

                        if (data.pedidos.length > 0) {
                            $.each(data.pedidos, function(index, pedido) {
                                var statusClass = ''; // Variável para armazenar a classe de cor

                                // Determina a classe com base no status
                                switch (pedido.status) {
                                    case 'pendente':
                                        statusClass = 'status-pendente';
                                        break;
                                    case 'processando':
                                        statusClass = 'status-processando';
                                        break;
                                    case 'pago':
                                        statusClass = 'status-pago';
                                        break;
                                    case 'produção':
                                        statusClass = 'status-producao';
                                        break;
                                    case 'finalizado':
                                        statusClass = 'status-finalizado';
                                        break;
                                    case 'cancelado':
                                        statusClass = 'status-cancelado';
                                        break;
                                    case 'reembolsado':
                                        statusClass = 'status-reembolsado';
                                        break;
                                    case 'processando reembolso':
                                        statusClass = 'status-processandoR';
                                        break;
                                    default:
                                        statusClass = ''; // Caso não tenha um status conhecido, não adiciona cor
                                        break;
                                }

                                // Adiciona a linha da tabela com a classe de cor
                                tbody.append('<tr>' +
                                    '<td>' + pedido.id_pedido + '</td>' +
                                    '<td>' + pedido.usuario_pedido + '</td>' +
                                    '<td>' + pedido.usuario_contato + '</td>' +
                                    '<td><textarea class="form-control desc_prod" rows="2" readonly>' + pedido.produtos + '</textarea></td>' +
                                    '<td>' + pedido.preco_total + '</td>' +
                                    '<td>' +
                                    '<select class="status-pedido form-control ' + statusClass + '" data-id="' + pedido.id_pedido + '">' +
                                    '<option value="Pendente"' + (pedido.status === 'pendente' ? ' selected' : '') + '>Pendente</option>' +
                                    '<option value="Processando"' + (pedido.status === 'processando' ? ' selected' : '') + '>Processando</option>' +
                                    '<option value="Pago"' + (pedido.status === 'pago' ? ' selected' : '') + '>Pago</option>' +
                                    '<option value="Produção"' + (pedido.status === 'produção' ? ' selected' : '') + '>Produção</option>' +
                                    '<option value="Finalizado"' + (pedido.status === 'finalizado' ? ' selected' : '') + '>Finalizado</option>' +
                                    '<option value="Cancelado"' + (pedido.status === 'cancelado' ? ' selected' : '') + '>Cancelado</option>' +
                                    '<option value="Reembolsado"' + (pedido.status === 'reembolsado' ? ' selected' : '') + '>Reembolsado</option>' +
                                    '<option value="Processando reembolso"' + (pedido.status === 'processando reembolso' ? ' selected' : '') + '>Processando reembolso</option>' +
                                    '</select>' +
                                    '</td>' +
                                    '<td><button class=" btn-form btn-excluir" data-id="' + pedido.id_pedido + '">Apagar</button></td>' +
                                    '</tr>');
                            });


                            // Gera a paginação
                            gerarPaginacao(data.total, 10, pagina);
                        } else {
                            tbody.append('<tr><td colspan="7">Nenhum pedido encontrado.</td></tr>');
                        }
                    },
                    error: function() {
                        alert('Erro ao carregar os pedidos.');
                    }
                });
            }

            // Evento para pesquisa em tempo real com debounce
            $('#campo-pesquisa').on('input', function() {
                clearTimeout(timeout); // Limpa o timeout anterior
                var pesquisa = $(this).val();

                // Debounce de 300ms
                timeout = setTimeout(function() {
                    carregarPedidos(paginaAtual, pesquisa); // Chama a função com a pesquisa em tempo real
                }, 300);
            });

            // Evento para atualizar o status do pedido
            $(document).on('change', '.status-pedido', function() {
                var id_pedido = $(this).data('id');
                var novo_status = $(this).val();

                $.ajax({
                    url: '../controllers/atualizar_status.php',
                    type: 'POST',
                    data: {
                        id_pedido: id_pedido,
                        status: novo_status
                    },
                    dataType: 'json',
                    success: function(response) {
                        alert(response.mensagem);
                    },
                    error: function() {
                        alert('Erro ao atualizar o status do pedido.');
                    }
                });
            });

            // Evento de clique para o botão de apagar
            $(document).on('click', '.btn-excluir', function() {
                var id_pedido = $(this).data('id');
                if (confirm('Tem certeza que deseja apagar este pedido?')) {
                    $.ajax({
                        url: '../controllers/apagar_pedido.php',
                        type: 'POST',
                        data: {
                            id_pedido: id_pedido
                        },
                        dataType: 'json',
                        success: function(response) {
                            alert(response.mensagem);
                            if (response.status === 'sucesso') {
                                carregarPedidos(); // Atualiza a lista de pedidos após a exclusão
                            }
                        },
                        error: function() {
                            alert('Erro ao tentar apagar o pedido.');
                        }
                    });
                }
            });

            function gerarPaginacao(totalPedidos, limite, paginaAtual) {
                var totalPaginas = Math.ceil(totalPedidos / limite);
                var paginationDiv = $('#pagination');
                paginationDiv.empty(); // Limpa a paginação existente

                // Botão Anterior
                if (paginaAtual > 1) {
                    paginationDiv.append('<li><button class="pagina " data-pagina="' + (paginaAtual - 1) + '">Anterior</button></li> ');
                }

                // Determina o intervalo de páginas a serem exibidas
                var inicio = Math.max(1, paginaAtual - 2); // Começa 2 páginas atrás
                var fim = Math.min(totalPaginas, inicio + 4); // Limita a 5 páginas visíveis

                for (var i = inicio; i <= fim; i++) {
                    if (i === paginaAtual) {
                        paginationDiv.append('<li><button class="pagina " data-pagina="' + i + '">' + i + '</button></li> '); // Página atual com classe "active"
                    } else {
                        paginationDiv.append('<li><button class="pagina " data-pagina="' + i + '">' + i + '</button></li>');
                    }
                }

                // Botão Próximo
                if (paginaAtual < totalPaginas) {
                    paginationDiv.append('<li><button class="pagina  " data-pagina="' + (paginaAtual + 1) + '">Próximo</button></li>');
                }
            }

            // Carrega os pedidos na primeira vez
            carregarPedidos();

            // Evento de clique para a paginação
            $(document).on('click', '.pagina', function(e) {
                e.preventDefault();
                var pagina = $(this).data('pagina');
                carregarPedidos(pagina, $('#campo-pesquisa').val()); // Passa a pesquisa atual para a função
            });

            $(document).on('click', '.desc_prod', function() {
                var descricao = $(this).val();
                $('#modalDescText').val(descricao); // Define a descrição no textarea do modal
                $('#modalDescricao').modal('show'); // Mostra o modal
            });
        });
    </script>


</html>
