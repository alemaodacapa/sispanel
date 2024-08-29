
<?php
// Inclua o arquivo de conexão com o banco de dados
include 'conexao.php';

// Recebe os dados do formulário
$nome = $_POST['nome'];
$cpf = $_POST['cpf'];
$tipo_senha = $_POST['tipo_senha'];
$senha = $_POST['senha'];

// Determina o consultório com base no tipo de senha
$consultorio = ($tipo_senha === 'normal') ? 1 : 2;

try {
    // Insere os dados na tabela 'clientes'
    $stmt = $pdo->prepare("INSERT INTO clientes (nome, cpf, tipo_senha, senha, consultorio) VALUES (:nome, :cpf, :tipo_senha, :senha, :consultorio)");
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':tipo_senha', $tipo_senha);
    $stmt->bindParam(':senha', $senha);
    $stmt->bindParam(':consultorio', $consultorio);
    $stmt->execute();

    // Retorna uma mensagem de sucesso
    echo json_encode(['status' => 'success', 'message' => 'Cadastro realizado com sucesso!']);
} catch (PDOException $e) {
    // Retorna uma mensagem de erro
    echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar o cliente: ' . $e->getMessage()]);
}
?>
