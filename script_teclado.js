document.addEventListener("DOMContentLoaded", function() {
    // Verifica se estamos na página manual.html
    if (window.location.pathname === '/manual.html') {
        // Coloque aqui as funções que devem ser executadas somente na página manual.html

        function exemploFuncao() {
            console.log("Função executada na página manual.html");
        }

        exemploFuncao();
    }
});

// Função para preencher números com zeros à esquerda
function pad(num, size) {
    var s = num + "";
    while (s.length < size) s = "0" + s;
    return s;
}

jQuery(document).ready(function($) {

    // Função para processar comandos
    function processarComando(comando) {
        var senhaAtual   = $("#senhaAtualNumero");
        var senhaNormal  = $("#senhaNormal");
        var senhaPrior   = $("#senhaPrioridade");
        var ultimaSenha  = $("#ultimaSenhaNumero");
        var audioChamada = $("#audioChamada");

        if(comando == 'next'){
            ultimaSenha.html(senhaAtual.html());
            senha = parseInt(senhaNormal.val()) + 1;
            senhaAtual.html(pad(senha, 4));
            senhaNormal.val(pad(senha, 4));
            audioChamada.trigger("play");
            audioChamada.trigger("enter");
        }
        if(comando == 'prev'){
            senha = parseInt(senhaNormal.val()) - 1;
            senhaAtual.html(pad(senha, 4));
            senhaNormal.val(pad(senha, 4));
        }
        if(comando == 'priorityNext'){
            ultimaSenha.html(senhaAtual.html());
            senha = parseInt(senhaPrior.val().replace("P","")) + 1;
            senhaAtual.html("P" + pad(senha, 3));
            senhaPrior.val("P" + pad(senha, 3));
            audioChamada.trigger("play");
            audioChamada.trigger("enter");
        }
        if(comando == 'priorityPrev'){
            senha = parseInt(senhaPrior.val().replace("P","")) - 1;
            senhaAtual.html("P" + pad(senha, 3));
            senhaPrior.val("P" + pad(senha, 3));
        }
    }

    // Escutar eventos de teclado físico
    $("body").on('keydown', function(e) {
        switch(e.keyCode) {
            case 39: // >
                processarComando('next');
                break;
            case 65: // A
                processarComando('prev');
                break;
            case 38: // ^
                processarComando('priorityNext');
                break;
            case 83: // S
                processarComando('priorityPrev');
                break;
        }
    });

    // Função para escutar comandos vindos do teclado virtual (teclado.html)
    async function escutarComandos() {
        try {
            const resposta = await fetch('https://e-panel.x10.mx/get-commands', { method: 'GET' });
            const data = await resposta.json();

            if (data && data.command) {
                switch(data.command) {
                    case '>':
                        processarComando('next');
                        break;
                    case 'A':
                        processarComando('prev');
                        break;
                    case '^':
                        processarComando('priorityNext');
                        break;
                    case 'S':
                        processarComando('priorityPrev');
                        break;
                    default:
                        console.log("Comando desconhecido: ", data.command);
                        break;
                }
            }
        } catch (error) {
            console.error("Erro ao escutar comandos:", error);
        }
    }

    // Verificar novos comandos a cada 2 segundos
    setInterval(escutarComandos, 2000);
});
