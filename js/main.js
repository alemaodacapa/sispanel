$(document).ready(function() {
    function tocarSom() {
        var audio = document.getElementById('audioChamada');
        if (audio) {
            audio.play();
        } else {
            console.error('Elemento de áudio não encontrado.');
        }
    }
