$(document).ready(function() {
    // Função para atualizar os dados do cliente no painel
    function atualizarDadosCliente(cliente) {
        const nomeCliente = cliente.nome || 'Cliente';
        const senha = cliente.senha_gerada || 'Senha';

        // Atualiza a interface com as novas informações
        $('.campo-caixa:nth-of-type(1) div:last-child').text(cliente.tipo_senha);
        $('.campo-caixa:nth-of-type(2) div:last-child').text(cliente.senha_anterior || 'Nenhuma senha anterior');
        $('.campo-caixa:nth-of-type(3) div:last-child').html(`<strong style="font-size: 36px;">${senha}</strong> - ${nomeCliente}`);
    }

    // Função para atualizar o painel com dados do servidor
    function atualizarPainel() {
        setInterval(() => {
            $.ajax({
                url: '/consultar_clientes', // Endpoint para obter os dados dos clientes
                method: 'GET',
                success: function(dados) {
                    // Atualize o conteúdo da página com os dados recebidos
                    $('#conteudoPainel').html(dados.map(cliente => 
                        `<tr>
                            <td>${cliente.id}</td>
                            <td>${cliente.nome}</td>
                            <td>${cliente.tipo_senha}</td>
                            <td>${cliente.senha}</td>
                        </tr>`).join(''));
                },
                error: function(error) {
                    console.error('Erro ao atualizar o painel:', error);
                }
            });
        }, 1000); // Consultar a cada 5 segundos
    }

    // Chame a função para começar o polling quando a página carregar
    atualizarPainel();
});
