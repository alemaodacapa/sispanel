<?php
// Definir informações de conexão com o banco de dados
$servidor = 'localhost'; // Altere para o servidor de banco de dados
$usuario = 'usuario'; // Altere para o nome de usuário do banco de dados
$senha = 'Senha'; // Altere para a senha do banco de dados
$bd = 'database'; // Altere para o nome do banco de dados

// Criar conexão com o banco de dados
$conn = new mysqli($servidor, $usuario, $senha, $bd);

// Verificar se a conexão foi estabelecida corretamente
if ($conn->connect_error) {
    die('Conexão não estabelecida: ' . $conn->connect_error);
}

// Fechar a conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SisPainel Consultório - Login do Operador</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }

    .login-header {
      text-align: center;
      margin-bottom: 20px;
    }

    .login-header h1 {
      font-size: 1.8em;
      font-weight: bold;
      color: #007bff;
    }

    .form-group label {
      font-weight: bold;
    }

    .btn-login {
      font-weight: bold;
      font-size: 1.2em;
      width: 100%;
      padding: 10px;
    }

    .footer {
      text-align: center;
      margin-top: 20px;
      font-size: 0.9em;
    }

    .footer a {
      text-decoration: underline;
      color: #007bff;
      cursor: pointer;
    }

    .modal-content {
      padding: 20px;
    }

    .modal-header {
      border-bottom: none;
    }

    .alert {
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="login-container">
      <div class="login-header text-center">
        <h1>SisPainel Consultório</h1>
        <p>Login do Operador</p>
      </div>

      <?php if (isset($_GET['mensagem'])): ?>
        <div class="alert alert-<?php echo strpos($_GET['mensagem'], 'sucesso') !== false ? 'success' : 'danger'; ?>" role="alert">
          <?php echo htmlspecialchars($_GET['mensagem']); ?>
        </div>
      <?php endif; ?>
      
      <form action="processa_login.php" method="POST">
        <div class="form-group">
          <label for="usuario">Usuário (CPF):</label>
          <input type="text" id="usuario" name="usuario" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="senha">Senha:</label>
          <input type="password" id="senha" name="senha" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-login">Entrar</button>
      </form>
      
      <div class="footer text-center mt-3">
        <a href="#" data-toggle="modal" data-target="#modalCadastro">Cadastre-se aqui</a> |
        <a href="#" data-toggle="modal" data-target="#modalCadastroGerente">Cadastro de Gerente</a> |
        <a href="https://e-painel.x10.mx/painel.php" target="_blank">Painel Eletrônico Online</a>
      </div>

      <!-- Botão de Login do Gerente -->
      <div class="text-center mt-3">
        <button type="button" class="btn btn-info" onclick="showLoginPopup()">Login do Gerente</button>
      </div>
    </div>
  </div>

  <!-- Modal de Cadastro Operador -->
  <div class="modal fade" id="modalCadastro" tabindex="-1" aria-labelledby="modalCadastroLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCadastroLabel">Cadastro de Operador</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="processa_cadastro.php" method="POST">
            <div class="form-group">
              <label for="nome">Nome:</label>
              <input type="text" id="nome" name="nome" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="cpf">CPF:</label>
              <input type="text" id="cpf" name="cpf" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="senha_cadastro">Senha:</label>
              <input type="password" id="senha_cadastro" name="senha" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Cadastrar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de Cadastro Gerente -->
  <div class="modal fade" id="modalCadastroGerente" tabindex="-1" aria-labelledby="modalCadastroGerenteLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCadastroGerenteLabel">Cadastro de Gerente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="processa_cadastro_gerente.php" method="POST">
            <div class="form-group">
              <label for="nome_gerente">Nome:</label>
              <input type="text" id="nome_gerente" name="nome_gerente" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="cpf_gerente">CPF:</label>
              <input type="text" id="cpf_gerente" name="cpf_gerente" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="senha_gerente">Senha:</label>
              <input type="password" id="senha_gerente" name="senha_gerente" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Cadastrar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
  function showLoginPopup() {
    const nome = prompt("Digite seu nome:");
    const cpf = prompt("Digite seu CPF:");
    const senha = prompt("Digite sua senha:");

    if (nome && cpf && senha) {
      // Cria um formulário temporário
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'validacao_gerente.php'; // Página PHP para validar o gerente

      // Adiciona os campos ao formulário
      const nomeField = document.createElement('input');
      nomeField.type = 'hidden';
      nomeField.name = 'nome';
      nomeField.value = nome;
      form.appendChild(nomeField);

      const cpfField = document.createElement('input');
      cpfField.type = 'hidden';
      cpfField.name = 'cpf';
      cpfField.value = cpf;
      form.appendChild(cpfField);

      const senhaField = document.createElement('input');
      senhaField.type = 'hidden';
      senhaField.name = 'senha';
      senhaField.value = senha;
      form.appendChild(senhaField);

      // Adiciona o formulário ao corpo do documento
      document.body.appendChild(form);

      // Submete o formulário
      form.submit();
    } else {
      alert("Por favor, preencha todos os campos.");
    }
  }
</script>

</body>

</html>
