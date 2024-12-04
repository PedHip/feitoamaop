<?php
session_start();

if (isset($_SESSION['nome'])) {
    $nome_usuario = $_SESSION['nome'];
} else {
    $nome_usuario = 'logar';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../src/styles/prodCad.css">
    	<link rel="stylesheet" href="../src/styles/styles.css">
	<link rel="stylesheet" href="../src/styles/index.css">
	<link rel="shortcut icon" type="imagex/png" href="../src/imagens/website/balloon.png">
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
		@import url('https://fonts.googleapis.com/css2?family=Style+Script&display=swap');
	</style>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<title>cadastra-se</title>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../src/js/verificarusuario.js"></script>
</head>

<body>
	<?php 
    include '../module/header.php';

    ?>
	<main>
		<section id="card2">
			<div class="cadastre">
				<h1>Bem-vindo de volta!</h1>
				<p class="pLogin">Faça login no nosso site para voltar a fazer seus pedidos</p>
				<a href="login.php"><button class="cadastreBttn">sign in</button></a>
			</div>
			<div class="data">
				<div class="login">
					<h1>Cadastre-se</h1>
					<p class="pLogin">Não tem uma conta? Faça um cadastro para que possa realizar os seus pedidos</p>
				</div>
				<form id="formCadastro">
					<div id="dataDivInput">
						<input class="input" type="text" id="nome" name="nome" placeholder="nome" required>
						<input class="input" type="email" id="email" name="email" placeholder="email" required>
						<input class="input" type="tel" id="telefone" name="telefone" placeholder="número" required maxlength="15" pattern="[0-9()+ \-]{1,15}" title="Apenas números e caracteres especiais (até 15 caracteres)">
						<input class="input" type="password" id="senha" name="senha" placeholder="senha" required>
					</div>
					<span id="emailError" style="color:red; display:none;">Este email já está em uso.</span>
					
					<div id="dataDivBttn">
						<button class="dataBttn" type="submit">criar conta</button>
					</div>
					<div id="mensagem"></div>
				</form>
			</div>
		</section>
	</main>
    <?php
    include '../module/footer.php';
    include '../module/navmobile.php';
    ?>

	<script>
		var nomeUsuario = "<?php echo $nome_usuario; ?>";
        document.getElementById("mensagemnome").innerHTML = nomeUsuario;
        
		$(document).ready(function() {
    // Verificação de email ao sair do campo
    $('#email').on('blur', function() {
        const email = $(this).val();
        $.ajax({
            url: '../controllers/verificar_email.php',  // Script para verificar e-mail
            method: 'POST',
            data: { email: email },
            success: function(response) {
                if (response.status === 'error') {
                    $('#emailError').text(response.message).show();  // Exibe mensagem de erro
                } else {
                    $('#emailError').hide();  // Esconde mensagem de erro se e-mail for válido
                }
            },
            error: function() {
                $('#mensagem').html('<div style="color:red;">Erro ao verificar e-mail.</div>');
            }
        });
    });

    // Processamento do formulário
    $('#formCadastro').on('submit', function(event) {
        event.preventDefault();

        if ($('#emailError').is(':visible')) {
            return; // Não envia o formulário se o e-mail já estiver em uso
        }

        $.ajax({
            url: '../controllers/cadastrar.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#mensagem').html('<div style="color:green;">Usuário cadastrado com sucesso!</div>');
                    setTimeout(function() {
                        window.location.href = 'login.php'; // Redireciona após 2 segundos
                    }, 1000);
                } else {
                    $('#mensagem').html('<div style="color:red;">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#mensagem').html('<div style="color:red;">Erro ao realizar o cadastro.</div>');
            }
        });
    });
});

	</script>
</body>

</html>
