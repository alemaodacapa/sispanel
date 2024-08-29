<?php
include 'conexao.php';

// Testando a conexão
try {
    $stmt = $pdo->query("SELECT VERSION()");
    $version = $stmt->fetchColumn();
    echo "Versão do MySQL: " . $version;
} catch (PDOException $e) {
    echo "Erro ao buscar a versão: " . $e->getMessage();
}
?>
