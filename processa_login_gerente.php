<?php
// Inclua a conexão com o banco de dados
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome_gerente'];
    $cpf = $_POST['cpf_gerente'];
    $senha = $_POST['senha_gerente'];

    // Prepare e execute a consulta para verificar o gerente
    $query = $pdo->prepare("SELECT * FROM gerentes WHERE cpf = ? OR nome = ?");
    $query->execute([$cpf, $nome]);
    $gerente = $query->fetch(PDO::FETCH_ASSOC);

    if ($gerente && password_verify($senha, $gerente['senha'])) {
        // Login bem-sucedido, redirecione para a página de relatório
        header('Location: validacao_atendimento.php');
        exit();
    } else {
        // Login falhou, redirecione de volta com mensagem de erro
        header('Location: login.php?mensagem=Nome, CPF ou senha incorretos');
        exit();
    }
}
?>
