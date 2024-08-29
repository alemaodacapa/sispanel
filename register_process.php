<?php
require 'conexao.php';

$nome = $_POST['nome'];
$cpf = $_POST['cpf'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

try {
    $query = $pdo->prepare("INSERT INTO funcionarios (nome, cpf, senha) VALUES (:nome, :cpf, :senha)");
    $query->execute([
        'nome' => $nome,
        'cpf' => $cpf,
        'senha' => $senha
    ]);
    header("Location: login.php?message=Cadastro concluído com sucesso.");
} catch (PDOException $e) {
    header("Location: login.php?message=Erro ao cadastrar: " . $e->getMessage());
}
?>
