<?php
include 'conexao.php';

// Busca 15 registros aleatórios para servir de exemplo
$sql = "SELECT id, nome, cpf, cidade FROM pessoas ORDER BY RAND() LIMIT 15";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dados para Teste</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f3f4f6; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #374151; font-size: 22px; margin-bottom: 10px; text-align: center; }
        p { text-align: center; color: #666; margin-bottom: 30px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        th { background: #f9fafb; font-weight: 600; color: #6b7280; text-transform: uppercase; font-size: 12px; }
        tr:hover { background-color: #f8fafc; }
        
        .copy-btn {
            background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; 
            padding: 6px 12px; border-radius: 6px; cursor: pointer; 
            font-size: 12px; font-weight: bold; transition: all 0.2s;
        }
        .copy-btn:hover { background: #4338ca; color: white; }
        
        .voltar { display: block; text-align: center; margin-top: 30px; text-decoration: none; color: #2563eb; font-weight: 600; }
        .voltar:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Lista de Dados para Testes</h1>
        <p>Use estes dados para testar as buscas Exatas (Indexada e Hash).</p>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CPF (Clique para copiar)</th>
                    <th>Cidade</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><button class="copy-btn" onclick="copiar('<?php echo $row['id']; ?>')"><?php echo $row['id']; ?></button></td>
                    <td><?php echo $row['nome']; ?></td>
                    <td>
                        <span style="font-family: monospace; font-size: 14px;"><?php echo $row['cpf']; ?></span>
                        <button class="copy-btn" onclick="copiar('<?php echo $row['cpf']; ?>')"><i class="fa-regular fa-copy"></i> Copiar</button>
                    </td>
                    <td><?php echo $row['cidade']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <a href="javascript:window.close();" class="voltar">&times; Voltar para Pesquisas</a>
    </div>

    <script>
        function copiar(texto) {
            navigator.clipboard.writeText(texto).then(function() {
                // Feedback visual simples (alerta)
                alert('Copiado: ' + texto);
            });
        }
    </script>
</body>
</html>