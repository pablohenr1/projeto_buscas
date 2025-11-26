<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Search Engine</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <div style="text-align: center; margin-bottom: 30px;">
            <i class="fa-solid fa-database" style="font-size: 40px; color: var(--primary-color); margin-bottom: 15px;"></i>
            <h1>Pesquisa de Dados</h1>

            <button type="submit">
                <a href="home.php" style="text-decoration: none; color: #ffffff; font-size: 14px; margin-bottom: 20px; display: inline-block;">
                    <i class="fa-solid fa-arrow-left"></i> Voltar para Home
                </a>
            </button>

            <p class="descricao">Selecione os parâmetros abaixo para filtrar a base de registros.</p>
        </div>
        
        <form action="index.php" method="POST">
            
            <div class="form-group">
                <label><i class="fa-solid fa-filter"></i> 1. Método</label>
                <select name="metodo_busca" id="metodo_busca" required onchange="atualizarColunas()">
                    <option value="">Selecione...</option>
                    <option value="indexada">⚡ Indexada (Rápida)</option>
                    <option value="sequencial">🔍 Sequencial (Varredura)</option>
                    <option value="hash">🔑 Hash (Mapeamento)</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-table-columns"></i> 2. Coluna</label>
                <select name="coluna_busca" id="coluna_busca" required>
                    <option value="">-- Aguardando --</option>
                </select>
            </div>
            
            <div class="form-group">
                <label><i class="fa-solid fa-magnifying-glass"></i> 3. Valor</label>
                <input type="text" name="valor_busca" id="valor_busca" required placeholder="Digite aqui...">
            </div>
            
            <button type="submit">
                PESQUISAR
            </button>
        </form>
        
        <div class="resultado-box">
            <?php echo $mensagem_resultado; ?>
        </div>
    </div>

    <script>
        function atualizarColunas() {
            var metodo = document.getElementById("metodo_busca").value;
            var colunaSelect = document.getElementById("coluna_busca");
            var inputValor = document.getElementById("valor_busca");
            
            colunaSelect.innerHTML = "";
            var opcoes = [];

            if (metodo === "indexada") {
                opcoes = [{valor: "id", texto: "ID (Primary Key)"}];
                inputValor.placeholder = "Ex: 3500";
            } 
            else if (metodo === "sequencial") {
                opcoes = [
                    {valor: "nome", texto: "Nome"},
                    {valor: "cidade", texto: "Cidade"},
                    {valor: "cpf", texto: "CPF"},
                    {valor: "id", texto: "ID"}
                ];
                inputValor.placeholder = "Ex: São Paulo";
            } 
            else if (metodo === "hash") {
                opcoes = [
                    {valor: "cpf", texto: "CPF"},
                    {valor: "cidade", texto: "Cidade"}
                ];
                inputValor.placeholder = "Ex: 000.000.000-00";
            } 
            else {
                opcoes = [{valor: "", texto: "-- Aguardando Método --"}];
                inputValor.placeholder = "Digite o termo...";
            }

            for (var i = 0; i < opcoes.length; i++) {
                var opt = document.createElement('option');
                opt.value = opcoes[i].valor;
                opt.innerHTML = opcoes[i].texto;
                colunaSelect.appendChild(opt);
            }
        }
    </script>

</body>
</html>