<?php
// Configurações de conexão com o banco de dados
$host = 'localhost';
$dbname = 'database';
$username = 'usuario';
$password = 'senha';

// Cria uma nova conexão PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Define o modo de erro para exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Exibe uma mensagem de erro se a conexão falhar
    die("Erro de conexão: " . $e->getMessage());
}
?>
