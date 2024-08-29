<?php
require 'conexao.php';

$nome_cpf = $_POST['nome_cpf'];

$query = $pdo->prepare("SELECT * FROM funcionarios WHERE nome = :nome_cpf OR cpf = :nome_cpf");
$query->execute(['nome_cpf' => $nome_cpf]);

if ($query->rowCount() > 0) {
    // Redirecionar para a tela de admin
    header("Location: admin.php");
} else {
    header("Location: login.php?message=Nome ou CPF não encontrado.");
}
?>
