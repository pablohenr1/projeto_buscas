<?php
// index.php - Tempo integrado na mensagem de sucesso
include 'conexao.php';

$NUM_BUCKETS = 10;

function hash_cpf($cpf, $buckets) {
    $num_cpf = preg_replace('/\D/', '', $cpf);
    $key = substr($num_cpf, -3);
    return intval($key) % $buckets;
}

function hash_cidade($cidade, $buckets) {
    $cidade_limpa = strtoupper(trim($cidade));
    $hash_val = crc32($cidade_limpa);
    return abs($hash_val) % $buckets;
}

$mensagem_resultado = "<p style='text-align:center; color:#666;'>Os resultados da pesquisa aparecerão aqui.</p>";
// Removemos a variável separada $mensagem_tempo, pois ela vai entrar no resultado.

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['metodo_busca'])) {
    
    $metodo = $_POST['metodo_busca'];
    $coluna = $_POST['coluna_busca']; 
    $valor  = $_POST['valor_busca'];
    
    // --- INICIO CRONÔMETRO ---
    $tempo_inicio = microtime(true);
    
    $colunas_permitidas = ['id', 'nome', 'cpf', 'cidade'];
    if (!in_array($coluna, $colunas_permitidas)) die("Coluna inválida.");

    // 1. QUERY TOTAL (Contagem)
    $sql_count = "";
    if ($metodo == 'sequencial') {
        $sql_count = "SELECT COUNT(*) as total FROM pessoas WHERE $coluna LIKE ?";
        $valor_sql = "%" . $valor . "%";
    } else {
        $sql_count = "SELECT COUNT(*) as total FROM pessoas WHERE $coluna = ?";
        $valor_sql = $valor;
    }
    
    $stmt_count = $conn->prepare($sql_count);
    if ($metodo == 'indexada' && $coluna == 'id') {
        $stmt_count->bind_param("i", $valor_sql);
    } else {
        $stmt_count->bind_param("s", $valor_sql);
    }
    $stmt_count->execute();
    $res_count = $stmt_count->get_result()->fetch_assoc();
    $total_encontrado = $res_count['total'];
    $stmt_count->close();

    // 2. QUERY DADOS (Limit 10)
    $sql = "";
    if ($metodo == 'sequencial') {
        $sql = "SELECT * FROM pessoas WHERE $coluna LIKE ? LIMIT 10";
    } else {
        $sql = "SELECT * FROM pessoas WHERE $coluna = ? LIMIT 10";
    }

    $stmt = $conn->prepare($sql);
    if ($metodo == 'indexada' && $coluna == 'id') {
        $stmt->bind_param("i", $valor_sql);
    } else {
        $stmt->bind_param("s", $valor_sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    // --- FIM CRONÔMETRO (Calculamos AGORA para usar no texto abaixo) ---
    $tempo_fim = microtime(true);
    $tempo_execucao = $tempo_fim - $tempo_inicio;
    $cor_tempo = ($tempo_execucao < 0.005) ? "green" : "#c0392b"; // Verde ou Vermelho escuro
    
    // Monta a string do tempo bonitinha
    $msg_timer = "<span style='color: $cor_tempo; font-size: 0.9em; margin-left: 10px;'> (⏱️ " . number_format($tempo_execucao, 6) . "s)</span>";
    
    
    // --- VISUALIZAÇÃO ---
    $info_extra = "";
    if ($metodo == 'hash') {
        $bucket = ($coluna == 'cpf') ? hash_cpf($valor, $NUM_BUCKETS) : hash_cidade($valor, $NUM_BUCKETS);
        $info_extra = "<div class='info-hash'>🔑 Bucket HASH: <strong>#{$bucket}</strong></div>";
    }

    if ($result->num_rows > 0) {
        $mensagem_resultado = $info_extra;
        
        // AQUI ESTÁ A MUDANÇA: Adicionamos $msg_timer dentro da mensagem de sucesso
        $mensagem_resultado .= "<p class='sucesso'>✅ Encontrados <strong>{$total_encontrado}</strong> registros. $msg_timer</p>";

        if ($total_encontrado > 10) {
            $mensagem_resultado .= "<p style='font-size:12px; color:#e67e22; margin-top: 7px;'>⚠️ Exibindo apenas os <strong>10 primeiros</strong>.</p>";
        }

        $link_exportar = "exportar.php?metodo=$metodo&coluna=$coluna&valor=" . urlencode($valor);
        $mensagem_resultado .= "<div style='margin: 10px 0; text-align: right;'><a href='$link_exportar' class='btn-excel'>📥 Exportar Excel</a></div>";

        $mensagem_resultado .= "<div class='tabela-container'>
                                <table>
                                    <thead>
                                        <tr><th>ID</th><th>Nome</th><th>CPF</th><th>Cidade</th></tr>
                                    </thead>
                                    <tbody>";
        
        while($row = $result->fetch_assoc()) {
            $mensagem_resultado .= "<tr>
                                        <td>{$row['id']}</td>
                                        <td>" . htmlspecialchars($row['nome']) . "</td>
                                        <td>" . htmlspecialchars($row['cpf']) . "</td>
                                        <td>" . htmlspecialchars($row['cidade']) . "</td>
                                    </tr>";
        }
        $mensagem_resultado .= "</tbody></table></div>";
        
    } else {
        // Adiciona o tempo também na mensagem de erro (0 encontrados)
        $mensagem_resultado = "<div class='erro'>❌ Nenhum registro encontrado. $msg_timer</div>";
    }
    $stmt->close();
}

$conn->close();
include 'view_index.php';
?>