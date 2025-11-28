<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratório de Performance</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <a href="index_original.php" style="text-decoration: none; color: #9ca3af; font-size: 12px; margin-bottom: 10px; display: block;">
                <i class="fa-solid fa-arrow-right"></i> Ir para Sistema Completo (Original)
            </a>
            
            <i class="fa-solid fa-flask" style="font-size: 40px; color: #9333ea; margin-bottom: 15px;"></i>
            <h1>Teste de Performance</h1>
            <p class="descricao">Modo Laboratório: O sistema irá PARAR no primeiro resultado encontrado (LIMIT 1).</p>
            
            <div style="text-align: center; margin-bottom: 20px;">
                <a href="exemplos.php" target="_blank" style="background-color: #e5e7eb; color: #374151; padding: 8px 15px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 600;">
                    <i class="fa-solid fa-list"></i> Ver Lista de CPFs Válidos
                </a>
            </div>
        </div>
        
        <form action="index.php" method="POST">
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>1. Método de Pesquisa</label>
                <div class="selecao-container">
                    <label><input type="radio" name="metodo_busca" value="indexada" class="radio-hidden" onclick="atualizarDica('indexada')"><div class="selecao-card"><i class="fa-solid fa-bolt"></i><span>Indexada</span></div></label>
                    <label><input type="radio" name="metodo_busca" value="sequencial" class="radio-hidden" onclick="atualizarDica('sequencial')" checked><div class="selecao-card"><i class="fa-solid fa-list-ul"></i><span>Sequencial</span></div></label>
                    <label><input type="radio" name="metodo_busca" value="hash" class="radio-hidden" onclick="atualizarDica('hash')"><div class="selecao-card"><i class="fa-solid fa-hashtag"></i><span>Hash</span></div></label>
                </div>
            </div>

            <div class="form-group" style="grid-column: 1 / -1;">
                <label>2. Coluna Alvo</label>
                <div class="selecao-container">
                    <label><input type="radio" name="coluna_busca" value="id" class="radio-hidden"><div class="selecao-card"><i class="fa-solid fa-fingerprint"></i><span>ID</span></div></label>
                    <label><input type="radio" name="coluna_busca" value="nome" class="radio-hidden" checked><div class="selecao-card"><i class="fa-solid fa-user"></i><span>Nome</span></div></label>
                    <label><input type="radio" name="coluna_busca" value="cidade" class="radio-hidden"><div class="selecao-card"><i class="fa-solid fa-city"></i><span>Cidade</span></div></label>
                    <label><input type="radio" name="coluna_busca" value="cpf" class="radio-hidden"><div class="selecao-card"><i class="fa-solid fa-id-card"></i><span>CPF</span></div></label>
                </div>
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>3. Valor</label>
                <input type="text" name="valor_busca" id="valor_busca" required placeholder="Digite o valor..." style="padding: 15px; font-size: 16px;">
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1; margin-top: 10px;">
                <button type="submit" style="width: 100%; padding: 15px; font-size: 18px; background-color: #9333ea;">
                    EXECUTAR TESTE <i class="fa-solid fa-vial"></i>
                </button>
            </div>
        </form>
        
        <div class="resultado-box">
            <?php echo $mensagem_resultado; ?>
        </div>
    </div>

    <script>
    // 1. Função que desenha os botões das colunas (Igual ao anterior)
    function atualizarColunas(metodo) {
        var container = document.getElementById("container-colunas");
        var inputValor = document.getElementById("valor_busca");
        
        // Limpa o valor antigo para evitar confusão
        inputValor.value = ""; 
        container.innerHTML = "";
        
        var colunas = [
            {valor: "id", texto: "ID (Código)", icone: "fa-fingerprint"},
            {valor: "nome", texto: "Nome", icone: "fa-user"},
            {valor: "cidade", texto: "Cidade", icone: "fa-city"},
            {valor: "cpf", texto: "CPF", icone: "fa-id-card"}
        ];

        if (metodo === "sequencial") {
            inputValor.placeholder = "Pode digitar parte do texto...";
        } else {
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
            
            // Foco automático e Limpeza de Máscara
            radio.onclick = function() {
                inputValor.value = ""; // Limpa campo
                inputValor.focus();
                
                // Muda o placeholder se for CPF
                if (col.valor === 'cpf') {
                    inputValor.placeholder = "000.000.000-00 (Apenas números)";
                    inputValor.maxLength = 14; // Limita tamanho
                } else {
                    inputValor.placeholder = "Digite o valor...";
                    inputValor.removeAttribute("maxLength"); // Remove limite
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

    // 2. NOVA FUNÇÃO: Aplica a Máscara de CPF enquanto digita
    var inputBusca = document.getElementById("valor_busca");

    inputBusca.addEventListener('input', function(e) {
        // Verifica qual coluna está marcada
        var colunaSelecionada = document.querySelector('input[name="coluna_busca"]:checked');
        
        // Se a coluna for CPF, aplica a máscara
        if (colunaSelecionada && colunaSelecionada.value === 'cpf') {
            var valor = e.target.value;
            
            // Remove tudo que não é dígito
            valor = valor.replace(/\D/g, ""); 
            
            // Aplica a formatação (Regex)
            valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
            valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
            valor = valor.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
            
            // Atualiza o valor no campo
            e.target.value = valor;
        }
    });
</script>
</body>
</html>