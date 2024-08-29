<?php
$host = 'localhost';
$dbname = 'database';
$username = 'usuario';
$password = 'senha';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recebe os dados do formulário
    $nome = $_POST['nome_gerente'];
    $cpf = $_POST['cpf_gerente'];
    $senha = $_POST['senha_gerente'];

    // Insere os dados na tabela 'gerentes'
    $stmt = $pdo->prepare("INSERT INTO gerentes (nome, cpf, senha) VALUES (:nome, :cpf, :senha)");
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':senha', password_hash($senha, PASSWORD_DEFAULT)); // Armazenar senha criptografada
    $stmt->execute();

    // Redireciona para login.php com mensagem de sucesso
    header("Location: login.php?msg=cadastro_gerente_sucesso");
    exit;
} catch (PDOException $e) {
    // Redireciona para login.php com mensagem de erro
    header("Location: login.php?msg=erro_cadastro_gerente");
    exit;
}
?>
