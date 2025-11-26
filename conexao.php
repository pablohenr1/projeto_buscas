<?php
// --- 1. CONFIGURAÇÃO DA CONEXÃO ---
// Endereço do servidor
$servername = "localhost"; 
// Seu nome de usuário do MySQL
$username = "root";        
// Sua senha do MySQL (padrão é vazia no XAMPP)
$password = "";            
// Nome do seu banco de dados
$dbname = "projeto_buscas"; 

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Checa a conexão e para o script se houver erro
if ($conn->connect_error) {
    die("❌ Falha na conexão com o banco de dados: " . $conn->connect_error);
}
?>