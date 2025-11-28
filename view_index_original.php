<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Buscas Completo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <a href="home.php" style="text-decoration: none; color: #9ca3af; font-size: 12px; margin-bottom: 10px; display: block;">
                <i class="fa-solid fa-arrow-left"></i> Voltar para Home
            </a>
            
            <i class="fa-solid fa-database" style="font-size: 40px; color: #2563eb; margin-bottom: 15px;"></i>
            <h1>Sistema de Buscas</h1>
            <p class="descricao">Modo Completo: Visualização de até 10 registros e exportação de dados.</p>
        </div>
        
        <form action="index_original.php" method="POST">
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>1. Método de Pesquisa</label>
                <div class="selecao-container">
                    <label><input type="radio" name="metodo_busca" value="indexada" class="radio-hidden" onclick="atualizarColunas('indexada')"><div class="selecao-card"><i class="fa-solid fa-bolt"></i><span>Indexada</span></div></label>
                    <label><input type="radio" name="metodo_busca" value="sequencial" class="radio-hidden" onclick="atualizarColunas('sequencial')" checked><div class="selecao-card"><i class="fa-solid fa-list-ul"></i><span>Sequencial</span></div></label>
                    <label><input type="radio" name="metodo_busca" value="hash" class="radio-hidden" onclick="atualizarColunas('hash')"><div class="selecao-card"><i class="fa-solid fa-hashtag"></i><span>Hash</span></div></label>
                </div>
            </div>

            <div class="form-group" style="grid-column: 1 / -1;">
                <label>2. Coluna Alvo</label>
                <div id="container-colunas" class="selecao-container">
                    </div>
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>3. Valor</label>
                <input type="text" name="valor_busca" id="valor_busca" required placeholder="Digite o valor..." style="padding: 15px; font-size: 16px;">
                
                <div id="area-sugestoes" style="margin-top: 15px; display: none; background: #f0f9ff; padding: 15px; border-radius: 8px; border: 1px dashed #bae6fd;">
                    <small style="color: #0284c7; font-weight:bold; display:block; margin-bottom:8px;">
                        <i class="fa-solid fa-lightbulb"></i> Sugestões do Banco:
                    </small>
                    <div id="lista-cpfs">Carregando...</div>
                </div>
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1; margin-top: 10px;">
                <button type="submit" style="width: 100%; padding: 15px; font-size: 18px;">
                    PESQUISAR DADOS <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>
        
        <div class="resultado-box">
            <?php echo $mensagem_resultado; ?>
        </div>
    </div>

    <script>
        window.onload = function() { atualizarColunas('sequencial'); };

        function atualizarColunas(metodo) {
            var container = document.getElementById("container-colunas");
            var inputValor = document.getElementById("valor_busca");
            var areaSugestoes = document.getElementById("area-sugestoes");
            
            inputValor.value = ""; 
            container.innerHTML = "";
            areaSugestoes.style.display = "none"; 
            
            var colunas = [];
            if (metodo === "indexada") {
                colunas = [{valor: "id", texto: "ID (Código)", icone: "fa-fingerprint"}];
                inputValor.placeholder = "Digite o ID exato...";
            } else if (metodo === "sequencial") {
                colunas = [
                    {valor: "nome", texto: "Nome", icone: "fa-user"},
                    {valor: "cidade", texto: "Cidade", icone: "fa-city"},
                    {valor: "cpf", texto: "CPF", icone: "fa-id-card"},
                    {valor: "id", texto: "ID", icone: "fa-fingerprint"}
                ];
                inputValor.placeholder = "Pode digitar parte do texto...";
            } else if (metodo === "hash") {
                colunas = [
                    {valor: "cpf", texto: "CPF", icone: "fa-id-card"},
                    {valor: "cidade", texto: "Cidade", icone: "fa-city"}
                ];
                inputValor.placeholder = "Digite o valor EXATO...";
            }

            colunas.forEach(function(col) {
                var label = document.createElement("label");
                var radio = document.createElement("input");
                radio.type = "radio";
                radio.name = "coluna_busca";
                radio.value = col.valor;
                radio.className = "radio-hidden";
                radio.required = true;
                
                radio.onclick = function() {
                    inputValor.value = "";
                    inputValor.focus();
                    if (col.valor === 'cpf') {
                        inputValor.placeholder = "000.000.000-00";
                        areaSugestoes.style.display = "block";
                        carregarCpfs();
                    } else {
                        inputValor.placeholder = "Digite o valor...";
                        areaSugestoes.style.display = "none";
                    }
                };

                var card = document.createElement("div");
                card.className = "selecao-card";
                card.innerHTML = '<i class="fa-solid '+ col.icone +'"></i><span>' + col.texto + '</span>';

                label.appendChild(radio);
                label.appendChild(card);
                container.appendChild(label);
            });
        }

        function carregarCpfs() {
            var lista = document.getElementById("lista-cpfs");
            lista.innerHTML = "Buscando...";

            fetch('get_cpfs.php')
                .then(response => response.json())
                .then(data => {
                    lista.innerHTML = "";
                    data.forEach(cpf => {
                        var btn = document.createElement("span");
                        btn.innerText = cpf;
                        // Estilo Azul para o Sistema Original
                        btn.style.cssText = "background:white; color:#0284c7; padding:6px 12px; border-radius:20px; font-size:13px; margin-right:8px; cursor:pointer; border:1px solid #bae6fd; display:inline-block; margin-bottom:5px; font-weight:bold;";
                        
                        btn.onclick = function() {
                            var input = document.getElementById("valor_busca");
                            input.value = cpf;
                            input.style.backgroundColor = "#f0f9ff";
                            setTimeout(() => input.style.backgroundColor = "white", 300);
                        };
                        lista.appendChild(btn);
                    });
                });
        }

        var inputBusca = document.getElementById("valor_busca");
        inputBusca.addEventListener('input', function(e) {
            var colunaSelecionada = document.querySelector('input[name="coluna_busca"]:checked');
            if (colunaSelecionada && colunaSelecionada.value === 'cpf') {
                var valor = e.target.value.replace(/\D/g, "");
                valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
                valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
                valor = valor.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
                e.target.value = valor;
            }
        });
    </script>
</body>
</html>