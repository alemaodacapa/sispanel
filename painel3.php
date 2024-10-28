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
    <link href="/img/att.jpg" rel="icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Atendimento Consultório</title>
    <link href="css/bootstrap2.css" rel="stylesheet">
    <link href="css/style3.css" rel="stylesheet">
    <script src="lib/jquery-3.3.1.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/funcoes_painel.js"></script>
    <script src="js/jquery.js"></script>
    <script src="js/script.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<style>
    body {
        background: linear-gradient(135deg, #007bff, #5a5aff);
        color: #333;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        align-items: center;
        font-family: 'Cairo', sans-serif;
        margin: 0;
        padding: 0;
    }

    .barraSuperior {
        background-color: #003a5f;
        color: #fff;
        padding: 20px;
        font-size: 2.5rem;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .container {
        flex: 1;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 20px;
        padding: 20px;
        width: 100%;
        max-width: 90%; /* Aumenta a largura máxima para aproveitar mais espaço em telas grandes */
    }

    /* Configurações das caixas */
    .caixa {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
        padding: 10px; /* Diminui o padding das caixas */
        text-align: center;
        transition: transform 0.2s, box-shadow 0.2s;
        color: #0C315C;
        font-size: 3.8rem; /* Diminui o tamanho da fonte */
        max-height: 450px; /* Ajuste a altura máxima para evitar rolagem */
        overflow: hidden; /* Esconde o conteúdo que ultrapassar a altura */
    }

    .caixa-titulo {
        font-weight: bold;
        font-size: 1.8rem; /* Diminui o tamanho do título */
        margin-bottom: 15px;
        color: #007bff;
    }

    .caixa:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }

    /* Caixa de vídeo com redirecionamento */
    .video-container {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        margin: 10px 0;
        background-color: #000;
        width: 100%;
        height: 100%;
    }

    .video-container video {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Garante que o vídeo preencha a caixa completamente */
    }

    /* Link invisível sobre o vídeo */
    .link-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .footer {
        width: 100%;
        background-color: #1a1a1d;
        color: #c9c9c9;
        padding: 15px;
        text-align: center;
        font-size: 1.2rem;
        margin-top: auto;
    }

    .data-hora {
        font-size: 1.5rem;
        color: #fff;
        margin-bottom: 5px;
    }

    /* Ajustes específicos para dispositivos móveis */
    @media (max-width: 400px) {
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
        }
        
        .titulo {
            font-size: 1rem; /* Ajuste o tamanho da fonte conforme necessário */
            margin-left: 10px; /* Espaço entre o logo e o título */
        }

        .caixa {
            width: 100%;
            max-width: 400px;
            margin: 5px 0;
            padding: 5px;
            height: auto; /* Permite que as caixas se ajustem sem rolagem */
        }

        .caixa .numero {
            font-size: 3rem; /* Diminui o tamanho do número */
        }

        .caixa-titulo {
            font-size: 2rem; /* Mantém o tamanho do título pequeno */
        }

        .video-container {
            height: 200px;
            border-radius: 5px;
            margin: 0;
        }

        .campo-caixa,
        .caixa-normal,
        .caixa-anterior {
            font-size: 20px; /* Tamanho da fonte ajustado */
            padding: 10px; /* Ajusta o padding */
        }

        .campo-caixa-usuario {
            font-size: 20px; /* Tamanho da fonte ajustado */
            padding: 10px; /* Ajusta o padding */
        }

        .barraSuperior {
            height: auto; /* Ajusta a altura para dispositivos móveis */
            padding: 10px; /* Reduzir o padding para economizar espaço */
        }
    }

    /* Ajustes específicos para desktop */
    @media (min-width: 601px) {
        .caixa {
            width: 100%;
            max-width: auto;
            margin: 3px 20;
            padding: 15px; /* Mantém o padding em desktop */
            height: auto; /* Permite que as caixas se ajustem ao conteúdo */
        }

        .caixa-titulo {
            font-size: 2rem; /* Mantém o tamanho do título grande */
        }

        .caixa .numero {
            font-size: 2.5rem; /* Diminui o tamanho do número */
        }

        .video-container {
            height: 400px; /* Altura da caixa de vídeo */
            border-radius: 5px;
            margin: 0;
        }

        .titulo {
            font-size: 3rem; /* Ajuste o tamanho da fonte conforme necessário */
            margin-left: 10px; /* Espaço entre o logo e o título */
        }
        
        .campo-caixa,
        .caixa-normal,
        .caixa-anterior {
            font-size: 35px; /* Tamanho da fonte ajustado */
            padding: 18px; /* Ajusta o padding */
        }

        .campo-caixa-usuario {
            font-size: 35px; /* Tamanho da fonte ajustado */
            padding: 18px; /* Ajusta o padding */
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
</style>


<!-- Inclua seu logo na barra superior -->
<div class="barraSuperior">
    <p style="font-size: 2rem;">Atendimento com Senha</p>
</div>

</head>
<body>

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

    <!-- Caixa de Anúncio com redirecionamento sobreposto ao vídeo -->
    <div class="caixa" style="grid-column: span 2;">
        <div class="video-container">
            <video autoplay loop muted>
                <source src="video/SEO_Summerside.mp4" type="video/mp4">
                Seu navegador não suporta vídeo HTML5.
            </video>
            <a href="https://painelsummerside.com.br" target="_blank" class="link-overlay"></a>
        </div>
    </div>

    <!-- Segunda Caixa de Anúncio com redirecionamento sobreposto ao vídeo -->
    <div class="caixa">
        <div class="video-container">
            <video autoplay loop muted>
                <source src="video/google_meu_negocio.mp4" type="video/mp4">
                Seu navegador não suporta vídeo HTML5.
            </video>
            <a href="https://painelsummerside.com.br" target="_blank" class="link-overlay"></a>
        </div>
    </div>
</main>


<footer class="footer">
    <p>© Sis Panel - Todos os direitos reservados</p>
</footer>

<audio id="audioChamada" src="audio/chamada.wav"></audio>

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
            }, 25000); // 25000 milissegundos = 25 segundos
        };
    </script>
</body>
</html>



