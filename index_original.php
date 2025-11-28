<?php
// index_original.php - SISTEMA COMPLETO (Visualização de Dados)
include 'conexao.php';

// --- Funções Auxiliares ---
$NUM_BUCKETS = 100;

function hash_numerico($valor, $buckets) {
    $nums = preg_replace('/\D/', '', $valor); 
    if ($nums == "") $nums = "0"; 
    $key = substr($nums, -6); 
    return intval($key) % $buckets;
}

function hash_string($valor, $buckets) {
    $str = strtoupper(trim($valor)); 
    $hash_val = crc32($str); 
    return abs($hash_val) % $buckets;
}

// Verifica se existe índice real no banco
function verificar_se_tem_indice($conn, $tabela, $coluna) {
    $sql = "SHOW INDEX FROM $tabela WHERE Column_name = '$coluna'";
    $result = $conn->query($sql);
    return ($result->num_rows > 0);
}

$mensagem_resultado = "<p style='text-align:center; color:#666;'><strong>Sistema de Buscas:</strong><br>Visualize até 10 registros e exporte para Excel.</p>";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['metodo_busca'])) {
    
    $metodo = $_POST['metodo_busca'];
    $coluna = $_POST['coluna_busca']; 
    $valor  = trim($_POST['valor_busca']);
    
    $tempo_inicio = microtime(true);
    
    $colunas_permitidas = ['id', 'nome', 'cpf', 'cidade'];
    if (!in_array($coluna, $colunas_permitidas)) die("Coluna inválida.");

    // --- QUERY (COM LIMIT 10) ---
    $sql = "";
    $tipo_param = "s"; 

    if ($metodo == 'sequencial') {
        $sql = "SELECT * FROM pessoas WHERE $coluna LIKE ? LIMIT 10";
        $valor_query = "%" . $valor . "%";
    } else {
        $sql = "SELECT * FROM pessoas WHERE $coluna = ? LIMIT 10";
        $valor_query = $valor;
        if ($coluna == 'id' && is_numeric($valor)) $tipo_param = "i";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($tipo_param, $valor_query);
    $stmt->execute();
    $result = $stmt->get_result();

    $tempo_fim = microtime(true);
    $tempo_execucao = $tempo_fim - $tempo_inicio;
    
    // Cores do Tempo (Critério um pouco mais leve pois traz mais dados)
    $cor_tempo = ($tempo_execucao < 0.005) ? "#10b981" : "#ef4444";
    $msg_timer = "<span style='color: $cor_tempo; font-weight:bold; font-size: 1.1em;'>(⏱️ " . number_format($tempo_execucao, 6) . "s)</span>";
    
    // --- MENSAGENS E FEEDBACK ---
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Pega o primeiro para checagem, depois reseta
        $result->data_seek(0); // Volta o ponteiro para o início para o loop da tabela
        
        $tem_indice = verificar_se_tem_indice($conn, 'pessoas', $coluna);
        
        // Mensagens Educativas (Mesma lógica do Lab)
        if ($metodo == 'indexada') {
            if ($tem_indice) {
                $mensagem_resultado = "<div class='info-hash' style='background:#eff6ff; color:#1d4ed8; border-color:#93c5fd'><i class='fa-solid fa-sitemap'></i> <strong>B-Tree Ativada:</strong> Índice confirmado. Busca Otimizada.</div>";
            } else {
                $mensagem_resultado = "<div class='info-hash' style='background:#fffbeb; color:#b45309; border-color:#fcd34d'><i class='fa-solid fa-triangle-exclamation'></i> <strong>Sem Índice:</strong> O sistema detectou falta de índice na coluna <u>{$coluna}</u>. Executado via Table Scan.</div>";
            }
        } elseif ($metodo == 'sequencial') {
             $mensagem_resultado = "<div class='info-hash' style='background:#fff7ed; color:#c2410c; border-color:#fdba74'><i class='fa-solid fa-list-ol'></i> <strong>Full Table Scan:</strong> Varredura parcial realizada.</div>";
        } elseif ($metodo == 'hash') {
            $bucket = ($coluna == 'id' || $coluna == 'cpf') ? hash_numerico($valor, $NUM_BUCKETS) : hash_string($valor, $NUM_BUCKETS);
            if ($tem_indice) {
                $mensagem_resultado = "<div class='info-hash' style='background:#fdf4ff; color:#86198f; border-color:#f0abfc'><i class='fa-solid fa-bolt'></i> <strong>Hash Lookup:</strong> Acesso direto via índice único (Bucket #{$bucket}).</div>";
            } else {
                 $mensagem_resultado = "<div class='info-hash' style='background:#fdf4ff; color:#86198f; border-color:#f0abfc'><i class='fa-solid fa-calculator'></i> <strong>Hash Simulado:</strong> Matematicamente no Bucket #{$bucket} (Sem índice real).</div>";
            }
        }

        $mensagem_resultado .= "<p class='sucesso'>✅ Exibindo <strong>{$result->num_rows}</strong> registro(s). $msg_timer</p>";
        
        // BOTÃO EXCEL (Exclusivo do Sistema Original)
        $link_exportar = "exportar.php?metodo=$metodo&coluna=$coluna&valor=" . urlencode($valor);
        $mensagem_resultado .= "<div style='margin: 10px 0; text-align: right;'><a href='$link_exportar' class='btn-excel'><i class='fa-solid fa-file-excel'></i> Baixar Excel Completo</a></div>";

        // TABELA
        $mensagem_resultado .= "<div class='tabela-container'><table><thead><tr><th>ID</th><th>Nome</th><th>CPF</th><th>Cidade</th></tr></thead><tbody>";
        while($row = $result->fetch_assoc()) {
            $mensagem_resultado .= "<tr><td>{$row['id']}</td><td>" . htmlspecialchars($row['nome']) . "</td><td>" . htmlspecialchars($row['cpf']) . "</td><td>" . htmlspecialchars($row['cidade']) . "</td></tr>";
        }
        $mensagem_resultado .= "</tbody></table></div>";

    } else {
        $mensagem_resultado = "<div class='erro'>❌ Nenhum registro encontrado. $msg_timer</div>";
    }
    $stmt->close();
}
$conn->close();

include 'view_index_original.php'; 
?>