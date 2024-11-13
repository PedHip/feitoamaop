<!DOCTYPE html>
<html lang="pt-BR">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Cadastro</title>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

	<h1>Cadastro</h1>
	<form id="formCadastro">
		<label for="nome">Nome:</label>
		<input type="text" id="nome" name="nome" required><br><br>

		<label for="email">Email:</label>
		<input type="email" id="email" name="email" required>
		<span id="emailError" style="color:red; display:none;">Este email já está em uso.</span><br><br>

		<label for="telefone">Telefone:</label>
		<input type="tel" id="telefone" name="telefone" required 
       	maxlength="15" pattern="[0-9()+ \-]{1,15}" title="Apenas números e caracteres especiais (até 15 caracteres)"><br><br>


		<label for="senha">Senha:</label>
		<input type="password" id="senha" name="senha" required><br><br>

		<button type="submit">Cadastrar</button>
	</form>

	<div id="mensagem"></div>

	<script>
		$(document).ready(function() {
			// Verificação de email ao sair do campo
			$('#email').on('blur', function() {
				const email = $(this).val();
				$.ajax({
					url: '../controllers/verificar_email.php',
					method: 'POST',
					data: {
						email: email
					},
					success: function(response) {
						if (response.exists) {
							$('#emailError').show();
						} else {
							$('#emailError').hide();
						}
					},
					error: function() {
						$('#mensagem').html('<div style="color:red;">Erro ao verificar email.</div>');
					}
				});
			});

			// Processamento do formulário
			$('#formCadastro').on('submit', function(event) {
				event.preventDefault();

				if ($('#emailError').is(':visible')) {
					return; // Não envia o formulário se o email já está em uso
				}

				$.ajax({
					url: '../services/cadastrar.php',
					type: 'POST',
					data: $(this).serialize(),
					dataType: 'json', // Adicione esta linha para esperar uma resposta JSON
				success: function(response) {
    $('#mensagem').html(response.message); // Mostra a mensagem de retorno
    if (response.status === 'success') {
        setTimeout(function() {
            window.location.href = 'logar.php'; // Redireciona após 2 segundos
        }, 1000);
    }
},
					error: function() {
						$('#mensagem').html('<div style="color:red;">Erro ao realizar cadastro.</div>');
					}
				});
			});

		});
	</script>
</body>

</html>