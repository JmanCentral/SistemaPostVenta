document.addEventListener('DOMContentLoaded', function() {
    const coinBackground = document.getElementById('coinBackground');
    const coinCount = 30; // Número de monedas
    
    for (let i = 0; i < coinCount; i++) {
        createCoin();
    }

    function createCoin() {
        const coin = document.createElement('div');
        coin.classList.add('coin');
        resetCoin(coin);
        coinBackground.appendChild(coin);
        coin.addEventListener('animationend', () => resetCoin(coin));
    }

    function resetCoin(coin) {
        const posX = Math.random() * 100;
        coin.style.left = `${posX}%`;

        const size = Math.random() * 20 + 10;
        coin.style.width = `${size}px`;
        coin.style.height = `${size}px`;

        const duration = Math.random() * 10 + 5;
        coin.style.animationDuration = `${duration}s`;

        // Reiniciar animación
        coin.style.animation = 'none';
        void coin.offsetWidth; // Forzar reflujo
        coin.style.animation = `fall ${duration}s linear infinite`;
    }
});