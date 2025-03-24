<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Iniciar Sessión</title>
    <link href="assets/css/styles.css" rel="stylesheet" />
    <script src="assets/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        /* Estilo para el fondo animado de monedas */
        .coin-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: -1;
        background-color:rgb(15, 51, 80); /* Color de fondo detrás de las monedas */
    }
        
        .coin {
            position: absolute;
            display: block;
            width: 20px;
            height: 20px;
            background: radial-gradient(circle, gold 30%, goldenrod 70%);
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.8);
            animation: fall linear infinite;
            opacity: 0.7;
        }
        
        @keyframes fall {
            to {
                transform: translateY(100vh);
            }
        }
    </style>
</head>

<body class="bg-primary">
    <!-- Fondo animado de monedas -->
    <div class="coin-background" id="coinBackground"></div>
    
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header text-center">
                                    <img class="img-thumbnail" src="assets/img/banner.jpeg" width="200">
                                    <h3 class="font-weight-light my-4">Iniciar Sessión</h3>
                                </div>
                                <div class="card-body">
                                    <form action="" method="POST">
                                        <div class="form-group">
                                            <label class="small mb-1" for="usuario"><i class="fas fa-user"></i> Usuario</label>
                                            <input class="form-control py-4" id="usuario" name="usuario" type="text" placeholder="Ingrese usuario" required />
                                        </div>
                                        <div class="form-group">
                                            <label class="small mb-1" for="clave"><i class="fas fa-key"></i> Contraseña</label>
                                            <input class="form-control py-4" id="clave" name="clave" type="password" placeholder="Ingrese Contraseña" required />
                                        </div>
                                        <div class="alert alert-danger text-center d-none" id="alerta" role="alert"></div>
                                        <?php echo isset($alert) ? $alert : ''; ?>
                                        <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button class="btn btn-primary" type="submit">Login</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="assets/js/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script src="assets/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="assets/js/scripts.js"></script>
    <script>
        // Script para generar las monedas animadas
        document.addEventListener('DOMContentLoaded', function() {
            const coinBackground = document.getElementById('coinBackground');
            const coinCount = 30; // Número de monedas
            
            for (let i = 0; i < coinCount; i++) {
                createCoin();
            }
            
            function createCoin() {
                const coin = document.createElement('div');
                coin.classList.add('coin');
                
                // Posición horizontal aleatoria
                const posX = Math.random() * 100;
                
                // Tamaño aleatorio entre 10px y 30px
                const size = Math.random() * 20 + 10;
                
                // Duración de la animación entre 5s y 15s
                const duration = Math.random() * 10 + 5;
                
                // Retraso inicial entre 0s y 5s
                const delay = Math.random() * 5;
                
                // Opacidad aleatoria entre 0.5 y 0.9
                const opacity = Math.random() * 0.4 + 0.5;
                
                // Aplicar estilos
                coin.style.left = `${posX}%`;
                coin.style.width = `${size}px`;
                coin.style.height = `${size}px`;
                coin.style.animationDuration = `${duration}s`;
                coin.style.animationDelay = `${delay}s`;
                coin.style.opacity = opacity;
                
                // Posicionar fuera de la pantalla (arriba)
                coin.style.top = `-${size}px`;
                
                // Añadir la moneda al fondo
                coinBackground.appendChild(coin);
                
                // Cuando la animación termine, reiniciar la moneda
                coin.addEventListener('animationiteration', function() {
                    // Nueva posición horizontal
                    const newPosX = Math.random() * 100;
                    coin.style.left = `${newPosX}%`;
                    
                    // Nuevo tamaño
                    const newSize = Math.random() * 20 + 10;
                    coin.style.width = `${newSize}px`;
                    coin.style.height = `${newSize}px`;
                    
                    // Nueva duración
                    const newDuration = Math.random() * 10 + 5;
                    coin.style.animationDuration = `${newDuration}s`;
                    
                    // Posicionar fuera de la pantalla de nuevo
                    coin.style.top = `-${newSize}px`;
                });
            }
        });
    </script>
</body>

</html>