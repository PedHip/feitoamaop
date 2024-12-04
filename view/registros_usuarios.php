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

<head>
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
            <h1 class="h1r">Registros de Usuários</h1>
    
            <div class="mb-3">
                <input type="text" id="search" class="form-control2" placeholder="Pesquisar">
            </div>
    
            <div class="table-container">
                <table class="table" id="usuariosTable">
                    <thead>
                        <tr>
                            <th>ID Atual</th>
                            <th>ID Novo</th>
                            <th>Tipo</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Nova Senha</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dados serão inseridos aqui via AJAX -->
                    </tbody>
                </table>
            </div>
    
            <nav aria-label="Page navigation">
                <ul class="pagination" id="paginacao">
                    <!-- Paginação será inserida aqui -->
                </ul>
            </nav>
        </div>
    </main>
   <?php
    include '../module/footer.php';
    include '../module/navmobile.php';
    ?>
</body>

    <script>
        var nomeUsuario = "<?php echo $nome_usuario; ?>";
        document.getElementById("mensagemnome").innerHTML = nomeUsuario;
        
        $(document).ready(function() {
            let timer; // Variável para armazenar o timer
            let paginaAtual = 1; // Variável para acompanhar a página atual

            function listarUsuarios() {
                $.ajax({
                    url: '../controllers/listar_usuarios.php',
                    type: 'GET',
                    data: {
                        pagina: paginaAtual
                    },
                    dataType: 'json',
                    success: function(response) {
                        atualizarTabela(response.usuarios);
                        atualizarPaginas(response.total); // Atualiza a paginação
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error(jqXHR.responseText);
                    }
                });
            }

            function atualizarTabela(usuarios) {
                const tableBody = $("#usuariosTable tbody");
                tableBody.empty(); // Limpa a tabela

                usuarios.forEach(u => {
                    tableBody.append(`
                <tr data-id="${u.id}">
                    <td><input type="text" value="${u.id}" class="form-control id_atual" readonly></td>
                    <td><input type="text" value="${u.id}" class="form-control id_novo"></td>
                    <td>
                        <select class="form-control tipo_usuario">
                            <option value="comum" ${u.tipo_usuario === 'comum' ? 'selected' : ''}>Comum</option>
                            <option value="administrador" ${u.tipo_usuario === 'administrador' ? 'selected' : ''}>Administrador</option>
                        </select>
                    </td>
                    <td><input type="text" value="${u.nome}" class="form-control nome"></td>
                    <td><input type="text" value="${u.telefone}" class="form-control telefone"></td>
                    <td><input type="text" value="${u.email}" class="form-control email"></td>
                    <td><input type="text" class="form-control senha" placeholder="Nova Senha"></td>
                    <td>
                        <button class="btn-form editar">Editar</button>
                        <button class="btn-form apagar">Apagar</button>
                    </td>
                </tr>
            `);
                });
            }

            function atualizarPaginas(totalUsuarios) {
                const usuariosPorPagina = 15; // Número de usuários por página
                const totalPaginas = Math.ceil(totalUsuarios / usuariosPorPagina);
                let paginacao = '';

                // Botão "Anterior"
                if (paginaAtual > 1) {
                    paginacao += `<li><button class="pagina" data-pagina="${paginaAtual - 1}">Anterior</button></li>`;
                }

                // Exibe um máximo de 5 páginas de cada vez
                const maxPaginasVisiveis = 5;
                const inicioPagina = Math.max(1, paginaAtual - Math.floor(maxPaginasVisiveis / 2));
                const fimPagina = Math.min(totalPaginas, inicioPagina + maxPaginasVisiveis - 1);

                for (let i = inicioPagina; i <= fimPagina; i++) {
                    paginacao += `<li><button class="pagina ${i === paginaAtual ? 'active' : ''}" data-pagina="${i}">${i}</button></li>`;
                }

                // Botão "Próximo"
                if (paginaAtual < totalPaginas) {
                    paginacao += `<li><button class="pagina" data-pagina="${paginaAtual + 1}">Próximo</button></li>`;
                }

                $('#paginacao').html(paginacao); // Atualiza a lista de páginas
            }

            $(document).on('click', '.pagina', function(event) {
                event.preventDefault();
                const pagina = $(this).data('pagina');
                if (pagina) {
                    paginaAtual = pagina; // Atualiza a página atual
                    listarUsuarios(); // Atualiza a lista de usuários ao trocar de página
                }
            });


            $("#search").on("keyup", function() {
                clearTimeout(timer); // Limpa o timer anterior
                const searchTerm = $(this).val();

                timer = setTimeout(function() {
                    $.ajax({
                        url: '../controllers/listar_usuarios.php',
                        type: 'GET',
                        data: {
                            term: searchTerm
                        },
                        dataType: 'json',
                        success: function(usuarios) {
                            atualizarTabela(usuarios);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error(jqXHR.responseText);
                        }
                    });
                });
            });

            $(document).on('click', '.editar', function() {
                const row = $(this).closest('tr');
                const idAtual = row.find('.id_atual').val();
                const idNovo = row.find('.id_novo').val();
                const tipo_usuario = row.find('.tipo_usuario').val();
                const nome = row.find('.nome').val();
                const telefone = row.find('.telefone').val();
                const email = row.find('.email').val();
                const senha = row.find('.senha').val();

                $.ajax({
                    url: '../controllers/editar_usuario.php',
                    type: 'POST',
                    data: {
                        idAtual,
                        idNovo,
                        tipo_usuario,
                        nome,
                        telefone,
                        email,
                        senha
                    },
                    dataType: 'json', // Esperando uma resposta JSON
                    success: function(response) {
                        // Exibe a mensagem em um alert
                        if (response.status === 'error') {
                            alert(response.message); // Mensagem de erro
                        } else {
                            alert(response.message); // Mensagem de sucesso
                            listarUsuarios(); // Atualiza a lista de usuários após editar
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error(jqXHR.responseText);
                        alert('Erro ao realizar a operação.'); // Mensagem genérica de erro
                    }
                });
            });


            $(document).on('click', '.apagar', function() {
                const row = $(this).closest('tr');
                const id = row.data('id'); // Pega o ID do usuário a ser apagado

                if (confirm("Tem certeza que deseja apagar?")) {
                    $.ajax({
                        url: '../controllers/apagar_usuario.php', // URL do PHP que apaga o usuário
                        type: 'POST',
                        data: {
                            id: id
                        }, // Passando o ID do usuário para o PHP
                        dataType: 'json', // Espera uma resposta JSON
                        success: function(response) {
                            console.log(response); // Exibe a resposta do PHP no console
                            if (response.status === 'error') {
                                alert(response.message); // Se erro, exibe mensagem de erro
                            } else {
                                alert(response.message); // Se sucesso, exibe mensagem de sucesso
                                listarUsuarios(); // Atualiza a lista de usuários após apagar
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error(jqXHR.responseText); // Exibe detalhes do erro no console
                            alert('Erro ao realizar a operação.'); // Mensagem genérica de erro
                        }
                    });
                }
            });




            // Chama a função para listar usuários ao carregar a página
            listarUsuarios();
        });
    </script>

</html>
