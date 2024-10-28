<?php
// Conexão com o banco de dados
$servidor = 'localhost';
$usuario = 'usuario';
$senha = 'senha';
$bd = 'database';

$conn = new mysqli($servidor, $usuario, $senha, $bd);
if ($conn->connect_error) {
    die('Conexão não estabelecida: ' . $conn->connect_error);
}

try {
    // Obter a última senha e informações do cliente
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
        $cliente = $result->fetch_assoc();

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
        $cliente = [
            'senha_gerada' => '0000',
            'nome' => 'Nome do Cliente',
            'tipo_senha' => 'normal',
            'id' => 0
        ];
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
    <title>Painel de Consultório</title>

    <!-- CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- JS -->
    <script src="lib/jquery-3.3.1.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/funcoes_painel.js"></script>
    <script src="js/jquery.js"></script>
    <script src="js/script.js"></script>
    <script src="server.js"></script>

    <!-- Meta Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s) {
            if(f.fbq)return;n=f.fbq=function() {
                n.callMethod ? n.callMethod.apply(n,arguments) : n.queue.push(arguments)
            };
            if(!f._fbq) f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)
        }(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '4299889786786958');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=4299889786786958&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Meta Pixel Code -->

    <!-- Estilos -->
    <style>
        /* Estilos Gerais */
        body {
            background-color: #ffffff;
            color: #000000;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Estilos da Barra Superior */
        .barraSuperior {
            background-color: #003a5f; /* Cor da barra superior */
            padding: 10px; /* Ajustado para maior responsividade */
            color: white;
            margin-bottom: 20px;
            text-align: center;
        }

        .uespiLogo {
            height: 60px; /* Altura do logo ajustada */
        }

        .uespiTexto {
            font-size: 20px; /* Tamanho da fonte ajustado */
            font-weight: bold;
        }

        .subtitulo {
            font-size: 16px; /* Tamanho do subtítulo ajustado */
        }

        /* Estilos do Container */
        .container.page {
            padding: 20px;
        }

        /* Estilos das Caixas */
        .campo-consultorio, .campo-caixa, .campo-consultorio-paciente {
            background-color: #007bff; /* Cor de fundo das caixas */
            border-radius: 5px; /* Bordas arredondadas */
            padding: 15px; /* Espaçamento interno reduzido */
            font-size: 24px; /* Tamanho da fonte reduzido */
            text-align: center; /* Centraliza o texto */
            color: white; /* Cor do texto */
            margin-bottom: 20px; /* Margem abaixo */
        }

        .video-container {
            position: relative;
            width: 100%;
            height: 150px; /* Altura reduzida para melhor visualização em dispositivos móveis */
            overflow: hidden;
        }

        .video-container video {
            width: 100%;
            height: 100%;
            display: block;
        }

        .link-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
            text-decoration: none;
            background: rgba(0, 0, 0, 0.0); /* Fundo semi-transparente */
            display: flex; /* Flex para centralizar o conteúdo */
            align-items: center; /* Alinha verticalmente ao centro */
            justify-content: center; /* Alinha horizontalmente ao centro */
            color: white; /* Cor do texto do link */
            font-size: 20px; /* Tamanho da fonte do link */
        }

        /* Estilos do Footer */
        .footer {
            background-color: #003a5f; /* Cor de fundo do footer */
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            width: 100%;
            bottom: 0;
            font-size: 14px; /* Tamanho da fonte do footer */
        }

        .footer a {
            color: #ffff00; /* Cor do link */
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline; /* Sublinha ao passar o mouse */
        }

        /* Estilos Responsivos */
        @media (max-width: 768px) {
            /* Reduzindo tamanhos de fonte e espaçamento em telas menores */
            .barraSuperior {
                padding: 10px; /* Menos padding */
            }

            .campo-consultorio, .campo-caixa, .campo-consultorio-paciente {
                font-size: 22px; /* Fonte menor */
                position: auto;
                padding: 10px; /* Menos padding */
            }

            .video-container {
                height: 150px; /* Menor altura para o vídeo */
            }

            .footer {
                font-size: 14px; /* Fonte menor no footer */
            }
        }
    </style>

    <script>
        function tocarAudio() {
            const audio = document.getElementById('audioChamada');
            audio.play();
        }

        function narrarTexto(texto) {
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(texto);
                speechSynthesis.speak(utterance);
            } else {
                alert('Navegador não suporta síntese de fala.');
            }
        }

        function narrarInformacoes() {
            const nomeCliente = document.getElementById('nomeCliente').textContent;
            const senhaGerada = document.getElementById('senhaGerada').textContent;
            narrarTexto(`Senha gerada para ${nomeCliente} é ${senhaGerada}`);
        }

        window.onload = function() {
            tocarAudio();
            narrarInformacoes();

            setInterval(function() {
                location.reload();
            }, 25000);
        };
    </script>
</head>
<body>
    <div class="barraSuperior">
        <img src="img/att.jpg" class="uespiLogo" alt="Logo">
        <div>
            <span class="uespiTexto">ATENDIMENTO</span><br>
            <span class="subtitulo">Chamada <strong>por Senha</strong></span>
            <a href="https://social.x10.mx" class="info-link">
                <i class="fa fa-info-circle"></i> Info
            </a>
        </div>
    </div>
    
    <div class="container page">
        <div class="row">
            <div class="col-xs-6">
                <div class="campo-consultorio">
                    <div><strong>CONSULTÓRIO</strong></div>
                    <div id="info">
                        <strong id="tipoSenha" style="font-size: 15px; font-weight: bold;">
                            <?php echo strtoupper($cliente['tipo_senha']); ?>
                        </strong>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="campo-caixa">
                    <div><strong>SENHA</strong></div>
                    <div id="info">
                        <span id="senhaGerada" style="font-size: 22px; font-weight: bold;">
                            <?php echo $cliente['senha_gerada']; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6">
                <div class="caixa-anterior">
                    <div><strong>ÚLTIMA SENHA</strong></div>
                    <div id="info">
                        <div id="senhaAnterior" style="font-size: 22px; font-weight: bold;">
                            <?php echo $senha_anterior; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="campo-caixa-usuario">
                    <div><strong>PACIENTE</strong></div>
                    <span id="nomeCliente" style="font-size: 22px; font-weight: bold;">
                        <?php echo $cliente['nome']; ?>
                    </span><br>
                    <div id="info"></div>
                </div>
            </div>
        </div>

        <!-- Caixa de Anúncio com redirecionamento sobreposto ao vídeo -->
        <div class="video-container">
            <video autoplay loop muted>
                <source src="video/SisPanel.mp4" type="video/mp4">
                Seu navegador não suporta o elemento de vídeo.
            </video>
            <a href="https://pay.hotmart.com/Y95202654S?checkoutMode=2" class="link-overlay"></a>
            <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
            </div>
        </div>
    </div>

    <audio id="audioChamada" src="audio/chamada.mp3"></audio>
    
    <footer class="footer">
        <p>© 2024 Sis Panel. Todos os direitos reservados. | <a href="https://social.x10.mx">Social Media</a></p>
    </footer>
</body>
</html>
