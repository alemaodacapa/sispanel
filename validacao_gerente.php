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

    // Prepare e execute a consulta para verificar o gerente
    $query = $conn->prepare("SELECT * FROM gerentes WHERE cpf = ?");
    $query->bind_param('s', $cpf);
    $query->execute();
    $gerente = $query->get_result()->fetch_assoc();

    if ($gerente && password_verify($senha, $gerente['senha'])) {
        // Login bem-sucedido, redirecione para a página de validação
        header('Location: https://e-painel.x10.mx/validacao_atendimento.php');
        exit();
    } else {
        // Login falhou, redirecione de volta com mensagem de erro
        header('Location: https://e-painel.x10.mx/login.php?mensagem=Gerente não encontrado ou senha incorreta');
        exit();
    }
}

// Fechar a conexão
$conn->close();
?>
