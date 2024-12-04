

    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("mensagemnome").addEventListener("click", function () {
            var dropdownContent = document.getElementById("dropdownContent");
            dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
        });
    });

    window.onclick = function (event) {
        if (!event.target.matches('#mensagemnome') && !event.target.matches('.dropdown-content') && !event.target.matches('.dropdown-content a')) {
            var dropdownContent = document.getElementById("dropdownContent");
            dropdownContent.style.display = "none";
        }
    };

    document.addEventListener("DOMContentLoaded", () => {
        const mensagemnomemobile = document.getElementById("mensagemnomemobile");
        const dropdown = document.getElementById("dropdownContentMobile");

        if (mensagemnomemobile && dropdown) {
            dropdown.style.display = "none";

            function toggleDropdown() {
                dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
            }

            mensagemnomemobile.addEventListener("click", toggleDropdown);

            window.addEventListener("click", (event) => {
                if (event.target !== mensagemnomemobile && !dropdown.contains(event.target)) {
                    dropdown.style.display = "none";
                }
            });

            console.log("Eventos registrados com sucesso para mensagemnomemobile!");
        }
    });

    $(document).ready(function () {
        $('#logoutButton, #logoutButtonMobile').on('click', function () {
            $.ajax({
                url: '../../controllers/logout.php',
                type: 'POST',
                success: function (response) {
                    $('#mensagem').html('<div style="color:green;">' + response.message + '</div>');
                    setTimeout(function () {
                        window.location.href = 'index.php';
                    }, 0);
                },
                error: function () {
                    $('#mensagem').html('<div style="color:red;">Erro ao realizar logout.</div>');
                }
            });
        });

        

        $.ajax({
            url: '../../controllers/verificar_usuario.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                const usuarioLogado = response.logado;
                const tipoUsuario = response.tipo;

                if (!usuarioLogado) {
                    $('#linkLogin').show();
                    $('#linkCadastro').show();
                    $('#logoutButton').hide();
                    $('#linkProd').hide();
                    $('#linkUser').hide();
                    $('#linkPedidos').hide();
                    $('#linkPerfil').hide();
                    $('#linkLoginMobile').show();
                    $('#linkCadastroMobile').show();
                    $('#logoutButtonMobile').hide();
                    $('#linkProdMobile').hide();
                    $('#linkUserMobile').hide();
                    $('#linkPedidosMobile').hide();
                    $('#linkPerfilMobile').hide();
                } else {
                    $('#logoutButton').show();
                    $('#linkPerfil').show();
                    $('#linkCadastro').hide();
                    $('#linkLogin').hide();
                    $('#logoutButtonMobile').show();
                    $('#linkPerfilMobile').show();
                    $('#linkCadastroMobile').hide();
                    $('#linkLoginMobile').hide();
                    $('#linkUser').hide();
                    $('#linkProd').hide();
                    $('#linkPedidos').hide();
                    $('#linkUserMobile').hide();
                    $('#linkProdMobile').hide();
                    $('#linkPedidosMobile').hide();
                    if (tipoUsuario === 'administrador') {
                        $('#linkUser').show();
                        $('#linkProd').show();
                        $('#linkPedidos').show();
                        $('#linkUserMobile').show();
                        $('#linkProdMobile').show();
                        $('#linkPedidosMobile').show();
                    }
                }
            },
            error: function () {
                $('#navLinks').append('<span style="color:red;">Erro ao verificar estado do usuário.</span>');
            }
        });

        
        
    });


