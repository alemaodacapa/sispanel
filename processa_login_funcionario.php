<?php
// Iniciar a sessão
session_start();

// Definir informações de conexão com o banco de dados
$servidor = 'localhost'; // Altere para o servidor de banco de dados
$usuario = 'usuario'; // Altere para o nome de usuário do banco de dados
$senha = 'senha'; // Altere para a senha do banco de dados
$bd = 'database'; // Altere para o nome do banco de dados

// Criar conexão com o banco de dados
$conn = new mysqli($servidor, $usuario, $senha, $bd);

// Verificar se a conexão foi estabelecida corretamente
if ($conn->connect_error) {
    die('Conexão não estabelecida: ' . $conn->connect_error);
}

// Receber dados do formulário de login
$usuario = mysqli_real_escape_string($conn, $_POST['usuario']);
$senha = mysqli_real_escape_string($conn, $_POST['senha']);

// Consulta SQL para verificar se o funcionário existe
$sql = "SELECT * FROM funcionarios WHERE cpf = '$usuario' AND senha = '$senha'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Se existir, salvar os dados na sessão e redirecionar para registro.php
    $_SESSION['usuario'] = $usuario;
    header("Location: registro.php");
    exit();
} else {
    // Se não existir, redirecionar de volta para o login com mensagem de erro
    header("Location: login.php?mensagem=Usuário ou senha incorretos.");
    exit();
}

// Fechar a conexão
$conn->close();
?>
