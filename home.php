<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estruturas de Dados - Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container" style="background: transparent; box-shadow: none; max-width: 1200px;">
        
        <div class="hero-section">
            <h1 class="hero-title">Performance em Algoritmos de Busca</h1>
            <p class="hero-subtitle">Um laboratório prático demonstrando como <strong>Indexação (B-Tree)</strong>, <strong>Varredura (Scan)</strong> e <strong>Hashing</strong> afetam a velocidade de recuperação de dados em grandes volumes.</p>
        </div>

        <div class="grid-conceitos">
            
            <div class="card-conceito border-blue">
                <div class="icon-box bg-blue-light">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <h3 class="card-title">1. Busca Indexada (B-Tree)</h3>
                <p class="card-text">
                    O banco utiliza uma estrutura de árvore para "saltar" diretamente ao registro, sem ler a tabela toda.
                    <br><br>
                    <strong>Comportamento:</strong> Requer que a coluna tenha um índice criado previamente. É o padrão de mercado para buscas precisas.
                </p>
                <div class="card-footer">
                    <strong>🏆 Melhor para:</strong><br>
                    <span class="badge b-blue">ID (PK)</span>
                    <span class="badge b-blue">Nome Completo</span>
                    <span class="badge b-blue">Cidade</span>
                </div>
            </div>

            <div class="card-conceito border-orange">
                <div class="icon-box bg-orange-light">
                    <i class="fa-solid fa-list-ol"></i>
                </div>
                <h3 class="card-title">2. Busca Sequencial (Scan)</h3>
                <p class="card-text">
                    O banco lê linha por linha. É o único método capaz de encontrar "pedaços" de texto (flexibilidade), mas sofre com lentidão em grandes volumes.
                    <br><br>
                    <strong>Comportamento:</strong> O tempo varia (Sorte): achar o primeiro registro é rápido, o último é lento.
                </p>
                <div class="card-footer">
                    <strong>🏆 Melhor para:</strong><br>
                    <span class="badge b-orange">Parte do Nome</span>
                    <span class="badge b-orange">Parte da Cidade</span>
                    <span class="badge b-orange">Filtros Complexos</span>
                </div>
            </div>

            <div class="card-conceito border-purple">
                <div class="icon-box bg-purple-light">
                    <i class="fa-solid fa-hashtag"></i>
                </div>
                <h3 class="card-title">3. Busca Hash (Mapeamento)</h3>
                <p class="card-text">
                    Usa matemática para calcular o endereço de memória do dado. É o método mais rápido teoricamente (O(1)), mas exige chaves únicas e exatas.
                    <br><br>
                    <strong>Comportamento:</strong> Não aceita busca parcial. Se errar uma letra, o cálculo muda e não encontra nada.
                </p>
                <div class="card-footer">
                    <strong>🏆 Melhor para:</strong><br>
                    <span class="badge b-purple">CPF (Único)</span>
                    <span class="badge b-purple">ID (Único)</span>
                    <span class="badge b-purple">E-mail</span>
                </div>
            </div>

        </div>

        <div style="text-align: center; display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
            
            <a href="index.php" class="btn-cta" style="background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%); box-shadow: 0 10px 20px rgba(147, 51, 234, 0.3);">
                <i class="fa-solid fa-flask"></i> ABRIR LABORATÓRIO (TESTE)
            </a>

            <a href="index_original.php" class="btn-cta">
                 ABRIR SISTEMA COMPLETO <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>

    </div>

</body>
</html>