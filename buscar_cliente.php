<?php
header('Content-Type: application/json');

// Definir informações de conexão com o banco de dados
$servidor = 'localhost';
$usuario = 'usuario';
$senha = 'Senha';
$bd = 'database';

// Criar conexão com o banco de dados
$conn = new mysqli($servidor, $usuario, $senha, $bd);

// Verificar se a conexão foi estabelecida corretamente
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Conexão não estabelecida: ' . $conn->connect_error]);
    exit();
}

try {
    // Obter o ID do cliente da solicitação
    $clienteId = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($clienteId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID do cliente inválido']);
        exit();
    }

    // Consulta para obter os dados do cliente
    $sql = "SELECT tipo_senha, senha FROM clientes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $clienteId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cliente = $result->fetch_assoc();
        echo json_encode(['status' => 'success', 'cliente' => $cliente]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Cliente não encontrado']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao consultar dados: ' . $e->getMessage()]);
}

// Fechar a conexão
$conn->close();
?>
