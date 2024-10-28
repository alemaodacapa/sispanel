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
    // Obter a última senha gerada e as informações do cliente
    $sql_cliente = "
        SELECT 
            c.senha AS senha_gerada, 
            c.nome, 
            c.tipo_senha,
            c.id
        FROM clientes c
        ORDER BY c.id DESC 
        LIMIT 1
    ";
    
    $result = $conn->query($sql_cliente);

    if ($result->num_rows > 0) {
        // Obter os dados do cliente
        $cliente = $result->fetch_assoc();

        // Obter a última senha gerada anteriormente
        $sql_senhas_anteriores = "
            SELECT senha 
            FROM clientes 
            WHERE id < ? 
            ORDER BY id DESC 
            LIMIT 1
        ";
        $stmt = $conn->prepare($sql_senhas_anteriores);
        $stmt->bind_param("i", $cliente['id']);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $senha_anterior = $resultado->num_rows > 0 ? $resultado->fetch_assoc()['senha'] : 'Nenhuma senha anterior';

    } else {
        // Se não houver cliente, definir valores padrão
        $cliente = [
            'senha_gerada' => '0000',
            'nome' => 'Nome do Cliente',
            'tipo_senha' => 'normal',
            'id' => 0
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Atendimento</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/bootstrap2.css" rel="stylesheet">
    <link href="css/style3.css" rel="stylesheet">
    <script src="lib/jquery-3.3.1.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/funcoes_painel.js"></script>
    <script src="js/jquery.js"></script>
    <script src="js/script.js"></script>
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
        }
        
        /* Barra superior */
        .barraSuperior {
            background-color: #003a5f;
            color: #fff;
            padding: 20px;
            font-size: 2.5rem; /* Aumenta o tamanho da fonte */
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            width: 100%;
        }

        /* Container principal */
        .container {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); /* Caixas responsivas */
            gap: 20px; /* Espaçamento entre as caixas */
            padding: 20px;
            width: 100%;
            max-width: 1200px; /* Limita a largura máxima */
        }

        /* Estilo da caixa */
        .caixa {
            background: rgba(255, 255, 255, 0.9); /* Fundo branco semi-transparente */
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            padding: 40px; /* Aumenta o padding */
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
            color: #333;
            font-size: 1.8rem; /* Aumenta o tamanho da fonte */
        }

        .caixa-titulo {
            font-weight: bold;
            font-size: 2.2rem; /* Aumenta o tamanho do título */
            margin-bottom: 15px;
            color: #007bff;
        }

        .caixa:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        /* Estilo do vídeo */
        .video-container {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            margin: 10px 0;
            background-color: #000; /* Fundo preto para o vídeo */
        }

        .video-container video {
            width: 100%;
            height: 100%; /* Garante que o vídeo preencha a altura */
            object-fit: cover; /* Mantém a proporção do vídeo */
            border-radius: 10px;
        }

        /* Botão Visitar o Site */
        .botao-visitar {
            display: inline-block;
            padding: 15px 30px; /* Aumenta o tamanho do botão */
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 10px;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.2s;
            font-size: 1.5rem; /* Aumenta o tamanho da fonte do botão */
        }

        .botao-visitar:hover {
            background-color: #0056b3;
            transform: scale(1.05); /* Efeito de aumento ao passar o mouse */
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
        }

        /* Estilo para data e hora */
        .data-hora {
            font-size: 1.5rem; /* Aumenta o tamanho da fonte */
            color: #fff;
            margin-bottom: 5px;
        }

        /* Responsividade */
        @media (max-width: 600px) {
            .container {
                display: flex;
                flex-direction: column; /* Muda a direção do layout para coluna */
                align-items: center; /* Centraliza as caixas */
                padding: 10px; /* Adiciona algum padding lateral */
            }

            .caixa {
                width: 100%; /* Faz as caixas ocuparem 100% da largura */
                max-width: 400px; /* Define uma largura máxima para as caixas */
                margin: 10px 0; /* Adiciona margem entre as caixas */
            }

            .video-container {
                height: auto; /* Deixa a altura do vídeo automática */
            }
        }

        @media (min-width: 600px) {
            .video-container {
                height: 250px; /* Ajusta a altura do vídeo em telas grandes */
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
        <div class="caixa-titulo">CAIXA</div>
        <h2><div id="tipoSenha"><strong><?php echo strtoupper($cliente['tipo_senha']); ?></strong></div></h2>
    </div>
    <div class="caixa">
        <div class="caixa-titulo">ANTERIORES</div>
        <h2><div id="senhaAnterior" class="numero"><strong><?php echo $senha_anterior; ?></strong></div></h2>
    </div>
    <div class="caixa">
        <div class="caixa-titulo">USUÁRIO</div>
        <h3><div id="nomeCliente"><strong><?php echo $cliente['nome']; ?></strong></div></h3>
        <h2><div id="senhaGerada" class="numero"><strong><?php echo $cliente['senha_gerada']; ?></strong></div></h2>
    </div>
    
    <div class="caixa" style="grid-column: span 2;"> <!-- Faz a caixa ocupar duas colunas -->
        <div class="caixa-titulo">ANÚNCIO</div>
        <div class="video-container">
            <video autoplay loop muted>
                <source src="video/SEO_Summerside.mp4" type="video/mp4">
                Seu navegador não suporta vídeo HTML5.
            </video>
        </div>
        <a href="https://painelsummerside.com.br" class="botao-visitar" style="color: blue">Visitar o Site</a>
    </div>
    <div class="caixa">
        <div class="caixa-titulo">ANÚNCIO</div>
        <div class="video-container">
            <video autoplay loop muted>
                <source src="video/google_meu_negocio.mp4" type="video/mp4">
                Seu navegador não suporta vídeo HTML5.
            </video>
        </div>
        <a href="https://painelsummerside.com.br" class="botao-visitar">Visitar o Site</a>
    </div>
</main>

<footer class="footer">
    <p>© Sis Panel - Todos os direitos reservados</p>
</footer>

<!-- Áudio de chamada -->
<audio id="audioChamada" src="audio/chamada.wav"></audio>
<!-- Áudio da narração -->
<audio id="audioNarracao" src="audio/narracao.mp3"></audio>

<script>
    function tocarAudio() {
        const audio = document.getElementById('audioChamada');
        audio.play();
    }

    function tocarNarracao() {
        const narracao = document.getElementById('audioNarracao');
        narracao.play();
    }

    function atualizarDados() {
        const nomeCliente = document.getElementById('nomeCliente');
        const senhaGerada = document.getElementById('senhaGerada');

        // Aqui você pode definir as variáveis 'nome' e 'senha' a partir de seus dados
        const nome = '<?php echo $cliente['nome']; ?>'; 
        const senha = '<?php echo $cliente['senha_gerada']; ?>'; 

        // Atualiza os elementos
        nomeCliente.innerHTML = `<strong>${nome}</strong>`;
        senhaGerada.innerHTML = `<strong>${senha}</strong>`;

        // Toca a narração após atualizar os dados
        tocarNarracao();
    }

    // Chama a função para atualizar os dados e tocar áudio
    atualizarDados();
    setInterval(atualizarDados, 10000); // Atualiza os dados a cada 10 segundos

    // Atualiza a página a cada 40 segundos
    setInterval(function() {
        location.reload();
    }, 40000); // 40000 milissegundos = 40 segundos

    // Tocar o áudio de chamada no início
    tocarAudio();
</script>
</body>
</html>


