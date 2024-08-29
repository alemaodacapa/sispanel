
<?php
// Inclua a conexão com o banco de dados
include('conexao.php'); // Certifique-se de que este arquivo contém a configuração de conexão PDO

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cpf = $_POST['usuario']; // Altere para 'usuario' se os campos do formulário estiverem assim
    $senha = $_POST['senha'];

    // Prepare e execute a consulta para verificar o funcionário
    $query = $pdo->prepare("SELECT * FROM funcionarios WHERE cpf = ? OR nome = ?");
    $query->execute([$cpf, $cpf]);
    $funcionario = $query->fetch(PDO::FETCH_ASSOC);

    if ($funcionario && password_verify($senha, $funcionario['senha'])) {
        // Login bem-sucedido, redirecione para a página de registro
        header('Location: registro.php'); // Atualizado para redirecionar para a página do operador
        exit();
    } else {
        // Login falhou, redirecione de volta com mensagem de erro
        header('Location: login.php?msg=erro_login'); // Mensagem de erro ajustada para o padrão definido
        exit();
    }
}
?>
