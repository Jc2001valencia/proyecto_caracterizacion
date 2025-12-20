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

    /* Estilos para el selector de idioma */
    .language-selector {
        background: white;
        border: 1px solid #d1d5db;
        color: #374151;
        padding: 0.4rem 2rem 0.4rem 0.8rem;
        border-radius: 0.375rem;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
    }

    .language-selector:hover {
        border-color: #3b82f6;
        box-shadow: 0 0 0 1px #3b82f6;
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
                        <span class="text-xl font-bold text-gray-900" id="logo-text">CaracterizadorPro</span>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="#como-funciona"
                            class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md font-medium transition"
                            id="nav-how">
                            ¿Cómo funciona?
                        </a>
                        <a href="#beneficios"
                            class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md font-medium transition"
                            id="nav-benefits">
                            Beneficios
                        </a>
                        <a href="#framework"
                            class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md font-medium transition"
                            id="nav-framework">
                            Framework Cynefin
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Selector de Idioma -->
                    <select id="language-select" class="language-selector">
                        <option value="es">🇪🇸 Español</option>
                        <option value="en">🇬🇧 English</option>
                    </select>

                    <a href="?page=login" class="btn-login text-white px-6 py-2 rounded-lg font-semibold"
                        id="nav-login">
                        <i class="fas fa-sign-in-alt mr-2"></i><span>Iniciar sesión</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient text-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 relative z-10">
            <div class="text-center">
                <span class="cynefin-badge inline-block mb-6" id="hero-badge">
                    <i class="fas fa-shield-alt mr-2"></i>Basado en el framework Cynefin
                </span>

                <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                    <span id="hero-title1">Caracterización inteligente de</span><br>
                    <span class="text-yellow-300" id="hero-title2">proyectos de software</span>
                </h1>

                <p class="text-xl md:text-2xl mb-8 text-blue-100 max-w-3xl mx-auto leading-relaxed" id="hero-subtitle">
                    Identifica la complejidad de tus proyectos, aplica las estrategias adecuadas
                    y maximiza tus probabilidades de éxito con nuestro sistema especializado.
                </p>

                <!-- Botón Principal de Caracterización -->
                <div class="mb-12">
                    <a href="?page=register"
                        class="btn-characterize text-white rounded-lg inline-flex items-center pulse-glow"
                        id="hero-button">
                        <i class="fas fa-play-circle mr-3 text-xl"></i>
                        <span>Comenzar caracterización</span>
                    </a>
                    <p class="text-blue-200 mt-3 text-sm" id="hero-features">
                        Registro gratuito • Análisis inmediato • Recomendaciones personalizadas
                    </p>
                </div>

                <!-- Mini Dashboard Preview -->
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20 max-w-4xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                        <div>
                            <i class="fas fa-bolt text-3xl text-yellow-300 mb-3"></i>
                            <div class="text-2xl font-bold" id="hero-time">2 minutos</div>
                            <div class="text-blue-100 text-sm" id="hero-time-desc">Para caracterizar</div>
                        </div>
                        <div>
                            <i class="fas fa-chart-pie text-3xl text-green-300 mb-3"></i>
                            <div class="text-2xl font-bold" id="hero-dimensions">5 dimensiones</div>
                            <div class="text-blue-100 text-sm" id="hero-dimensions-desc">De análisis</div>
                        </div>
                        <div>
                            <i class="fas fa-lightbulb text-3xl text-purple-300 mb-3"></i>
                            <div class="text-2xl font-bold" id="hero-strategies">10+ estrategias</div>
                            <div class="text-blue-100 text-sm" id="hero-strategies-desc">Recomendadas</div>
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
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4" id="how-title">
                    Caracteriza tu proyecto en 3 pasos simples
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto" id="how-subtitle">
                    Nuestro proceso simplificado te guía paso a paso para comprender la complejidad de tu proyecto
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Paso 1 -->
                <div class="feature-card rounded-xl p-6 border-l-4 border-blue-500">
                    <div class="flex items-start mb-4">
                        <div class="step-indicator">1</div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2" id="step1-title">Información básica</h3>
                            <p class="text-gray-600" id="step1-desc">
                                Describe tu proyecto: nombre, equipo, dominio del problema y contexto general.
                            </p>
                        </div>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3 mt-4">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        <span class="text-sm text-blue-700" id="step1-badge">Datos esenciales del proyecto</span>
                    </div>
                </div>

                <!-- Paso 2 -->
                <div class="feature-card rounded-xl p-6 border-l-4 border-green-500">
                    <div class="flex items-start mb-4">
                        <div class="step-indicator">2</div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2" id="step2-title">Triple restricción</h3>
                            <p class="text-gray-600" id="step2-desc">
                                Identifica los factores fijos: tiempo, alcance, costo y tipo de contrato.
                            </p>
                        </div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3 mt-4">
                        <i class="fas fa-balance-scale text-green-500 mr-2"></i>
                        <span class="text-sm text-green-700" id="step2-badge">Factores críticos de gestión</span>
                    </div>
                </div>

                <!-- Paso 3 -->
                <div class="feature-card rounded-xl p-6 border-l-4 border-purple-500">
                    <div class="flex items-start mb-4">
                        <div class="step-indicator">3</div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2" id="step3-title">Complejidad y estrategias
                            </h3>
                            <p class="text-gray-600" id="step3-desc">
                                Analiza factores de complejidad y recibe estrategias personalizadas.
                            </p>
                        </div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-3 mt-4">
                        <i class="fas fa-chess-knight text-purple-500 mr-2"></i>
                        <span class="text-sm text-purple-700" id="step3-badge">Recomendaciones accionables</span>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="text-center mt-12">
                <a href="?page=register" class="btn-characterize text-white rounded-lg inline-flex items-center"
                    id="how-button">
                    <i class="fas fa-rocket mr-3 text-xl"></i>
                    <span>Comenzar mi caracterización</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Beneficios Section -->
    <section id="beneficios" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4" id="benefits-title">
                    Beneficios de la caracterización
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto" id="benefits-subtitle">
                    Mejora la toma de decisiones y aumenta las probabilidades de éxito de tus proyectos
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                    <i class="fas fa-bullseye text-3xl text-blue-600 mb-4"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-2" id="benefit1-title">Enfoque preciso</h3>
                    <p class="text-gray-600 text-sm" id="benefit1-desc">
                        Aplica las metodologías correctas según la complejidad real de tu proyecto.
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                    <i class="fas fa-clock text-3xl text-green-600 mb-4"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-2" id="benefit2-title">Ahorro de tiempo</h3>
                    <p class="text-gray-600 text-sm" id="benefit2-desc">
                        Evita enfoques incorrectos que generan retrasos y sobrecostos innecesarios.
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                    <i class="fas fa-chart-line text-3xl text-purple-600 mb-4"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-2" id="benefit3-title">Mejores resultados</h3>
                    <p class="text-gray-600 text-sm" id="benefit3-desc">
                        Proyectos entregados a tiempo, dentro del presupuesto y con la calidad esperada.
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                    <i class="fas fa-graduation-cap text-3xl text-orange-600 mb-4"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-2" id="benefit4-title">Aprendizaje continuo</h3>
                    <p class="text-gray-600 text-sm" id="benefit4-desc">
                        Base de conocimiento de proyectos anteriores para mejorar continuamente.
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
                    <span class="cynefin-badge inline-block mb-4" id="cynefin-badge">Framework Cynefin</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6" id="cynefin-title">
                        Basado en el framework de complejidad Cynefin
                    </h2>
                    <p class="text-lg text-gray-600 mb-6 leading-relaxed" id="cynefin-desc">
                        Nuestro sistema utiliza el reconocido framework Cynefin para clasificar proyectos
                        en cinco dominios de complejidad, permitiendo aplicar las estrategias de gestión
                        más efectivas para cada contexto específico.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-gray-700" id="cynefin-benefit1">Identifica el dominio correcto de tu
                                proyecto</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-gray-700" id="cynefin-benefit2">Aplica estrategias probadas para cada
                                contexto</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-gray-700" id="cynefin-benefit3">Evita errores comunes de gestión de
                                proyectos</span>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-8 text-white text-center">
                    <i class="fas fa-compass text-6xl mb-6"></i>
                    <h3 class="text-2xl font-bold mb-4" id="cynefin-card-title">Domina la complejidad</h3>
                    <p class="text-purple-100 mb-6" id="cynefin-card-desc">
                        El framework Cynefin te ayuda a navegar en entornos complejos y tomar mejores decisiones.
                    </p>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-white/20 rounded-lg p-3">
                            <div class="font-bold" id="domain1">Obvio</div>
                            <div id="domain1-desc">Causa y efecto evidentes</div>
                        </div>
                        <div class="bg-white/20 rounded-lg p-3">
                            <div class="font-bold" id="domain2">Complicado</div>
                            <div id="domain2-desc">Análisis experto requerido</div>
                        </div>
                        <div class="bg-white/20 rounded-lg p-3">
                            <div class="font-bold" id="domain3">Complejo</div>
                            <div id="domain3-desc">Emergencia y experimentación</div>
                        </div>
                        <div class="bg-white/20 rounded-lg p-3">
                            <div class="font-bold" id="domain4">Caótico</div>
                            <div id="domain4-desc">Actuar inmediatamente</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final Call to Action -->
    <section class="py-16 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold mb-6" id="cta-title">
                ¿Listo para caracterizar tu proyecto?
            </h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto" id="cta-subtitle">
                Únete a decenas de organizaciones que ya están tomando mejores decisiones
                basadas en la caracterización inteligente de proyectos.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="?page=register"
                    class="btn-characterize text-white rounded-lg inline-flex items-center justify-center"
                    id="cta-button1">
                    <i class="fas fa-play mr-3"></i>
                    <span>Comenzar caracterización gratis</span>
                </a>
                <a href="?page=login"
                    class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition"
                    id="cta-button2">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    <span>Ya tengo cuenta</span>
                </a>
            </div>
            <p class="text-blue-200 mt-4 text-sm" id="cta-features">
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
                    <p class="text-gray-400 text-sm" id="footer-desc">
                        Sistema especializado en caracterización de proyectos de software basado en el framework
                        Cynefin.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4" id="footer-links">Enlaces rápidos</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="?page=login" class="hover:text-white transition">Iniciar sesión</a></li>
                        <li><a href="?page=register" class="hover:text-white transition">Registrarse</a></li>
                        <li><a href="#como-funciona" class="hover:text-white transition">¿Cómo funciona?</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4" id="footer-resources">Recursos</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#framework" class="hover:text-white transition">Framework Cynefin</a></li>
                        <li><a href="#beneficios" class="hover:text-white transition">Beneficios</a></li>
                        <li><a href="#" class="hover:text-white transition">Documentación</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4" id="footer-contact">Contacto</h4>
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
                <p id="footer-copyright">&copy; 2024 CaracterizadorPro. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
    // Sistema de traducción completo
    const translations = {
        es: {
            // Navegación
            "logo-text": "CaracterizadorPro",
            "nav-how": "¿Cómo funciona?",
            "nav-benefits": "Beneficios",
            "nav-framework": "Framework Cynefin",
            "nav-login": "Iniciar sesión",

            // Hero Section
            "hero-badge": "Basado en el framework Cynefin",
            "hero-title1": "Caracterización inteligente de",
            "hero-title2": "proyectos de software",
            "hero-subtitle": "Identifica la complejidad de tus proyectos, aplica las estrategias adecuadas y maximiza tus probabilidades de éxito con nuestro sistema especializado.",
            "hero-button": "Comenzar caracterización",
            "hero-features": "Registro gratuito • Análisis inmediato • Recomendaciones personalizadas",
            "hero-time": "2 minutos",
            "hero-time-desc": "Para caracterizar",
            "hero-dimensions": "5 dimensiones",
            "hero-dimensions-desc": "De análisis",
            "hero-strategies": "10+ estrategias",
            "hero-strategies-desc": "Recomendadas",

            // Cómo Funciona
            "how-title": "Caracteriza tu proyecto en 3 pasos simples",
            "how-subtitle": "Nuestro proceso simplificado te guía paso a paso para comprender la complejidad de tu proyecto",
            "step1-title": "Información básica",
            "step1-desc": "Describe tu proyecto: nombre, equipo, dominio del problema y contexto general.",
            "step1-badge": "Datos esenciales del proyecto",
            "step2-title": "Triple restricción",
            "step2-desc": "Identifica los factores fijos: tiempo, alcance, costo y tipo de contrato.",
            "step2-badge": "Factores críticos de gestión",
            "step3-title": "Complejidad y estrategias",
            "step3-desc": "Analiza factores de complejidad y recibe estrategias personalizadas.",
            "step3-badge": "Recomendaciones accionables",
            "how-button": "Comenzar mi caracterización",

            // Beneficios
            "benefits-title": "Beneficios de la caracterización",
            "benefits-subtitle": "Mejora la toma de decisiones y aumenta las probabilidades de éxito de tus proyectos",
            "benefit1-title": "Enfoque preciso",
            "benefit1-desc": "Aplica las metodologías correctas según la complejidad real de tu proyecto.",
            "benefit2-title": "Ahorro de tiempo",
            "benefit2-desc": "Evita enfoques incorrectos que generan retrasos y sobrecostos innecesarios.",
            "benefit3-title": "Mejores resultados",
            "benefit3-desc": "Proyectos entregados a tiempo, dentro del presupuesto y con la calidad esperada.",
            "benefit4-title": "Aprendizaje continuo",
            "benefit4-desc": "Base de conocimiento de proyectos anteriores para mejorar continuamente.",

            // Framework Cynefin
            "cynefin-badge": "Framework Cynefin",
            "cynefin-title": "Basado en el framework de complejidad Cynefin",
            "cynefin-desc": "Nuestro sistema utiliza el reconocido framework Cynefin para clasificar proyectos en cinco dominios de complejidad, permitiendo aplicar las estrategias de gestión más efectivas para cada contexto específico.",
            "cynefin-benefit1": "Identifica el dominio correcto de tu proyecto",
            "cynefin-benefit2": "Aplica estrategias probadas para cada contexto",
            "cynefin-benefit3": "Evita errores comunes de gestión de proyectos",
            "cynefin-card-title": "Domina la complejidad",
            "cynefin-card-desc": "El framework Cynefin te ayuda a navegar en entornos complejos y tomar mejores decisiones.",
            "domain1": "Obvio",
            "domain1-desc": "Causa y efecto evidentes",
            "domain2": "Complicado",
            "domain2-desc": "Análisis experto requerido",
            "domain3": "Complejo",
            "domain3-desc": "Emergencia y experimentación",
            "domain4": "Caótico",
            "domain4-desc": "Actuar inmediatamente",

            // Call to Action
            "cta-title": "¿Listo para caracterizar tu proyecto?",
            "cta-subtitle": "Únete a decenas de organizaciones que ya están tomando mejores decisiones basadas en la caracterización inteligente de proyectos.",
            "cta-button1": "Comenzar caracterización gratis",
            "cta-button2": "Ya tengo cuenta",
            "cta-features": "Registro en 30 segundos • Sin tarjeta de crédito • Acceso inmediato",

            // Footer
            "footer-desc": "Sistema especializado en caracterización de proyectos de software basado en el framework Cynefin.",
            "footer-links": "Enlaces rápidos",
            "footer-resources": "Recursos",
            "footer-contact": "Contacto",
            "footer-copyright": "© 2024 CaracterizadorPro. Todos los derechos reservados."
        },
        en: {
            // Navigation
            "logo-text": "CaracterizadorPro",
            "nav-how": "How It Works",
            "nav-benefits": "Benefits",
            "nav-framework": "Cynefin Framework",
            "nav-login": "Log In",

            // Hero Section
            "hero-badge": "Based on Cynefin Framework",
            "hero-title1": "Intelligent characterization of",
            "hero-title2": "software projects",
            "hero-subtitle": "Identify the complexity of your projects, apply the right strategies, and maximize your chances of success with our specialized system.",
            "hero-button": "Start characterization",
            "hero-features": "Free registration • Immediate analysis • Personalized recommendations",
            "hero-time": "2 minutes",
            "hero-time-desc": "To characterize",
            "hero-dimensions": "5 dimensions",
            "hero-dimensions-desc": "Of analysis",
            "hero-strategies": "10+ strategies",
            "hero-strategies-desc": "Recommended",

            // How It Works
            "how-title": "Characterize your project in 3 simple steps",
            "how-subtitle": "Our simplified process guides you step by step to understand your project's complexity",
            "step1-title": "Basic information",
            "step1-desc": "Describe your project: name, team, problem domain, and general context.",
            "step1-badge": "Essential project data",
            "step2-title": "Triple constraint",
            "step2-desc": "Identify fixed factors: time, scope, cost, and contract type.",
            "step2-badge": "Critical management factors",
            "step3-title": "Complexity and strategies",
            "step3-desc": "Analyze complexity factors and receive personalized strategies.",
            "step3-badge": "Actionable recommendations",
            "how-button": "Start my characterization",

            // Benefits
            "benefits-title": "Benefits of characterization",
            "benefits-subtitle": "Improve decision-making and increase your project success rates",
            "benefit1-title": "Precise focus",
            "benefit1-desc": "Apply the right methodologies according to your project's actual complexity.",
            "benefit2-title": "Time saving",
            "benefit2-desc": "Avoid incorrect approaches that cause delays and unnecessary cost overruns.",
            "benefit3-title": "Better results",
            "benefit3-desc": "Projects delivered on time, within budget, and with expected quality.",
            "benefit4-title": "Continuous learning",
            "benefit4-desc": "Knowledge base from previous projects to continuously improve.",

            // Cynefin Framework
            "cynefin-badge": "Cynefin Framework",
            "cynefin-title": "Based on the Cynefin complexity framework",
            "cynefin-desc": "Our system uses the renowned Cynefin framework to classify projects into five complexity domains, allowing application of the most effective management strategies for each specific context.",
            "cynefin-benefit1": "Identify the correct domain of your project",
            "cynefin-benefit2": "Apply proven strategies for each context",
            "cynefin-benefit3": "Avoid common project management errors",
            "cynefin-card-title": "Master complexity",
            "cynefin-card-desc": "The Cynefin framework helps you navigate complex environments and make better decisions.",
            "domain1": "Obvious",
            "domain1-desc": "Cause and effect evident",
            "domain2": "Complicated",
            "domain2-desc": "Expert analysis required",
            "domain3": "Complex",
            "domain3-desc": "Emergence and experimentation",
            "domain4": "Chaotic",
            "domain4-desc": "Act immediately",

            // Call to Action
            "cta-title": "Ready to characterize your project?",
            "cta-subtitle": "Join dozens of organizations already making better decisions based on intelligent project characterization.",
            "cta-button1": "Start free characterization",
            "cta-button2": "I already have an account",
            "cta-features": "30-second registration • No credit card required • Immediate access",

            // Footer
            "footer-desc": "Specialized system for software project characterization based on the Cynefin framework.",
            "footer-links": "Quick Links",
            "footer-resources": "Resources",
            "footer-contact": "Contact",
            "footer-copyright": "© 2024 CaracterizadorPro. All rights reserved."
        }
    };

    // Función para cambiar el idioma
    function changeLanguage(lang) {
        // Cambiar el atributo lang del HTML
        document.documentElement.lang = lang;

        // Cambiar el título de la página
        document.title = lang === 'es' ?
            "Sistema de Caracterización de Proyectos - Inicio" :
            "Project Characterization System - Home";

        // Actualizar todos los textos traducibles
        Object.keys(translations[lang]).forEach(key => {
            const element = document.getElementById(key);
            if (element) {
                // Si es un span dentro de un botón, actualizar el span
                if (element.tagName === 'A' && element.querySelector('span')) {
                    element.querySelector('span').textContent = translations[lang][key];
                } else {
                    element.textContent = translations[lang][key];
                }
            }
        });

        // Guardar la preferencia de idioma
        localStorage.setItem('preferredLanguage', lang);

        // Actualizar el selector
        document.getElementById('language-select').value = lang;
    }

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

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar el selector de idioma
        const languageSelect = document.getElementById('language-select');

        // Cargar idioma guardado o usar español por defecto
        const savedLanguage = localStorage.getItem('preferredLanguage') || 'es';
        changeLanguage(savedLanguage);

        // Evento para cambiar idioma
        languageSelect.addEventListener('change', function() {
            changeLanguage(this.value);
        });
    });
    </script>
</body>

</html>