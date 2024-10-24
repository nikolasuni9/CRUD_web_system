<?php
// Dados de conexão (substitua pelos seus dados)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "escola";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
  die("Conexão com banco de dados falhou: " . $conn->connect_error);
}

// Função para fechar a conexão (opcional, mas recomendada)
function fecharConexao() {
    global $conn;
    $conn->close();
}
?>