
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <h1>Login</h1>
    <form id="formLogin">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required><br><br>

        <button type="submit">Entrar</button>
        <br>

    </form>

<span>Ainda não possui conta?</span>
<a href="cadastro.php">Cadastrar</a>
<button id="esquecisenhamodal">Esqueci minha senha</button>

<div class="modal fade" tabindex="-1" id="modalsenha">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Recuperar Senha</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span>Digite seu e-mail para recuperar a senha</span>
                <form method="POST" id="esquecisenhaform">
                    <input type="email" name="email" placeholder="Seu e-mail" required>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                    <span id="mensagememail"></span>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="mensagem"></div>

    <script>
        $(document).ready(function() {
            $('#esquecisenhamodal').on('click', function() {
            $('#modalsenha').modal('show');
        });
        
        $('#esquecisenhaform').on('submit', function(event) {
            event.preventDefault();

            $.ajax({
                url: '../services/recuperar_senha.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    // Exibe a mensagem de resposta
                    $('#mensagememail').html(response.message);
                    if (response.status === 'success') {
                        $('#mensagememail').css('color', 'green');
                    } else {
                        $('#mensagememail').css('color', 'red');
                    }
                }
            });
        });
    
            
            $('#formLogin').on('submit', function(event) {
                event.preventDefault();

                $.ajax({
                    url: '../controllers/login.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json', // Esperando uma resposta JSON
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#mensagem').html('<div style="color:green;">' + response.message + '</div>');
                            // Redireciona após 1 segundo
                            setTimeout(function() {
                                window.location.href = 'index.php'; // Mude para o caminho correto se necessário
                            }, 1000);
                        } else {
                            $('#mensagem').html('<div style="color:red;">' + response.message + '</div>');
                        }
                    },
                    error: function() {
                        $('#mensagem').html('<div style="color:red;">Erro ao realizar login.</div>');
                    }
                });
            });


            $('#lembrarSenha').on('click', function(event) {
                event.preventDefault();
                alert("Função de lembrar senha não implementada.");
            });
        });
    </script>
</body>

</html>
