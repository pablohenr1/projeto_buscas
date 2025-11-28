<?php
// index.php - LABORATÓRIO DE TESTES (Versão Final Completa)
include 'conexao.php';

// --- FUNÇÕES AUXILIARES (Simulação Hash) ---
$NUM_BUCKETS = 100; // Número de gavetas virtuais

function hash_numerico($valor, $buckets) {
    // Para ID e CPF: remove não-números e usa os últimos dígitos
    $nums = preg_replace('/\D/', '', $valor); 
    if ($nums == "") $nums = "0"; 
    $key = substr($nums, -6); // Pega até 6 dígitos
    return intval($key) % $buckets;
}

function hash_string($valor, $buckets) {
    // Para Nome e Cidade: transforma texto em número (CRC32)
    $str = strtoupper(trim($valor)); 
    $hash_val = crc32($str); 
    return abs($hash_val) % $buckets;
}

// --- FUNÇÃO INTELIGENTE: Verifica índices reais no MySQL ---
function verificar_se_tem_indice($conn, $tabela, $coluna) {
    // Pergunta ao banco se existe algum índice nesta coluna
    $sql = "SHOW INDEX FROM $tabela WHERE Column_name = '$coluna'";
    $result = $conn->query($sql);
    return ($result->num_rows > 0);
}

// Mensagem padrão inicial
$mensagem_resultado = "<p style='text-align:center; color:#666;'><strong>Laboratório de Performance:</strong><br>O sistema analisa a estrutura de dados e o tempo de resposta real (LIMIT 1).</p>";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['metodo_busca'])) {
    
    $metodo = $_POST['metodo_busca'];
    $coluna = $_POST['coluna_busca']; 
    $valor  = trim($_POST['valor_busca']);
    
    // Início Cronômetro
    $tempo_inicio = microtime(true);
    
    // Segurança: Valida colunas permitidas
    $colunas_permitidas = ['id', 'nome', 'cpf', 'cidade'];
    if (!in_array($coluna, $colunas_permitidas)) die("Coluna inválida.");

    // --- 1. DEFINIÇÃO DA QUERY SQL ---
    $sql = "";
    $tipo_param = "s"; // String por padrão

    if ($metodo == 'sequencial') {
        // Sequencial: Aceita busca parcial (LIKE)
        $sql = "SELECT * FROM pessoas WHERE $coluna LIKE ? LIMIT 1";
        $valor_query = "%" . $valor . "%";
    } else {
        // Indexada e Hash: Exigem busca exata (=)
        $sql = "SELECT * FROM pessoas WHERE $coluna = ? LIMIT 1";
        $valor_query = $valor;
        
        // Otimização para ID numérico
        if ($coluna == 'id' && is_numeric($valor)) $tipo_param = "i";
    }

    // Execução Segura
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($tipo_param, $valor_query);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fim Cronômetro
    $tempo_fim = microtime(true);
    $tempo_execucao = $tempo_fim - $tempo_inicio;
    
    // Visual do Tempo (Verde = Rápido < 0.002s)
    $cor_tempo = ($tempo_execucao < 0.002) ? "#10b981" : "#ef4444";
    $msg_timer = "<span style='color: $cor_tempo; font-weight:bold; font-size: 1.1em;'>(⏱️ " . number_format($tempo_execucao, 6) . "s)</span>";
    
    // --- 2. LÓGICA DE MENSAGENS EDUCATIVAS ---
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verifica se tem índice real no banco para dar o veredito
        $tem_indice = verificar_se_tem_indice($conn, 'pessoas', $coluna);
        
        // --- CENÁRIO: BUSCA INDEXADA ---
        if ($metodo == 'indexada') {
            if ($tem_indice) {
                // SUCESSO REAL (AZUL)
                $mensagem_resultado = "<div class='info-hash' style='background:#eff6ff; color:#1d4ed8; border-color:#93c5fd'>
                    <i class='fa-solid fa-sitemap'></i> <strong>B-Tree Ativada (Otimizada):</strong><br> 
                    O MySQL confirmou que <strong>existe um índice</strong> na coluna <u>{$coluna}</u>. A busca foi direta via árvore.<br>
                    <small>Complexidade: O(log n)</small>
                </div>";
            } else {
                // ALERTA DIDÁTICO (AMARELO)
                $mensagem_resultado = "<div class='info-hash' style='background:#fffbeb; color:#b45309; border-color:#fcd34d'>
                    <i class='fa-solid fa-triangle-exclamation'></i> <strong>Falso Índice (Table Scan):</strong><br> 
                    Você pediu 'Indexada', mas o sistema detectou que <strong>NÃO existe índice</strong> na coluna <u>{$coluna}</u>. O banco teve que ler tudo sequencialmente.
                </div>";
            }
        } 
        
        // --- CENÁRIO: BUSCA SEQUENCIAL ---
        elseif ($metodo == 'sequencial') {
             $mensagem_resultado = "<div class='info-hash' style='background:#fff7ed; color:#c2410c; border-color:#fdba74'>
                <i class='fa-solid fa-list-ol'></i> <strong>Full Table Scan:</strong><br>
                O banco percorreu a tabela procurando trechos que contenham '{$valor}'.<br>
                <small>Complexidade: O(n)</small>
            </div>";
        }
        
        // --- CENÁRIO: BUSCA HASH ---
        elseif ($metodo == 'hash') {
            // Calcula o bucket visualmente (funciona para qualquer coluna)
            $bucket = ($coluna == 'id' || $coluna == 'cpf') ? hash_numerico($valor, $NUM_BUCKETS) : hash_string($valor, $NUM_BUCKETS);
            
            if ($tem_indice) {
                // Hash Otimizado (Roxo)
                $mensagem_resultado = "<div class='info-hash' style='background:#fdf4ff; color:#86198f; border-color:#f0abfc'>
                    <i class='fa-solid fa-bolt'></i> <strong>Hash Lookup (Otimizado):</strong><br>
                    1. Fórmula: Mapeado para <strong>Bucket #{$bucket}</strong>.<br>
                    2. Banco: Índice encontrado na coluna <u>{$coluna}</u>. Acesso direto.
                </div>";
            } else {
                // Hash Simulado (Roxo Claro com aviso)
                $mensagem_resultado = "<div class='info-hash' style='background:#fdf4ff; color:#86198f; border-color:#f0abfc'>
                    <i class='fa-solid fa-calculator'></i> <strong>Hash Simulado:</strong><br>
                    Matematicamente o dado está no <strong>Bucket #{$bucket}</strong>.<br>
                    ⚠️ Como não há índice no banco, a recuperação física foi via Table Scan.
                </div>";
            }
        }

        // Exibição do Registro
        $mensagem_resultado .= "<p class='sucesso'>✅ Registro Encontrado. $msg_timer</p>";
        $mensagem_resultado .= "<div class='tabela-container'><table><thead><tr><th>ID</th><th>Nome</th><th>CPF</th><th>Cidade</th></tr></thead><tbody>";
        $mensagem_resultado .= "<tr><td>{$row['id']}</td><td>" . htmlspecialchars($row['nome']) . "</td><td>" . htmlspecialchars($row['cpf']) . "</td><td>" . htmlspecialchars($row['cidade']) . "</td></tr>";
        $mensagem_resultado .= "</tbody></table></div>";

    } else {
        // Não Encontrado
        $mensagem_resultado = "<div class='erro'>❌ Nenhum registro encontrado. $msg_timer</div>";
        if ($metodo != 'sequencial') $mensagem_resultado .= "<p style='text-align:center; font-size:12px; color:#666; margin-top:5px;'>Dica: Métodos Indexados e Hash exigem valor exato.</p>";
    }
    $stmt->close();
}
$conn->close();

// Carrega a interface visual
include 'view_index.php'; 
?>