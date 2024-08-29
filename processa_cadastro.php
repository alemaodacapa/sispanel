<?php
// Configurações do banco de dados
$host = 'localhost';
$dbname = 'database';
$username = 'usuario';
$password = 'Senha';

try {
    // Conecta ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nome = $_POST['nome'];
        $cpf = $_POST['cpf'];
        $senha = $_POST['senha'];

        // Verifica se o CPF já está cadastrado
        $query = $pdo->prepare("SELECT COUNT(*) FROM funcionarios WHERE cpf = ?");
        $query->execute([$cpf]);
        $count = $query->fetchColumn();

        if ($count > 0) {
            // Se o CPF já estiver cadastrado, redireciona com uma mensagem de erro
            header("Location: login.php?mensagem=CPF já cadastrado. Faça login ou use um CPF diferente.");
            exit;
        }

        // Prepara e executa a consulta para inserir o funcionário
        $query = $pdo->prepare("INSERT INTO funcionarios (nome, cpf, senha) VALUES (?, ?, ?)");
        $query->execute([$nome, $cpf, password_hash($senha, PASSWORD_DEFAULT)]);

        // Redireciona após o cadastro com uma mensagem de sucesso
        header("Location: login.php?mensagem=Cadastro concluído com sucesso, faça login com seus dados cadastrados");
        exit();
    }
} catch (PDOException $e) {
    // Em caso de erro na conexão ou na operação, redireciona com uma mensagem de erro
    header("Location: login.php?mensagem=Erro ao cadastrar. Tente novamente.");
    exit;
}
?>
