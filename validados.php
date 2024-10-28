<?php
// Definir informações de conexão com o banco de dados
$servidor = 'localhost'; // Altere para o servidor de banco de dados
$usuario = 'usuario';    // Altere para o nome de usuário do banco de dados
$senha = 'senha';       // Altere para a senha do banco de dados
$bd = 'database';  // Altere para o nome do banco de dados

// Criar conexão com o banco de dados
$conn = new mysqli($servidor, $usuario, $senha, $bd);

// Verificar se a conexão foi estabelecida corretamente
if ($conn->connect_error) {
    die('Conexão não estabelecida: ' . $conn->connect_error);
}

try {
    // Obter o último atendimento registrado
    $sql_atendimento = "
        SELECT 
            a.senha AS senha_gerada, 
            a.tipo_atendimento,  -- Alterado de tipo_senha para tipo_atendimento
            a.cpf AS id_cliente, -- Alterado de id_cliente para cpf, já que a tabela 'atendimentos' não possui 'id_cliente'
            a.id AS atendimento_id
        FROM atendimentos a
        ORDER BY a.id DESC 
        LIMIT 1
    ";
    
    $result = $conn->query($sql_atendimento);

    if ($result->num_rows > 0) {
        // Obter os dados do atendimento
        $atendimento = $result->fetch_assoc();

        // Obter as informações do cliente correspondente
        $sql_cliente = "
            SELECT 
                c.nome, 
                SUBSTRING(c.cpf, 1, 3) AS cpf
            FROM clientes c
            WHERE c.cpf = ?  -- Alterado de c.id para c.cpf, já que 'atendimentos' usa CPF e não ID
        ";
        $stmt = $conn->prepare($sql_cliente);
        $stmt->bind_param("s", $atendimento['id_cliente']);
        $stmt->execute();
        $resultado_cliente = $stmt->get_result();
        $cliente = $resultado_cliente->fetch_assoc();

        // Obter a última senha gerada anteriormente
        $sql_senhas_anteriores = "
            SELECT senha 
            FROM atendimentos 
            WHERE id < ? 
            ORDER BY id DESC 
            LIMIT 1
        ";
        $stmt = $conn->prepare($sql_senhas_anteriores);
        $stmt->bind_param("i", $atendimento['atendimento_id']);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $senha_anterior = $resultado->num_rows > 0 ? $resultado->fetch_assoc()['senha'] : 'Nenhuma senha anterior';

    } else {
        // Se não houver atendimento, definir valores padrão
        $cliente = [
            'nome' => 'Nome do Cliente',
            'cpf' => '---'
        ];
        $atendimento = [
            'senha_gerada' => '0000',
            'tipo_atendimento' => 'normal',  // Alterado para tipo_atendimento
            'id_cliente' => 0
        ];
        $senha_anterior = 'Nenhuma senha anterior';
    }
} catch (Exception $e) {
    // Exibir a mensagem de erro diretamente para diagnóstico
    die('Erro ao consultar dados: ' . $e->getMessage());
}

// Fechar a conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link href="/img/att.jpg" rel="icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Atendimentos</title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="lib/jquery-3.3.1.min.js"></script>
    <script src="js/main.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #ffffff;
            color: #000000;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .barraSuperior {
            background-color: #0056b3;
            padding: 20px;
            color: white;
            height: 200px;
            margin-bottom: 20px;
            position: relative;
        }

        .uespiLogo {
            height: 80px;
        }

        .uespiTexto {
            font-size: 24px;
            font-weight: bold;
        }

        .subtitulo {
            font-size: 18px;
        }

        .container.page {
            padding: 1px;
        }

        .campo-caixa {
            background-color: #007bff;
            border-radius: 5px;
            padding: 20px;
            font-size: 40px;
            text-align: center;
            color: white;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .campo-caixa-usuario {
            background-color: #ffff00;
            color: #000000;
            font-size: 40px;
            padding: 20px;
            font-weight: bold;
            text-align: center;
            border-radius: 5px;
        }

        .row {
            margin-left: 0;
            margin-right: 0;
        }

        .col-xs-6 {
            padding-left: 10px;
            padding-right: 10px;
        }

        .caixa-normal,
        .caixa-anterior {
            background-color: #0056b3;
            border-radius: 5px;
            padding: 20px;
            font-size: 40px;
            text-align: center;
            color: white;
            margin-bottom: 20px;
            overflow: hidden;
        }

        @media (max-width: 767px) {
            .col-xs-6 {
                width: 100%;
                padding-left: 0;
                padding-right: 0;
            }
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .campo-caixa,
            .caixa-normal,
            .caixa-anterior {
                font-size: 30px;
                padding: 15px;
            }

            .campo-caixa-usuario {
                font-size: 30px;
                padding: 15px;
            }
        }

        @media (min-width: 992px) and (max-width: 1199px) {
            .campo-caixa,
            .caixa-normal,
            .caixa-anterior {
                font-size: 35px;
                padding: 18px;
            }

            .campo-caixa-usuario {
                font-size: 35px;
                padding: 18px;
            }
        }

        @media (min-width: 1200px) {
            .campo-caixa,
            .caixa-normal,
            .caixa-anterior {
                font-size: 40px;
                padding: 20px;
            }

            .campo-caixa-usuario {
                font-size: 40px;
                padding: 20px;
            }
        }

        .info-link {
            display: inline-flex;
            align-items: center;
            margin-left: 10px;
            font-size: 16px;
        }

        .info-link i {
            margin-right: 5px;
            font-size: 20px;
            color: #007bff;
        }

        .footer {
            background-color: #0056b3;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        .footer a {
            color: #ffff00;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>

    <script>
        // Função para tocar o áudio
        function tocarAudio() {
            const audio = document.getElementById('audioChamada');
            audio.play();
        }

        // Função para narrar texto
        function narrarTexto(texto) {
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(texto);
                speechSynthesis.speak(utterance);
            } else {
                alert('Navegador não suporta síntese de fala.');
            }
        }

        // Função para narrar o nome do cliente e a senha gerada ao carregar a página
        function narrarInformacoes() {
            const nomeCliente = document.getElementById('nomeCliente').textContent;
            const senhaGerada = document.getElementById('senhaGerada').textContent;
            narrarTexto(`Senha gerada para ${nomeCliente} é ${senhaGerada}`);
        }

        // Executa as funções quando a página é carregada
        window.onload = function() {
            tocarAudio(); // Toca o áudio
            narrarInformacoes();
            
            // Atualiza a página a cada 25 segundos
            setInterval(function() {
                location.reload();
            }, 25000);
        }
    </script>
</head>

<body>
    <header class="barraSuperior">
        <div class="container">
            <img src="/img/att.jpg" class="uespiLogo" alt="Logo">
            <div class="uespiTexto">Sistema de Atendimento</div>
            <div class="subtitulo">Gerenciamento de Senhas</div>
        </div>
    </header>

    <div class="container page">
        <div class="row">
            <div class="col-xs-6">
                <div class="campo-caixa" id="senhaGerada">
                    <?php echo $atendimento['senha_gerada']; ?>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="campo-caixa-usuario" id="nomeCliente">
                    <?php echo $cliente['nome']; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6">
                <div class="caixa-normal">
                    Tipo de Atendimento: <?php echo ucfirst($atendimento['tipo_atendimento']); ?>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="caixa-anterior">
                    Última Senha: <?php echo $senha_anterior; ?>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer d-none d-md-block">
        <p><a href="https://social.x10.mx">Sis Panel</a> Todos os direitos reservados</p>
    </footer>

    <audio id="audioChamada" src="audio/chamada.wav"></audio>
</body>
</html>
