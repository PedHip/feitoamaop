<?php
session_start();

if (isset($_SESSION['nome'])) {
    $nome_usuario = $_SESSION['nome'];
} else {
    $nome_usuario = 'logar';
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Box de Luxo</title>
    <link rel="shortcut icon" type="imagex/png" href="../src/imagens/website/balloon.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../src/styles/pedir.css">
    <link rel="stylesheet" href="../src/styles/produtos.css">
    <link rel="stylesheet" href="../src/styles/index.css">
    <link rel="stylesheet" href="../src/styles/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Style+Script&display=swap');
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="../src/js/verificarusuario.js"></script>
</head>

<body>
   <?php
    include '../module/header.php';
    ?>
    <main>
        <section id="headerMobileMaes"></section>
        <div class="padding">
            <section id="apresentacao" class="pcbox">
                <div id="bannerAp">
                    <div id="apresentacaoText">
                        <h1>Box de Luxo</h1>
                        <p>Nossa box de luxo proporciona uma experiência culinária sofisticada, recheada com delícias gourmet e apresentada com elegância. Perfeita tanto para indulgência pessoal quanto para presentear alguém especial, ela transforma qualquer ocasião em um momento inesquecível com seus sabores exclusivos e requinte.</p>
                    </div>
                </div>
            </section>
            <section id="bubblesDisp">
                <div id="bubblesDispText">
                    <h2>Presentes incríveis</h2>
                    <p>para construir lembranças inesquecíveis</p>
                </div>
                <div id="boxVitrine">
                    <div class="displayVitrine" id="natalProductsContainer">
                    </div>
                </div>
            </section>
            <div class="resumopedido" id="resumopedido">
                <div class="summary-container mt-4" id="summaryContainer">
                    <h3>Resumo do Pedido</h3>
                    <div class="summary-list">
                        <!-- Resumo dos itens selecionados será inserido aqui -->
                    </div>
                    <div class="summary-total mt-2">
                        <strong>Total: R$ <span id="totalPrice">0.00</span></strong>
                    </div>
                    <div id="text-summary" class="mt-3 mb-3">
				        <p>Ao fazer um pedido, ele será no nosso sistema. Em alguns dias entraremos em contato para dar continuidade ao pedido. Você pode acompanhar a situação do seu pedido na sua página de perfil.</p>
			        </div>
                </div>
                <div class="">
                    <button id="sendButton" class="btn btn-primary botao ">Enviar</button>
                    <button id="addToCartButton" class="btn btn-primary botao">Adicionar ao Carrinho</button>
                    <div id="messageContainer"></div>
                </div>

            </div>
    </main>

    <script>
            var nomeUsuario = "<?php echo $nome_usuario; ?>";

            document.getElementById("mensagemnome").innerHTML = nomeUsuario;

        $(document).ready(function() {
            // Função para listar os produtos do tipo "natal"
            function listarProdutosNatal() {
                $.ajax({
                    url: '../controllers/listar_produtos.php',
                    type: 'GET',
                    data: {
                        term: 'box_luxo'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.produtos && response.produtos.length > 0) {
                            atualizarCards(response.produtos);
                        } else {
                            alert("Nenhum produto encontrado.");
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error(jqXHR.responseText);
                    }
                });
            }

            listarProdutosNatal(); // Chama a função ao carregar a página

            function atualizarCards(produtos) {
                const container = $("#natalProductsContainer");
                container.empty();

                produtos.forEach(p => {
                    const card = `
            <div class="product-card" data-id="${p.id_prod}" data-preco="${p.preco_prod}">
                <div class='imgVitrine'><img src="${p.img_prod}" alt="Imagem do Produto"></div>
                <div class="product-info">
                    <h5 class="nome_prod">${p.nome_prod}</h5>
                    <div class="desc_prod" title="${p.desc_prod}"><h5>${p.desc_prod}</h5></div>

                    <div class="price">R$ <span class="price-value">${p.preco_prod}</span></div>
                </div>
                <div class="quantity-control" style="display: none;">
                    <div class="quantity">1</div>
                    <button class="botaoquantidade decrease-quantity">-</button>
                    <button class="botaoquantidade increase-quantity">+</button>
                </div>
            </div>`;
                    container.append(card);
                });

                // Adiciona os eventos após criar os elementos de produto
                $('.product-card').off('click').on('click', function() {
                    $(this).toggleClass('selected');
                    const quantityControl = $(this).find('.quantity-control');
                    if ($(this).hasClass('selected')) {
                        quantityControl.show(); // Mostra o controle de quantidade
                    } else {
                        quantityControl.hide(); // Esconde o controle de quantidade
                        quantityControl.find('.quantity').text(1); // Reseta a quantidade para 1
                        atualizarPrecoTotal($(this), 1); // Reseta o preço para o inicial
                    }
                    atualizarResumo(); // Atualiza o resumo após a seleção/deseleção do produto
                });

                // Evento para aumentar a quantidade
                $('.increase-quantity').off('click').on('click', function(e) {
                    e.stopPropagation(); // Evita que o clique no botão deselecione o produto
                    const quantityElement = $(this).siblings('.quantity');
                    let currentQuantity = parseInt(quantityElement.text());
                    currentQuantity++;
                    quantityElement.text(currentQuantity);
                    atualizarPrecoTotal($(this).closest('.product-card'), currentQuantity);
                    atualizarResumo(); // Atualiza o resumo quando a quantidade é alterada
                });

                // Evento para diminuir a quantidade
                $('.decrease-quantity').off('click').on('click', function(e) {
                    e.stopPropagation(); // Evita que o clique no botão deselecione o produto
                    const quantityElement = $(this).siblings('.quantity');
                    let currentQuantity = parseInt(quantityElement.text());
                    if (currentQuantity > 1) {
                        currentQuantity--;
                        quantityElement.text(currentQuantity);
                        atualizarPrecoTotal($(this).closest('.product-card'), currentQuantity);
                        atualizarResumo(); // Atualiza o resumo quando a quantidade é alterada
                    }
                });
            }


            // Função para atualizar o resumo dos produtos selecionados
            function atualizarResumo() {
                const summaryList = $('#summaryContainer .summary-list');
                summaryList.empty(); // Limpa a lista de resumo

                let precoTotal = 0;

                // Itera sobre os produtos selecionados e atualiza a lista de resumo
                $('.product-card.selected').each(function() {
                    const imgSrc = $(this).find('img').attr('src');
                    const nomeProduto = $(this).find('.nome_prod').text();
                    const quantidade = parseInt($(this).find('.quantity').text());
                    const precoUnitario = parseFloat($(this).data('preco'));
                    const precoProduto = precoUnitario * quantidade;
                    precoTotal += precoProduto;

                    const resumoItem = `
            <div class="summary-item d-flex mb-2">
                <img src="${imgSrc}" alt="${nomeProduto}" class="img-sumary mr-2" style="width: 5rem; height: 5rem;">
                <div>
                    <strong>${nomeProduto}</strong><br>
                    Quantidade: ${quantidade}<br>
                    Preço: R$ ${precoProduto.toFixed(2)}
                </div>
            </div>
        `;
                    summaryList.append(resumoItem);
                });

                // Atualiza o preço total no resumo
                $('#totalPrice').text(precoTotal.toFixed(2));

                // Verifica se há produtos selecionados e mostra/oculta o resumo do pedido
                if ($('.product-card.selected').length > 0) {
                    $('#resumopedido').show(); // Mostra o resumo do pedido
                } else {
                    $('#resumopedido').hide(); // Esconde o resumo do pedido
                }


            }

            // Certifique-se de esconder o resumo do pedido inicialmente
            $('#resumopedido').hide();





            // Atualize a função de clique para selecionar/deselecionar produtos para chamar atualizarResumo()
            $('.product-card').on('click', function() {
                $(this).toggleClass('selected');
                const quantityControl = $(this).find('.quantity-control');
                if ($(this).hasClass('selected')) {
                    quantityControl.show(); // Mostra o controle de quantidade
                } else {
                    quantityControl.hide(); // Esconde o controle de quantidade
                    quantityControl.find('.quantity').text(1); // Reseta a quantidade para 1
                    atualizarPrecoTotal($(this), 1); // Reseta o preço para o inicial
                }
                atualizarResumo(); // Atualiza o resumo após a seleção/deseleção do produto
            });

            // Atualize as funções de aumento e diminuição de quantidade para chamar atualizarResumo()
            $('.increase-quantity').on('click', function(e) {
                e.stopPropagation(); // Evita que o clique no botão deselecione o produto
                const quantityElement = $(this).siblings('.quantity');
                let currentQuantity = parseInt(quantityElement.text());
                currentQuantity++;
                quantityElement.text(currentQuantity);
                atualizarPrecoTotal($(this).closest('.product-card'), currentQuantity);
                atualizarResumo(); // Atualiza o resumo quando a quantidade é alterada
            });

            $('.decrease-quantity').on('click', function(e) {
                e.stopPropagation(); // Evita que o clique no botão deselecione o produto
                const quantityElement = $(this).siblings('.quantity');
                let currentQuantity = parseInt(quantityElement.text());
                if (currentQuantity > 1) {
                    currentQuantity--;
                    quantityElement.text(currentQuantity);
                    atualizarPrecoTotal($(this).closest('.product-card'), currentQuantity);
                    atualizarResumo(); // Atualiza o resumo quando a quantidade é alterada
                }
            });


            // Função para atualizar o preço total com base na quantidade selecionada
            function atualizarPrecoTotal(card, quantidade) {
                const precoUnitario = parseFloat(card.data('preco'));
                const precoTotal = precoUnitario * quantidade;
                card.find('.price-value').text(precoTotal.toFixed(2));
            }


            $('#sendButton').on('click', function() {
                const selectedProducts = [];
                $('.product-card.selected').each(function() {
                    const productId = $(this).data('id');
                    const productName = $(this).find('.nome_prod').text();
                    const productDesc = $(this).find('.desc_prod').attr('title');
                    const quantity = parseInt($(this).find('.quantity').text());

                    // Adiciona as informações do produto ao array
                    selectedProducts.push({
                        id: productId,
                        nome: productName,
                        descricao: productDesc,
                        quantidade: quantity
                    });
                });

                console.log('Produtos selecionados:', selectedProducts); // Log para depuração

                if (selectedProducts.length > 0) {
                    $.ajax({
                        url: '../controllers/enviar_pedidos.php',
                        type: 'POST',
                        data: {
                            produtos: JSON.stringify(selectedProducts) // Envia os produtos como JSON
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log('Resposta do servidor:', response); // Log da resposta do servidor
                            const alertClass = response.status === 'success' ? 'alert-success' : 'alert-danger';
                            $('#messageContainer').html(`
                    <div class="alert ${alertClass}" role="alert">
                        ${response.message}
                    </div>
                `);
                        },
                        error: function(jqXHR) {
                            try {
                                const response = JSON.parse(jqXHR.responseText); // Tenta analisar a resposta como JSON
                                $('#messageContainer').html(`
                        <div class="alert alert-danger" role="alert">
                            ${response.message}
                        </div>
                    `); // Exibe a mensagem de erro do servidor
                            } catch (e) {
                                $('#messageContainer').html(`
                        <div class="alert alert-danger" role="alert">
                            <strong>Erro desconhecido!</strong> Por favor, tente novamente.
                        </div>
                    `); // Exibe mensagem padrão se não for JSON
                            }
                        }
                    });
                } else {
                    $('#messageContainer').html(`
            <div class="alert alert-warning" role="alert">
                <strong>Atenção!</strong> Nenhum produto selecionado.
            </div>
        `);
                }
            });


            $('#addToCartButton').on('click', function() {
                const selectedProducts = [];

                // Percorre os produtos selecionados
                $('.product-card.selected').each(function() {
                    const productId = $(this).data('id');
                    const quantity = parseInt($(this).find('.quantity').text());
                    const productName = $(this).find('.nome_prod').text();
                    const productDesc = $(this).find('.desc_prod').attr('title');
                    const price = parseFloat($(this).data('preco')) * quantity;

                    selectedProducts.push({
                        id: productId,
                        nome: productName,
                        descricao: productDesc,
                        quantidade: quantity,
                        valor_total: price
                    });
                });

                // Verifica se há produtos selecionados
                if (selectedProducts.length > 0) {
                    $.ajax({
                        url: '../controllers/adicionar_carrinho.php',
                        type: 'POST',
                        data: {
                            produtos: selectedProducts
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log('Resposta do servidor:', response);
                            const alertClass = response.status === 'success' ? 'alert-success' : 'alert-danger';
                            $('#messageContainer').html(`
                    <div class="alert ${alertClass}" role="alert">
                        ${response.message}
                    </div>
                `);
                        },
                        error: function(jqXHR) {
                            try {
                                const response = JSON.parse(jqXHR.responseText); // Tenta analisar a resposta como JSON
                                $('#messageContainer').html(`
                        <div class="alert alert-danger" role="alert">
                             ${response.message}
                        </div>
                    `); // Exibe a mensagem de erro
                            } catch (e) {
                                $('#messageContainer').html(`
                        <div class="alert alert-danger" role="alert">
                            <strong>Erro desconhecido. Por favor, tente novamente.</strong>
                        </div>
                    `); // Exibe mensagem padrão se não for JSON
                            }
                        }
                    });
                } else {
                    $('#messageContainer').html(`
            <div class="alert alert-warning" role="alert">
                <strong>Atenção!</strong> Nenhum produto selecionado para o carrinho.
            </div>
        `);
                }
            });



        });
    </script>

    <?php
    include '../module/footer.php';
    include '../module/navmobile.php';
    ?>

</body>

</html>
