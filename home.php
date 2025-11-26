<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Algoritmos de Busca - Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container" style="background: transparent; box-shadow: none; max-width: 1100px;">
        
        <div class="hero-section">
            <h1 class="hero-title">Estruturas de Dados & Algoritmos</h1>
            <p class="hero-subtitle">Uma demonstração interativa comparando performance e lógica dos três principais métodos de recuperação de informação em Bancos de Dados.</p>
            

        </div>

        <div class="grid-conceitos">
            
            <div class="card-conceito border-blue">
                <div class="icon-box bg-blue-light">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <h3 class="card-title">1. Busca Indexada</h3>
                <p class="card-text">
                    Utiliza uma estrutura de árvore (B-Tree) na chave primária. O banco "pula" diretamente para o endereço de memória do registro.
                    <br><br>
                    <strong>Complexidade:</strong> O(log n) - Extremamente Rápida.
                    <br>
                    <strong>Exemplo:</strong> Buscar pelo ID.
                </p>
            </div>

            <div class="card-conceito border-orange">
                <div class="icon-box bg-orange-light">
                    <i class="fa-solid fa-list-ol"></i>
                </div>
                <h3 class="card-title">2. Busca Sequencial</h3>
                <p class="card-text">
                    Sem índices, o banco precisa ler a tabela inteira (Table Scan), linha por linha, até encontrar o que foi pedido.
                    <br><br>
                    <strong>Complexidade:</strong> O(n) - Lenta em grandes volumes.
                    <br>
                    <strong>Exemplo:</strong> Buscar por Nome ou Cidade.
                </p>
            </div>

            <div class="card-conceito border-purple">
                <div class="icon-box bg-purple-light">
                    <i class="fa-solid fa-hashtag"></i>
                </div>
                <h3 class="card-title">3. Busca Hash</h3>
                <p class="card-text">
                    Transforma o valor da busca (ex: CPF) em um número (Bucket) através de cálculo matemático, acessando a posição diretamente.
                    <br><br>
                    <strong>Complexidade:</strong> O(1) - Instantânea (idealmente).
                    <br>
                    <strong>Exemplo:</strong> Buscar por CPF (Simulado).
                </p>
            </div>

        </div>

        <div style="text-align: center;">
            <a href="index.php" class="btn-cta">
                <i class="fa-solid fa-rocket"></i> INICIAR SISTEMA
            </a>
        </div>

    </div>

</body>
</html>