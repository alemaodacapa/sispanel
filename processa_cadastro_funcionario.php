<?php
// Definir informações de conexão com o banco de dados
$servidor = 'localhost';
$usuario = 'usuario';
$senha = 'senha';
$bd = 'database';

// Criar conexão com o banco de dados
$conn = new mysqli($servidor, $usuario, $senha, $bd);

// Verificar se a conexão foi estabelecida corretamente
if ($conn->connect_error) {
    die('Conexão não estabelecida: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $senha = $_POST['senha'];

    // Verificar se o CPF já está cadastrado
    $query = $conn->prepare("SELECT * FROM funcionarios WHERE cpf = ?");
    $query->bind_param('s', $cpf);
    $query->execute();
    $funcionario = $query->get_result()->fetch_assoc();

    if ($funcionario) {
        // CPF já cadastrado
        header('Location: login.php?mensagem_funcionario=CPF já cadastrado. Faça login.');
        exit();
    } else {
        // Cadastrar novo funcionário
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $query = $conn->prepare("INSERT INTO funcionarios (nome, cpf, senha) VALUES (?, ?, ?)");
        $query->bind_param('sss', $nome, $cpf, $senha_hash);
        $query->execute();

        if ($query->affected_rows > 0) {
            // Cadastro bem-sucedido
            header('Location: login.php?mensagem_funcionario=Cadastro concluído com sucesso. Faça login.');
            exit();
        } else {
            // Falha no cadastro
            header('Location: login.php?mensagem_funcionario=Erro ao cadastrar. Tente novamente.');
            exit();
        }
    }
}

// Fechar a conexão
$conn->close();
?>
