document.addEventListener('DOMContentLoaded', function () {
    const registerBtn = document.getElementById('register-btn');
    const popup = document.getElementById('register-popup');
    const closeBtn = document.querySelector('.close-btn');

    registerBtn.addEventListener('click', function () {
        popup.style.display = 'flex';
    });

    closeBtn.addEventListener('click', function () {
        popup.style.display = 'none';
    });

    // Fechar o popup se o usuário clicar fora da área de conteúdo
    window.addEventListener('click', function (event) {
        if (event.target === popup) {
            popup.style.display = 'none';
        }
    });
});
