<?php
// Conexão ao banco de dados
$servidor = 'localhost';
$usuario = 'usuario';
$senha = 'senha';
$bd = 'database';

$conn = new mysqli($servidor, $usuario, $senha, $bd);
if ($conn->connect_error) {
    die('Conexão não estabelecida: ' . $conn->connect_error);
}

try {
    $sql_cliente = "SELECT c.senha AS senha_gerada, c.nome, c.tipo_senha, c.id FROM clientes c ORDER BY c.id DESC LIMIT 1";
    $result = $conn->query($sql_cliente);

    if ($result->num_rows > 0) {
        $cliente = $result->fetch_assoc();
        $sql_senhas_anteriores = "SELECT senha FROM clientes WHERE id < ? ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($sql_senhas_anteriores);
        $stmt->bind_param("i", $cliente['id']);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $senha_anterior = $resultado->num_rows > 0 ? $resultado->fetch_assoc()['senha'] : 'Nenhuma senha anterior';
    } else {
        $cliente = ['senha_gerada' => '0000', 'nome' => 'Nome do Cliente', 'tipo_senha' => 'normal', 'id' => 0];
        $senha_anterior = 'Nenhuma senha anterior';
    }
} catch (Exception $e) {
    die('Erro ao consultar dados: ' . $e->getMessage());
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Atendimento</title>
    <!-- CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/style5.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- JS -->
    <script src="lib/jquery-3.3.1.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/funcoes_painel.js"></script>
    <script src="server.js"></script>
    <style>
        /* Estilos gerais */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #007bff, #5a5aff);
            color: #fff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            align-items: center;
            transition: background-color 0.5s;
        }

        /* Barra superior */
        .barraSuperior {
            background-color: #003a5f;
            color: #fff;
            padding: 20px;
            font-size: 2.5rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            width: 100%;
            border-bottom: 5px solid #007bff;
        }

        /* Container principal */
        .container {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* Alinha 3 colunas */
            gap: 20px;
            padding: 20px;
            width: 100%;
            max-width: 1200px;
        }

        /* Estilo da caixa */
        .caixa {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            padding: 20px;
            position: relative;
            transition: transform 0.3s, box-shadow 0.3s;
            min-height: 150px; /* Altura mínima para alinhamento */
        }

        .caixa:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        .caixa-titulo {
            font-weight: bold;
            font-size: 2.2rem;
            margin-bottom: 10px;
            color: #007bff;
            text-align: center;
        }

        /* Estilo do vídeo */
        .video-container {
            position: relative;
            overflow: hidden;
            width: 100%;
            height: 100%;
            background-color: #000;
            border-radius: 10px;
        }

        .video-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        /* Botão Visitar o Site */
        .botao-visitar {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            padding: 10px 20px;
            background-color: rgba(0, 123, 255, 0.8);
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.2s;
            font-size: 1.2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .botao-visitar:hover {
            background-color: rgba(0, 86, 179, 0.8);
            transform: scale(1.05);
        }

        /* Rodapé */
        .footer {
            width: 100%;
            background-color: #1a1a1d;
            color: #c9c9c9;
            padding: 15px;
            text-align: center;
            font-size: 1.2rem;
            margin-top: auto;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.3);
        }

        /* Estilo para data e hora */
        .data-hora {
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 5px;
            text-align: center;
        }

        /* Responsividade */
        @media (max-width: 600px) {
            .container {
                grid-template-columns: 1fr; /* 1 coluna em dispositivos pequenos */
                align-items: center;
                padding: 10px;
            }

            .caixa {
                width: 100%;
                max-width: 200px;
                margin: 10px 0;
            }

            .video-container {
                height: auto;
            }
        }

        @media (min-width: 600px) {
            .video-container {
                height: 250px;
            }
        }
    </style>

</head>
<body>
    <header class="barraSuperior">
        ATENDIMENTO - CHAMADA POR SENHA
    </header>

    <main class="container">
        <div class="caixa">
            <div class="caixa-titulo">USUÁRIO</div>
            <h3><strong id="nomeCliente"><?php echo $cliente['nome']; ?></strong></h3>
            <h2><strong id="senhaGerada" class="numero"><?php echo $cliente['senha_gerada']; ?></strong></h2>
        </div>
        <div class="caixa">
            <div class="caixa-titulo">ANTERIORES</div>
            <h2><strong id="senhaAnterior" class="numero"><?php echo $senha_anterior; ?></strong></h2>
        </div>
        <div class="caixa">
            <div class="caixa-titulo">TIPO DE SENHA</div>
            <h3><strong id="tipoSenha"><?php echo $cliente['tipo_senha']; ?></strong></h3>
        </div>
        <div class="caixa">
            <div class="caixa-titulo">SENHAS EM ESPERA</div>
            <h3><strong id="senhasEmEspera">5</strong></h3>
        </div>
        <div class="caixa">
            <div class="caixa-titulo">ATENDIMENTO EM ANDAMENTO</div>
            <h3><strong id="atendimentoEmAndamento">2</strong></h3>
        </div>
    </main>

    <footer class="footer">
        <div class="video-container">
            <video autoplay muted loop>
                <source src="SEO_Summerside.mp4" type="video/mp4">
                Seu navegador não suporta o elemento de vídeo.
            </video>
            <a href="#" class="botao-visitar">Visitar o Site</a>
        </div>
        <div class="data-hora" id="dataHora"></div>
    </footer>

    <script>
        // Função para atualizar a data e hora em tempo real
        function atualizarDataHora() {
            const agora = new Date();
            const dataHora = agora.toLocaleString('pt-BR', { timeZone: 'America/Sao_Paulo' });
            document.getElementById('dataHora').innerText = dataHora;
        }
        setInterval(atualizarDataHora, 1000);
    </script>
</body>
</html>


