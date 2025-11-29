<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Caracterización de Proyectos - Inicio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    body {
        font-family: 'Inter', sans-serif;
    }

    .hero-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }

    .hero-gradient::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="%23ffffff" fill-opacity="0.05" points="0,1000 1000,0 1000,1000"/></svg>');
        background-size: cover;
    }

    .feature-card {
        transition: all 0.3s ease;
        border-left: 4px solid;
        background: white;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .btn-characterize {
        background: linear-gradient(135deg, #10b981, #059669);
        transition: all 0.3s ease;
        padding: 1rem 2rem;
        font-size: 1.125rem;
        font-weight: 600;
    }

    .btn-characterize:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
    }

    .btn-login {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    }

    .cynefin-badge {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .step-indicator {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .fade-in {
        animation: fadeIn 0.8s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .pulse-glow {
        animation: pulseGlow 2s infinite;
    }

    @keyframes pulseGlow {

        0%,
        100% {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
        }

        50% {
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.8);
        }
    }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-project-diagram text-blue-600 text-2xl mr-3"></i>
                        <span class="text-xl font-bold text-gray-900">CaracterizadorPro</span>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="#como-funciona"
                            class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md font-medium transition">¿Cómo
                            Funciona?</a>
                        <a href="#beneficios"
                            class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md font-medium transition">Beneficios</a>
                        <a href="#framework"
                            class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md font-medium transition">Framework
                            Cynefin</a>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="?page=login" class="btn-login text-white px-6 py-2 rounded-lg font-semibold">
                        <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient text-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 relative z-10">
            <div class="text-center">
                <span class="cynefin-badge inline-block mb-6">
                    <i class="fas fa-shield-alt mr-2"></i>Basado en Framework Cynefin
                </span>

                <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                    Caracterización Inteligente de
                    <span class="text-yellow-300">Proyectos Software</span>
                </h1>

                <p class="text-xl md:text-2xl mb-8 text-blue-100 max-w-3xl mx-auto leading-relaxed">
                    Identifica la complejidad de tus proyectos, aplica las estrategias correctas
                    y maximiza tus probabilidades de éxito con nuestro sistema especializado.
                </p>

                <!-- Botón Principal de Caracterización -->
                <div class="mb-12">
                    <a href="?page=register"
                        class="btn-characterize text-white rounded-lg inline-flex items-center pulse-glow">
                        <i class="fas fa-play-circle mr-3 text-xl"></i>
                        Comenzar Caracterización
                    </a>
                    <p class="text-blue-200 mt-3 text-sm">
                        Registro gratuito • Análisis inmediato • Recomendaciones personalizadas
                    </p>
                </div>

                <!-- Mini Dashboard Preview -->
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20 max-w-4xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                        <div>
                            <i class="fas fa-bolt text-3xl text-yellow-300 mb-3"></i>
                            <div class="text-2xl font-bold">2 Minutos</div>
                            <div class="text-blue-100 text-sm">Para caracterizar</div>
                        </div>
                        <div>
                            <i class="fas fa-chart-pie text-3xl text-green-300 mb-3"></i>
                            <div class="text-2xl font-bold">5 Dimensiones</div>
                            <div class="text-blue-100 text-sm">De análisis</div>
                        </div>
                        <div>
                            <i class="fas fa-lightbulb text-3xl text-purple-300 mb-3"></i>
                            <div class="text-2xl font-bold">10+ Estrategias</div>
                            <div class="text-blue-100 text-sm">Recomendadas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cómo Funciona Section -->
    <section id="como-funciona" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Caracteriza tu Proyecto en 3 Pasos
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Nuestro proceso simplificado te guía paso a paso para entender la complejidad de tu proyecto
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Paso 1 -->
                <div class="feature-card rounded-xl p-6 border-l-4 border-blue-500">
                    <div class="flex items-start mb-4">
                        <div class="step-indicator">1</div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Información Básica</h3>
                            <p class="text-gray-600">
                                Describe tu proyecto: nombre, equipo, dominio del problema y contexto general
                            </p>
                        </div>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3 mt-4">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        <span class="text-sm text-blue-700">Datos esenciales del proyecto</span>
                    </div>
                </div>

                <!-- Paso 2 -->
                <div class="feature-card rounded-xl p-6 border-l-4 border-green-500">
                    <div class="flex items-start mb-4">
                        <div class="step-indicator">2</div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Triple Restricción</h3>
                            <p class="text-gray-600">
                                Identifica los factores fijos: tiempo, alcance, costo y tipo de contrato
                            </p>
                        </div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3 mt-4">
                        <i class="fas fa-balance-scale text-green-500 mr-2"></i>
                        <span class="text-sm text-green-700">Factores críticos de gestión</span>
                    </div>
                </div>

                <!-- Paso 3 -->
                <div class="feature-card rounded-xl p-6 border-l-4 border-purple-500">
                    <div class="flex items-start mb-4">
                        <div class="step-indicator">3</div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Complejidad y Estrategias</h3>
                            <p class="text-gray-600">
                                Analiza factores de complejidad y recibe estrategias personalizadas
                            </p>
                        </div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-3 mt-4">
                        <i class="fas fa-chess-knight text-purple-500 mr-2"></i>
                        <span class="text-sm text-purple-700">Recomendaciones accionables</span>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="text-center mt-12">
                <a href="?page=register" class="btn-characterize text-white rounded-lg inline-flex items-center">
                    <i class="fas fa-rocket mr-3 text-xl"></i>
                    Comenzar Mi Caracterización
                </a>
            </div>
        </div>
    </section>

    <!-- Beneficios Section -->
    <section id="beneficios" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Beneficios de la Caracterización
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Mejora la toma de decisiones y aumenta las probabilidades de éxito de tus proyectos
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                    <i class="fas fa-bullseye text-3xl text-blue-600 mb-4"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Enfoque Preciso</h3>
                    <p class="text-gray-600 text-sm">
                        Aplica las metodologías correctas según la complejidad real de tu proyecto
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                    <i class="fas fa-clock text-3xl text-green-600 mb-4"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Ahorro de Tiempo</h3>
                    <p class="text-gray-600 text-sm">
                        Evita enfoques incorrectos que generan retrasos y sobrecostos
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                    <i class="fas fa-chart-line text-3xl text-purple-600 mb-4"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Mejores Resultados</h3>
                    <p class="text-gray-600 text-sm">
                        Proyectos entregados a tiempo, dentro del presupuesto y con calidad
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                    <i class="fas fa-graduation-cap text-3xl text-orange-600 mb-4"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Aprendizaje Continuo</h3>
                    <p class="text-gray-600 text-sm">
                        Base de conocimiento de proyectos anteriores para mejorar continuamente
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Framework Cynefin Section -->
    <section id="framework" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="cynefin-badge inline-block mb-4">Framework Cynefin</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                        Basado en el Framework de Complejidad Cynefin
                    </h2>
                    <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                        Nuestro sistema utiliza el reconocido framework Cynefin para clasificar proyectos
                        en cinco dominios de complejidad, permitiendo aplicar las estrategias de gestión
                        más efectivas para cada contexto.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-gray-700">Identifica el dominio correcto de tu proyecto</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-gray-700">Aplica estrategias probadas para cada contexto</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-gray-700">Evita errores comunes de gestión</span>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-8 text-white text-center">
                    <i class="fas fa-compass text-6xl mb-6"></i>
                    <h3 class="text-2xl font-bold mb-4">Domina la Complejidad</h3>
                    <p class="text-purple-100 mb-6">
                        El framework Cynefin te ayuda a navegar en entornos complejos y tomar mejores decisiones
                    </p>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-white/20 rounded-lg p-3">
                            <div class="font-bold">Obvio</div>
                            <div>Causa y efecto evidentes</div>
                        </div>
                        <div class="bg-white/20 rounded-lg p-3">
                            <div class="font-bold">Complicado</div>
                            <div>Análisis experto requerido</div>
                        </div>
                        <div class="bg-white/20 rounded-lg p-3">
                            <div class="font-bold">Complejo</div>
                            <div>Emergencia y experimentación</div>
                        </div>
                        <div class="bg-white/20 rounded-lg p-3">
                            <div class="font-bold">Caótico</div>
                            <div>Actuar inmediatamente</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final Call to Action -->
    <section class="py-16 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">
                ¿Listo para Caracterizar tu Proyecto?
            </h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Únete a decenas de organizaciones que ya están tomando mejores decisiones
                basadas en la caracterización inteligente de proyectos
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="?page=register"
                    class="btn-characterize text-white rounded-lg inline-flex items-center justify-center">
                    <i class="fas fa-play mr-3"></i>
                    Comenzar Caracterización Gratis
                </a>
                <a href="?page=login"
                    class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Ya Tengo Cuenta
                </a>
            </div>
            <p class="text-blue-200 mt-4 text-sm">
                Registro en 30 segundos • Sin tarjeta de crédito • Acceso inmediato
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-project-diagram text-blue-400 text-2xl mr-3"></i>
                        <span class="text-xl font-bold">CaracterizadorPro</span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        Sistema especializado en caracterización de proyectos software basado en el framework Cynefin.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="?page=login" class="hover:text-white transition">Iniciar Sesión</a></li>
                        <li><a href="?page=register" class="hover:text-white transition">Registrarse</a></li>
                        <li><a href="#como-funciona" class="hover:text-white transition">¿Cómo Funciona?</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Recursos</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#framework" class="hover:text-white transition">Framework Cynefin</a></li>
                        <li><a href="#beneficios" class="hover:text-white transition">Beneficios</a></li>
                        <li><a href="#" class="hover:text-white transition">Documentación</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contacto</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            soporte@caracterizadorpro.com
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-globe mr-2"></i>
                            www.caracterizadorpro.com
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; 2024 CaracterizadorPro. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
    // Smooth scroll para los enlaces internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Animación simple al hacer scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observar elementos para animación
    document.querySelectorAll('.feature-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
    </script>
    <!-- Code injected by live-server -->
    <script>
    // <![CDATA[  <-- For SVG support
    if ('WebSocket' in window) {
        (function() {
            function refreshCSS() {
                var sheets = [].slice.call(document.getElementsByTagName("link"));
                var head = document.getElementsByTagName("head")[0];
                for (var i = 0; i < sheets.length; ++i) {
                    var elem = sheets[i];
                    var parent = elem.parentElement || head;
                    parent.removeChild(elem);
                    var rel = elem.rel;
                    if (elem.href && typeof rel != "string" || rel.length == 0 || rel.toLowerCase() ==
                        "stylesheet") {
                        var url = elem.href.replace(/(&|\?)_cacheOverride=\d+/, '');
                        elem.href = url + (url.indexOf('?') >= 0 ? '&' : '?') + '_cacheOverride=' + (new Date()
                            .valueOf());
                    }
                    parent.appendChild(elem);
                }
            }
            var protocol = window.location.protocol === 'http:' ? 'ws://' : 'wss://';
            var address = protocol + window.location.host + window.location.pathname + '/ws';
            var socket = new WebSocket(address);
            socket.onmessage = function(msg) {
                if (msg.data == 'reload') window.location.reload();
                else if (msg.data == 'refreshcss') refreshCSS();
            };
            if (sessionStorage && !sessionStorage.getItem('IsThisFirstTime_Log_From_LiveServer')) {
                console.log('Live reload enabled.');
                sessionStorage.setItem('IsThisFirstTime_Log_From_LiveServer', true);
            }
        })();
    } else {
        console.error('Upgrade your browser. This Browser is NOT supported WebSocket for Live-Reloading.');
    }
    // ]]>
    </script>
</body>

</html>