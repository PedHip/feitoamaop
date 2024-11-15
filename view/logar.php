<?php

?>
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

    <span>ainda não possui conta?</span>
    <a href="cadastro.php">cadastrar</a>
    <button id="esquecisenhamodal">esqueci minha senha</button>
    <div class="modal fade" tabindex="-1" id="modalsenha">
        <span>digite seu email para recuperar senha</span>
        <form method="POST" id="esquecisenhaform">
            <input type="email" name="email">
            <button type="submit">enviar</button>
            <span id="mensagememail"></span>
        </form>
    </div>

    <div id="mensagem"></div>

    <script>
        $(document).ready(function() {
            $('#modalsenha').hide;

            $('#esquecisenhamodal').on('click', function(){
                $('#modalsenha').show();
            });

            
            $('#esquecisenhaform').on('submit', function(event){
                event.preventDefault();

                $.ajax({
                    url: '../services/recuperar_senha.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    datatype: 'json',
                    success: function(response) {
                        if (response.status === 'success'){
                            $('#mensagememail').html('response.message')}
                        else{
                            $('#mensagememail').html('response.message');
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
