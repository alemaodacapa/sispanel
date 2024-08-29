<?php
header('Content-Type: application/json');

// Definir informações de conexão com o banco de dados
$servidor = 'localhost'; 
$usuario = 'usuario'; 
$senha = 'senha'; 
$bd = 'database'; 

// Criar conexão com o banco de dados
$conn = new mysqli($servidor, $usuario, $senha, $bd);

// Verificar se a conexão foi estabelecida corretamente
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Conexão não estabelecida: ' . $conn->connect_error]);
    exit();
}

// Obter os dados enviados pelo POST
$clienteId = isset($_POST['clienteId']) ? $_POST['clienteId'] : null;
$tipo_senha = isset($_POST['tipo_senha']) ? $_POST['tipo_senha'] : null; // 'Normal' ou 'Preferencial'
$senha = isset($_POST['senha']) ? $_POST['senha'] : null;

// Verificar se os dados obrigatórios foram fornecidos
if ($clienteId && $tipo_senha && $senha) {
    // Buscar informações do cliente na tabela clientes
    $sql_cliente = "SELECT nome, cpf FROM clientes WHERE id = ?";
    $stmt_cliente = $conn->prepare($sql_cliente);
    
    if ($stmt_cliente) {
        $stmt_cliente->bind_param("i", $clienteId);
        $stmt_cliente->execute();
        $result_cliente = $stmt_cliente->get_result();

        if ($result_cliente->num_rows > 0) {
            // Obter os dados do cliente
            $cliente = $result_cliente->fetch_assoc();
            
            // Determinar o valor do consultório com base no tipo de senha
            $consultorio = ($tipo_senha === 'Normal') ? 1 : 2;

            // Inserir os dados na tabela atendimentos
            $sql_insert = "INSERT INTO atendimentos (senha, cpf, consultorio, tipo_atendimento) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            
            if ($stmt_insert) {
                $stmt_insert->bind_param("ssis", $senha, $cliente['cpf'], $consultorio, $tipo_senha);
                if ($stmt_insert->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Atendimento validado com sucesso!']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Erro ao validar o atendimento: ' . $stmt_insert->error]);
                }
                $stmt_insert->close();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao preparar a inserção: ' . $conn->error]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Cliente não encontrado.']);
        }
        $stmt_cliente->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao preparar a consulta: ' . $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios.']);
}

// Fechar a conexão
$conn->close();
?>
