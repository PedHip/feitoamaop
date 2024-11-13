<?php
session_start();

if (isset($_SESSION['nome'])) {
    $nome_usuario = $_SESSION['nome'];
} else{
	$nome_usuario = 'logar';
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<link rel="stylesheet" href="index.css">
    
</head>

<body>

    <h1>Bem-vindo ao sistema!</h1>


	<div class="dropdown">
        <span id="mensagemnome"></span> <!-- Nome do usuário -->
        <div id="dropdownContent" class="dropdown-content">
            <a class="dropdownbuttons" id="linkPerfil" href="perfil.php">Perfil</a>
			<a class="dropdownbuttons" id="linkLogin" href="logar.php" style="display:none;">Login</a>
        	<a class="dropdownbuttons" id="linkCadastro" href="cadastro.php" style="display:none;">Cadastro</a>
            <div class="dropdownbuttons" id=logoutButton>Sair</dvi>
        </div>
    </div>

    <nav id="navLinks">
		<span id="mensagemnome" class="mb-6"></span>
        <a id="linkRegistrosU" href="registros_usuarios.php" style="display:none;">Registros de usuários</a>
        <a id="linkRegistrosP" href="cadastro_produtos.php" style="display:none;">Registros de produtos</a>
        <a id="linkRegistrosPe" href="registros_pedidos.php" style="display:none;">Registros de pedidos</a>
    </nav>

    <main id="main">
        <a id="bubble" href="bubble.php">Bubbles</a>
        <a id="box_luxo" href="box_luxo.php">Box De Luxo</a>
        <a id="bubble_box" href="bubble_box.php">Bubble na Box</a>
        <a id="bubble_acrilico" href="bubble_acrilico.php">Bubble Acrílico</a>
        <a id="natal" href="natal.php">Natal</a>
        <a id="dia_das_maes" href="dia_das_maes.php">Dia das Mães</a>
        <a id="caneca" href="caneca.php">Caneca</a>
    </main>

    <script>
        // Passa o nome do usuário para uma variável do JavaScript
        var nomeUsuario = "<?php echo $nome_usuario; ?>";

        // Exibe o nome do usuário no elemento com id "mensagemnome"
        document.getElementById("mensagemnome").innerHTML = nomeUsuario;

        // Ao clicar no nome do usuário, mostra/esconde o dropdown
        document.getElementById("mensagemnome").addEventListener("click", function() {
            var dropdownContent = document.getElementById("dropdownContent");
            // Alterna a visibilidade do conteúdo do dropdown
            dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
        });

        // Fechar o dropdown se clicar fora dele
        window.onclick = function(event) {
            if (!event.target.matches('#mensagemnome') && !event.target.matches('.dropdown-content') && !event.target.matches('.dropdown-content a')) {
                var dropdownContent = document.getElementById("dropdownContent");
                dropdownContent.style.display = "none";
            }
        }

        // Ações dos botões no dropdown
		$('#logoutButton').on('click', function() {
                $.ajax({
                    url: '../controllers/logout.php', // URL do script de logout
                    type: 'POST',
                    success: function(response) {
                        $('#mensagem').html('<div style="color:green;">' + response.message + '</div>');
                        // Redireciona após 1 segundo
                        setTimeout(function() {
                            window.location.href = 'index.php'; // Redireciona para a página inicial
                        }, 1);
                    },
                    error: function() {
                        $('#mensagem').html('<div style="color:red;">Erro ao realizar logout.</div>');
                    }
                });
            });

        $(document).ready(function() {
            // Simulação de chamada AJAX para verificar o estado do usuário
            $.ajax({
                url: '../controllers/verificar_usuario.php', // Script que verifica o estado do usuário
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    const usuarioLogado = response.logado;
                    const tipoUsuario = response.tipo;

                    if (!usuarioLogado) {
                        $('#linkLogin').show();
                        $('#linkCadastro').show();
                        $('#logoutButton').hide();
                        $('#linkRegistrosU').hide();
                        $('#linkRegistrosP').hide();
                        $('#linkRegistrosPe').hide();
                        $('#linkPerfil').hide();
                    } else {
                        $('#logoutButton').show();
                        $('#linkPerfil').show();
                        if (tipoUsuario === 'administrador') {
                            $('#linkRegistrosU').show();
                            $('#linkRegistrosP').show();
                            $('#linkRegistrosPe').show();
                        }
                    }
                },
                error: function() {
                    $('#navLinks').append('<span style="color:red;">Erro ao verificar estado do usuário.</span>');
                }
            });
        });
    </script>


</body>

</html>
