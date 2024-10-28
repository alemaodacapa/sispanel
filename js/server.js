const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const mysql = require('mysql');

const app = express();
const server = http.createServer(app);
const io = socketIo(server);

// Configuração do banco de dados
const db = mysql.createConnection({
    host: 'localhost', // ou o endereço do seu servidor de banco de dados
    user: 'seu_usuario',
    password: 'sua_senha',
    database: 'seu_banco_de_dados'
});

db.connect((err) => {
    if (err) throw err;
    console.log('Conectado ao banco de dados!');
});

// Rota principal
app.get('/', (req, res) => {
    res.sendFile(__dirname + '/index.html');
});

// Lógica para atualizar informações em tempo real
io.on('connection', (socket) => {
    console.log('Um usuário se conectou');

    // Função para buscar dados do banco de dados
    const fetchData = () => {
        db.query('SELECT * FROM sua_tabela', (err, results) => {
            if (err) throw err;
            socket.emit('data', results); // Envia os dados para o cliente
        });
    };

    // Chama a função inicialmente
    fetchData();

    // Configura um intervalo para atualizar os dados a cada 10 segundos
    const intervalId = setInterval(fetchData, 10000);

    socket.on('disconnect', () => {
        console.log('Usuário desconectado');
        clearInterval(intervalId); // Limpa o intervalo ao desconectar
    });
});

const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
    console.log(`Servidor rodando na porta ${PORT}`);
});
