<?php
// exportar.php
include 'conexao.php';

// Verifica se recebeu os parâmetros (via GET para facilitar o link)
if (isset($_GET['metodo']) && isset($_GET['coluna']) && isset($_GET['valor'])) {
    
    $metodo = $_GET['metodo'];
    $coluna = $_GET['coluna'];
    $valor  = $_GET['valor'];

    // Proteção de coluna
    $colunas_permitidas = ['id', 'nome', 'cpf', 'cidade'];
    if (!in_array($coluna, $colunas_permitidas)) die("Coluna inválida");

    // Monta a Query (SEM O LIMIT, para baixar tudo o que foi achado)
    $sql = "";
    if ($metodo == 'sequencial') {
        $valor_sql = "%" . $valor . "%";
        $sql = "SELECT id, nome, cpf, cidade FROM pessoas WHERE $coluna LIKE ?";
    } else {
        $valor_sql = $valor;
        $sql = "SELECT id, nome, cpf, cidade FROM pessoas WHERE $coluna = ?";
    }

    $stmt = $conn->prepare($sql);
    
    // Bind
    if ($metodo == 'indexada' && $coluna == 'id') {
        $stmt->bind_param("i", $valor_sql);
    } else {
        $stmt->bind_param("s", $valor_sql);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // --- CONFIGURAÇÃO DO DOWNLOAD CSV (EXCEL) ---
    $filename = "relatorio_buscas_" . date('Ymd') . ".csv";
    
    // Avisa o navegador que é um arquivo para download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    // Abre a "saída" do arquivo
    $output = fopen('php://output', 'w');

    // Adiciona BOM para o Excel reconhecer acentos (UTF-8) corretamente
    fputs($output, "\xEF\xBB\xBF");

    // 1. Escreve o Cabeçalho das Colunas
    fputcsv($output, array('ID', 'Nome', 'CPF', 'Cidade'), ';');

    // 2. Escreve as Linhas do Banco
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row, ';'); // O ponto e vírgula (;) é o padrão do Excel no Brasil
    }

    fclose($output);
    exit(); // Encerra o script para não imprimir mais nada
}
?>