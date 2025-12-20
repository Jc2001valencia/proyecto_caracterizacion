<?php
// AuthView.php - SOLO estas líneas al inicio:

// No usar session_start() aquí - ya se inició en index.php

// Determinar página con lógica especial
if (isset($_SESSION['forzar_2fa'])) {
    $current_page = '2fa';
    unset($_SESSION['forzar_2fa']); // Limpiar para que no se repita
} else {
    $current_page = isset($_GET['page']) ? $_GET['page'] : 'login';
}

// Si hay usuario_temp pero estamos en login/register, forzar 2fa
if (isset($_SESSION['usuario_temp']) && ($current_page === 'login' || $current_page === 'register')) {
    $current_page = '2fa';
}

// CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Configuración inicial del idioma
if (!isset($_SESSION['idioma'])) {
    $_SESSION['idioma'] = $_COOKIE['idioma'] ?? 'es';
}

// Si hay cambio de idioma por GET
if (isset($_GET['idioma']) && in_array($_GET['idioma'], ['es', 'en'])) {
    $_SESSION['idioma'] = $_GET['idioma'];
    setcookie('idioma', $_GET['idioma'], time() + (86400 * 30), "/");
}

$idioma = $_SESSION['idioma'];

$textos = [
    'es' => [
        'titulo_sistema' => 'Sistema de caracterización',
        'subtitulo_sistema' => 'Framework Cynefin',
        'verificacion_dos_pasos' => 'Verificación en dos pasos',
        'codigo_enviado' => 'Hemos enviado un código de verificación de 6 dígitos a tu email',
        'revisar_email' => 'Revisa tu bandeja de entrada y la carpeta de spam',
        'codigo_expira' => 'El código expira en 15 minutos',
        'verificar' => 'Verificar',
        'volver_login' => 'Volver al inicio de sesión',
        'iniciar_sesion' => 'Iniciar sesión',
        'registrarse' => 'Registrarse',
        'usuario_email' => 'Usuario o email',
        'contraseña' => 'Contraseña',
        'recordarme' => 'Recordarme',
        'olvido_contrasena' => '¿Olvidó su contraseña?',
        'ingresar_sistema' => 'Ingresar al sistema',
        'datos_personales' => 'Datos personales',
        'nombre' => 'Nombre',
        'apellido' => 'Apellido',
        'email' => 'Email',
        'telefono_opcional' => 'Teléfono (opcional)',
        'nombre_usuario' => 'Nombre de usuario',
        'crear_contrasena' => 'Crear contraseña',
        'confirmar_contrasena' => 'Confirmar contraseña',
        'terminos_condiciones' => 'Acepto los términos y condiciones',
        'crear_usuario' => 'Crear usuario',
        'crear_organizacion' => 'Crear tu organización',
        'usuario_registrado_exito' => '¡Usuario registrado exitosamente!',
        'crear_organizacion_texto' => 'Ahora crea tu organización para empezar a usar el sistema.',
        'datos_organizacion' => 'Datos de la organización',
        'nombre_organizacion' => 'Nombre de la organización',
        'descripcion_organizacion' => 'Descripción de la organización (opcional)',
        'telefono_organizacion' => 'Teléfono (opcional)',
        'email_organizacion' => 'Email organización (opcional)',
        'direccion_organizacion' => 'Dirección (opcional)',
        'crear_organizacion_boton' => 'Crear organización',
        'recuperar_contrasena' => 'Recuperar contraseña',
        'recuperar_instrucciones' => 'Ingrese su correo electrónico y le enviaremos un enlace para restablecer su contraseña.',
        'cancelar' => 'Cancelar',
        'enviar_enlace' => 'Enviar enlace',
        'idioma' => 'Idioma',
        'espanol' => 'Español',
        'ingles' => 'English',
        'derechos_reservados' => '© 2024 Sistema de caracterización. Todos los derechos reservados.'
    ],
    'en' => [
        'titulo_sistema' => 'Characterization System',
        'subtitulo_sistema' => 'Cynefin Framework',
        'verificacion_dos_pasos' => 'Two-Step Verification',
        'codigo_enviado' => 'We have sent a 6-digit verification code to your email',
        'revisar_email' => 'Check your inbox and spam folder',
        'codigo_expira' => 'The code expires in 15 minutes',
        'verificar' => 'Verify',
        'volver_login' => 'Back to login',
        'iniciar_sesion' => 'Login',
        'registrarse' => 'Register',
        'usuario_email' => 'Username or email',
        'contraseña' => 'Password',
        'recordarme' => 'Remember me',
        'olvido_contrasena' => 'Forgot your password?',
        'ingresar_sistema' => 'Enter system',
        'datos_personales' => 'Personal data',
        'nombre' => 'First name',
        'apellido' => 'Last name',
        'email' => 'Email',
        'telefono_opcional' => 'Phone (optional)',
        'nombre_usuario' => 'Username',
        'crear_contrasena' => 'Create password',
        'confirmar_contrasena' => 'Confirm password',
        'terminos_condiciones' => 'I accept the terms and conditions',
        'crear_usuario' => 'Create user',
        'crear_organizacion' => 'Create your organization',
        'usuario_registrado_exito' => 'User registered successfully!',
        'crear_organizacion_texto' => 'Now create your organization to start using the system.',
        'datos_organizacion' => 'Organization data',
        'nombre_organizacion' => 'Organization name',
        'descripcion_organizacion' => 'Organization description (optional)',
        'telefono_organizacion' => 'Phone (optional)',
        'email_organizacion' => 'Organization email (optional)',
        'direccion_organizacion' => 'Address (optional)',
        'crear_organizacion_boton' => 'Create organization',
        'recuperar_contrasena' => 'Recover password',
        'recuperar_instrucciones' => 'Enter your email and we will send you a link to reset your password.',
        'cancelar' => 'Cancel',
        'enviar_enlace' => 'Send link',
        'idioma' => 'Language',
        'espanol' => 'Spanish',
        'ingles' => 'English',
        'derechos_reservados' => '© 2024 Characterization System. All rights reserved.'
    ]
];

$t = $textos[$idioma] ?? $textos['es'];

$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
unset($_SESSION['form_data']);
?>
<!doctype html>
<html lang="<?= $idioma ?>">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Autenticación - <?= $t['titulo_sistema'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card-entrance {
        animation: fadeIn 0.5s ease-out;
    }

    .tab-active {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .password-strength-weak {
        background-color: #ef4444 !important;
        width: 25% !important;
    }

    .password-strength-medium {
        background-color: #f59e0b !important;
        width: 65% !important;
    }

    .password-strength-strong {
        background-color: #10b981 !important;
        width: 100% !important;
    }

    .shake {
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        75% {
            transform: translateX(5px);
        }
    }

    .pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
        }

        50% {
            box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
        }
    }

    .language-selector {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 10;
    }

    .language-button {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        color: white;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .language-button:hover {
        background: rgba(255, 255, 255, 0.25);
        border-color: rgba(255, 255, 255, 0.3);
    }

    .language-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        margin-top: 0.5rem;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        min-width: 120px;
        overflow: hidden;
        display: none;
    }

    .language-dropdown.show {
        display: block;
        animation: fadeIn 0.2s ease-out;
    }

    .language-option {
        padding: 0.75rem 1rem;
        color: #374151;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }

    .language-option:hover {
        background: #f3f4f6;
    }

    .language-option.active {
        background: #3b82f6;
        color: white;
    }

    /* Clase para elementos traducibles */
    .translatable {
        transition: all 0.3s ease;
    }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-600 to-purple-700 min-h-screen">
    <div class="min-h-screen flex items-center justify-center p-4">
        <!-- Card Principal -->
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden card-entrance relative">

            <!-- SELECTOR DE IDIOMA DISCRETO -->
            <div class="language-selector">
                <button class="language-button" id="language-toggle">
                    <i class="fas fa-globe"></i>
                    <span><?= strtoupper($idioma) ?></span>
                    <i class="fas fa-chevron-down text-xs ml-1"></i>
                </button>
                <div class="language-dropdown" id="language-dropdown">
                    <button type="button" class="language-option change-language" data-lang="es">
                        <span class="mr-2">🇪🇸</span> Español
                    </button>
                    <button type="button" class="language-option change-language" data-lang="en">
                        <span class="mr-2">🇬🇧</span> English
                    </button>
                </div>
            </div>

            <!-- Header con Logo -->
            <div class="text-center p-6 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 pulse">
                    <i class="fas fa-project-diagram text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold translatable" data-key="titulo_sistema"><?= $t['titulo_sistema'] ?></h1>
                <p class="text-blue-100 mt-2 translatable" data-key="subtitulo_sistema"><?= $t['subtitulo_sistema'] ?>
                </p>
            </div>

            <!-- Mensajes de éxito/error -->
            <?php if (isset($error) && !empty($error)): ?>
            <div
                class="mx-4 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php endif; ?>

            <?php if (isset($success) && !empty($success)): ?>
            <div
                class="mx-4 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span><?= htmlspecialchars($success) ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php endif; ?>

            <!-- Sección de Verificación 2FA -->
            <div id="seccion-2fa" class="<?= $current_page === '2fa' ? '' : 'hidden' ?>">
                <div class="mx-4 mt-4 bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-blue-800 translatable" data-key="verificacion_dos_pasos">
                            <?= $t['verificacion_dos_pasos'] ?></h3>
                        <p class="text-blue-600 mt-2 text-sm translatable" data-key="codigo_enviado">
                            <?= $t['codigo_enviado'] ?>
                        </p>
                        <p class="text-blue-500 text-xs mt-1 translatable" data-key="revisar_email">
                            <?= $t['revisar_email'] ?>
                        </p>

                        <form id="form-2fa" method="POST" action="index.php" class="mt-4 space-y-4">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="action" value="verify2fa">

                            <div class="relative">
                                <input type="text" name="codigo" maxlength="6"
                                    class="w-full p-4 text-center text-2xl font-mono border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="000000" required pattern="[0-9]{6}" autocomplete="one-time-code"
                                    inputmode="numeric" autofocus />
                                <i
                                    class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                            </div>

                            <div class="text-center">
                                <p class="text-blue-500 text-xs translatable" data-key="codigo_expira">
                                    <?= $t['codigo_expira'] ?>
                                </p>
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" id="btn-2fa"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition duration-300 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105">
                                    <i class="fas fa-check mr-2"></i>
                                    <span id="2fa-text" class="translatable"
                                        data-key="verificar"><?= $t['verificar'] ?></span>
                                    <div id="2fa-spinner" class="hidden ml-2">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </button>
                                <a href="index.php?action=reenviar2fa" id="btn-renviar-codigo"
                                    class="px-4 bg-gray-500 hover:bg-gray-600 text-white py-3 rounded-lg font-semibold transition duration-300 flex items-center justify-center transform hover:scale-105">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </form>

                        <div class="mt-4 text-center">
                            <a href="index.php?page=login" id="btn-volver-login"
                                class="text-blue-600 hover:text-blue-800 text-sm inline-flex items-center transition duration-200">
                                <i class="fas fa-arrow-left mr-2"></i>
                                <span class="translatable" data-key="volver_login"><?= $t['volver_login'] ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs (ocultos durante 2FA y crear-organizacion) -->
            <div id="seccion-tabs"
                class="<?= ($current_page === '2fa' || $current_page === 'crear-organizacion') ? 'hidden' : 'block' ?>">
                <div class="flex bg-gray-100 p-1 m-4 rounded-lg">
                    <button id="tab-login"
                        class="flex-1 py-3 px-4 rounded-md transition-all duration-300 <?= $current_page === 'login' ? 'tab-active' : 'font-medium text-gray-600 hover:text-gray-800' ?>">
                        <i class="fas fa-sign-in-alt mr-2"></i><span class="translatable"
                            data-key="iniciar_sesion"><?= $t['iniciar_sesion'] ?></span>
                    </button>
                    <button id="tab-register"
                        class="flex-1 py-3 px-4 rounded-md transition-all duration-300 <?= $current_page === 'register' ? 'tab-active' : 'font-medium text-gray-600 hover:text-gray-800' ?>">
                        <i class="fas fa-user-plus mr-2"></i><span class="translatable"
                            data-key="registrarse"><?= $t['registrarse'] ?></span>
                    </button>
                </div>

                <!-- Contenido de los Forms -->
                <div id="seccion-forms" class="p-6 pt-0">
                    <!-- Login Form -->
                    <form id="form-login" method="POST" action="index.php"
                        class="space-y-5 <?= $current_page === 'login' ? '' : 'hidden' ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="login">

                        <div class="relative">
                            <input type="text" name="usuario" id="login-usuario"
                                class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                placeholder="<?= $t['usuario_email'] ?>" required
                                value="<?= isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : '' ?>" />
                            <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>

                        <div class="relative">
                            <input type="password" name="password" id="login-password"
                                class="w-full p-3 pl-10 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                placeholder="<?= $t['contraseña'] ?>" required />
                            <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <button type="button"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition duration-200"
                                onclick="togglePassword('login-password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                        <div class="flex justify-between items-center">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="remember"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition duration-200">
                                <span class="ml-2 text-sm text-gray-600 translatable"
                                    data-key="recordarme"><?= $t['recordarme'] ?></span>
                            </label>
                            <button type="button" id="btn-olvido"
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium transition duration-200">
                                <span class="translatable"
                                    data-key="olvido_contrasena"><?= $t['olvido_contrasena'] ?></span>
                            </button>
                        </div>

                        <button type="submit" id="btn-login"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition duration-300 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            <span id="login-text" class="translatable"
                                data-key="ingresar_sistema"><?= $t['ingresar_sistema'] ?></span>
                            <div id="login-spinner" class="hidden ml-2">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </button>
                    </form>

                    <!-- Registro Form (PASO 1 - SOLO USUARIO) -->
                    <form id="form-register" method="POST" action="index.php"
                        class="space-y-5 <?= $current_page === 'register' ? '' : 'hidden' ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="register">

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-user-circle mr-2 text-blue-600"></i>
                                <span class="translatable"
                                    data-key="datos_personales"><?= $t['datos_personales'] ?></span>
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="relative">
                                    <input type="text" name="nombre" id="register-nombre"
                                        class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                        placeholder="<?= $t['nombre'] ?>" required
                                        value="<?= isset($form_data['nombre']) ? htmlspecialchars($form_data['nombre']) : '' ?>">
                                    <i
                                        class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                </div>

                                <div class="relative">
                                    <input type="text" name="apellido" id="register-apellido"
                                        class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                        placeholder="<?= $t['apellido'] ?>" required
                                        value="<?= isset($form_data['apellido']) ? htmlspecialchars($form_data['apellido']) : '' ?>">
                                    <i
                                        class="fas fa-user-tie absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                </div>
                            </div>

                            <div class="relative mb-4">
                                <input type="email" name="email" id="register-email"
                                    class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                    placeholder="<?= $t['email'] ?>" required
                                    value="<?= isset($form_data['email']) ? htmlspecialchars($form_data['email']) : '' ?>">
                                <i
                                    class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>

                            <div class="relative mb-4">
                                <input type="text" name="telefono" id="register-telefono"
                                    class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                    placeholder="<?= $t['telefono_opcional'] ?>"
                                    value="<?= isset($form_data['telefono']) ? htmlspecialchars($form_data['telefono']) : '' ?>">
                                <i
                                    class="fas fa-phone absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>

                            <div class="relative mb-4">
                                <input type="text" name="usuario" id="register-usuario"
                                    class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                    placeholder="<?= $t['nombre_usuario'] ?>" required
                                    value="<?= isset($form_data['usuario']) ? htmlspecialchars($form_data['usuario']) : '' ?>">
                                <i
                                    class="fas fa-user-tag absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>

                            <div class="relative mb-4">
                                <input type="password" name="contrasena" id="register-password"
                                    class="w-full p-3 pl-10 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                    placeholder="<?= $t['crear_contrasena'] ?>" required
                                    oninput="checkPasswordStrength(this.value)">
                                <i
                                    class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <button type="button"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition duration-200"
                                    onclick="togglePassword('register-password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>

                            <div class="relative">
                                <input type="password" name="confirmar_contrasena" id="confirm-password"
                                    class="w-full p-3 pl-10 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                    placeholder="<?= $t['confirmar_contrasena'] ?>" required>
                                <i
                                    class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <button type="button"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition duration-200"
                                    onclick="togglePassword('confirm-password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>

                            <!-- Indicador de fortaleza de contraseña -->
                            <div id="password-strength" class="h-2 bg-gray-200 rounded-full hidden mt-3">
                                <div id="password-strength-bar" class="h-full rounded-full transition-all duration-300">
                                </div>
                            </div>
                            <div id="password-feedback" class="text-xs text-gray-500 mt-1"></div>

                            <div class="flex items-center mt-4">
                                <input type="checkbox" name="terms" id="register-terms" required
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition duration-200">
                                <span class="ml-2 text-sm text-gray-600 translatable" data-key="terminos_condiciones">
                                    <?= $t['terminos_condiciones'] ?>
                                </span>
                            </div>
                        </div>

                        <button type="submit" id="btn-register"
                            class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition duration-300 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-user-plus mr-2"></i>
                            <span id="register-text" class="translatable"
                                data-key="crear_usuario"><?= $t['crear_usuario'] ?></span>
                            <div id="register-spinner" class="hidden ml-2">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sección Crear Organización (PASO 2) -->
            <div id="seccion-crear-organizacion" class="<?= $current_page === 'crear-organizacion' ? '' : 'hidden' ?>">
                <div class="mx-4 mt-4 p-6">
                    <div class="text-center mb-6">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-building text-green-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 translatable" data-key="crear_organizacion">
                            <?= $t['crear_organizacion'] ?></h3>
                        <p class="text-gray-600 mt-2 text-sm">
                            <span class="translatable"
                                data-key="usuario_registrado_exito"><?= $t['usuario_registrado_exito'] ?></span><br>
                            <span class="translatable"
                                data-key="crear_organizacion_texto"><?= $t['crear_organizacion_texto'] ?></span>
                        </p>
                    </div>

                    <form id="form-crear-organizacion" method="POST" action="index.php" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="register">
                        <input type="hidden" name="paso" value="2">

                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-blue-700 mb-3 flex items-center">
                                <i class="fas fa-building mr-2 text-blue-600"></i>
                                <span class="translatable"
                                    data-key="datos_organizacion"><?= $t['datos_organizacion'] ?></span>
                            </h3>

                            <div class="relative mb-4">
                                <input type="text" name="nombre_organizacion" id="nombre-organizacion"
                                    class="w-full p-3 pl-10 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                    placeholder="<?= $t['nombre_organizacion'] ?> *" required
                                    value="<?= isset($form_data['nombre_organizacion']) ? htmlspecialchars($form_data['nombre_organizacion']) : '' ?>">
                                <i
                                    class="fas fa-building absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                            </div>

                            <div class="relative mb-4">
                                <textarea name="descripcion_organizacion" id="descripcion-organizacion"
                                    class="w-full p-3 pl-10 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                    placeholder="<?= $t['descripcion_organizacion'] ?>"
                                    rows="2"><?= isset($form_data['descripcion_organizacion']) ? htmlspecialchars($form_data['descripcion_organizacion']) : '' ?></textarea>
                                <i class="fas fa-info-circle absolute left-3 top-3 text-blue-400"></i>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="relative">
                                    <input type="text" name="telefono_organizacion" id="telefono-organizacion"
                                        class="w-full p-3 pl-10 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                        placeholder="<?= $t['telefono_organizacion'] ?>"
                                        value="<?= isset($form_data['telefono_organizacion']) ? htmlspecialchars($form_data['telefono_organizacion']) : '' ?>">
                                    <i
                                        class="fas fa-phone absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                                </div>

                                <div class="relative">
                                    <input type="email" name="email_organizacion" id="email-organizacion"
                                        class="w-full p-3 pl-10 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                        placeholder="<?= $t['email_organizacion'] ?>"
                                        value="<?= isset($form_data['email_organizacion']) ? htmlspecialchars($form_data['email_organizacion']) : '' ?>">
                                    <i
                                        class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                                </div>
                            </div>

                            <div class="relative">
                                <input type="text" name="direccion_organizacion" id="direccion-organizacion"
                                    class="w-full p-3 pl-10 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                    placeholder="<?= $t['direccion_organizacion'] ?>"
                                    value="<?= isset($form_data['direccion_organizacion']) ? htmlspecialchars($form_data['direccion_organizacion']) : '' ?>">
                                <i
                                    class="fas fa-map-marker-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" id="btn-crear-organizacion"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition duration-300 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105">
                                <i class="fas fa-check mr-2"></i>
                                <span id="crear-organizacion-text" class="translatable"
                                    data-key="crear_organizacion_boton"><?= $t['crear_organizacion_boton'] ?></span>
                                <div id="crear-organizacion-spinner" class="hidden ml-2">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </button>
                            <a href="index.php?page=login"
                                class="px-4 bg-gray-500 hover:bg-gray-600 text-white py-3 rounded-lg font-semibold transition duration-300 flex items-center justify-center transform hover:scale-105">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                <span class="translatable" data-key="iniciar_sesion"><?= $t['iniciar_sesion'] ?></span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t text-center">
                <p class="text-sm text-gray-600 translatable" data-key="derechos_reservados">
                    <?= $t['derechos_reservados'] ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Modal: Recuperar Contraseña -->
    <div id="modal-recuperar"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="p-6 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-key mr-2 text-blue-600"></i>
                        <span class="translatable"
                            data-key="recuperar_contrasena"><?= $t['recuperar_contrasena'] ?></span>
                    </h3>
                    <button type="button" id="cerrar-recuperar" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            <form id="form-recuperar" class="p-6 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="action" value="solicitarRecuperacion">

                <p class="text-sm text-gray-600 translatable" data-key="recuperar_instrucciones">
                    <?= $t['recuperar_instrucciones'] ?>
                </p>
                <div class="relative">
                    <input type="email" name="email" id="email-recuperar"
                        class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="correo@email.com" required />
                    <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" id="cerrar-recuperar-btn"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-medium translatable"
                        data-key="cancelar">
                        <?= $t['cancelar'] ?>
                    </button>
                    <button type="submit" id="btn-enviar-recuperacion"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium flex items-center transition duration-300 disabled:opacity-50">
                        <i class="fas fa-paper-plane mr-2"></i>
                        <span id="recuperacion-text" class="translatable"
                            data-key="enviar_enlace"><?= $t['enviar_enlace'] ?></span>
                        <div id="recuperacion-spinner" class="hidden ml-2">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // ==========================
    // DICCIONARIOS DE IDIOMAS
    // ==========================
    const textos = {
        es: <?= json_encode($textos['es']) ?>,
        en: <?= json_encode($textos['en']) ?>
    };

    // ==========================
    // GESTIÓN DE IDIOMA
    // ==========================
    let currentLang = '<?= $idioma ?>';

    function changeLanguage(lang) {
        if (!textos[lang] || lang === currentLang) return;

        currentLang = lang;

        // Actualizar todos los elementos traducibles
        document.querySelectorAll('.translatable').forEach(element => {
            const key = element.getAttribute('data-key');
            if (key && textos[lang][key]) {
                element.textContent = textos[lang][key];
            }
        });

        // Actualizar placeholders de inputs
        updateInputPlaceholders(lang);

        // Actualizar botón de idioma
        updateLanguageButton(lang);

        // Guardar preferencia en cookie
        document.cookie = `idioma=${lang}; path=/; max-age=${30*24*60*60}`;

        // Guardar en sesión via AJAX (opcional)
        saveLanguagePreference(lang);

        // Cerrar dropdown
        document.getElementById('language-dropdown').classList.remove('show');

        console.log('Idioma cambiado a:', lang);
    }

    function updateInputPlaceholders(lang) {
        const placeholders = {
            'login-usuario': textos[lang]['usuario_email'],
            'login-password': textos[lang]['contraseña'],
            'register-nombre': textos[lang]['nombre'],
            'register-apellido': textos[lang]['apellido'],
            'register-email': textos[lang]['email'],
            'register-telefono': textos[lang]['telefono_opcional'],
            'register-usuario': textos[lang]['nombre_usuario'],
            'register-password': textos[lang]['crear_contrasena'],
            'confirm-password': textos[lang]['confirmar_contrasena'],
            'nombre-organizacion': textos[lang]['nombre_organizacion'] + ' *',
            'descripcion-organizacion': textos[lang]['descripcion_organizacion'],
            'telefono-organizacion': textos[lang]['telefono_organizacion'],
            'email-organizacion': textos[lang]['email_organizacion'],
            'direccion-organizacion': textos[lang]['direccion_organizacion'],
            'email-recuperar': 'correo@email.com'
        };

        for (const [id, placeholder] of Object.entries(placeholders)) {
            const element = document.getElementById(id);
            if (element) {
                element.placeholder = placeholder;
            }
        }
    }

    function updateLanguageButton(lang) {
        const button = document.getElementById('language-toggle');
        if (button) {
            const span = button.querySelector('span');
            if (span) {
                span.textContent = lang.toUpperCase();
            }
        }
    }

    function saveLanguagePreference(lang) {
        // Enviar preferencia al servidor vía fetch
        const formData = new FormData();
        formData.append('idioma', lang);

        fetch('index.php?action=cambiarIdioma', {
            method: 'POST',
            body: formData
        }).catch(err => console.log('Error guardando idioma:', err));
    }

    // ==========================
    // FUNCIONES UTILITARIAS
    // ==========================
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.parentNode.querySelector('button i');
        input.type = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    }

    function checkPasswordStrength(password) {
        const strengthMeter = document.getElementById('password-strength');
        const strengthBar = document.getElementById('password-strength-bar');
        const feedback = document.getElementById('password-feedback');

        if (!password) {
            strengthMeter.classList.add('hidden');
            feedback.textContent = '';
            return;
        }

        let strength = 0;
        if (password.length >= 6) strength++;
        if (password.length >= 8) strength++;
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;
        if (/[0-9]/.test(password) && /[a-zA-Z]/.test(password)) strength++;

        strengthMeter.classList.remove('hidden');

        let barClass, feedbackText, feedbackColor;
        if (strength <= 1) {
            barClass = 'password-strength-weak';
            feedbackText = currentLang === 'en' ? "Weak password" : "Contraseña débil";
            feedbackColor = 'text-red-500';
        } else if (strength <= 3) {
            barClass = 'password-strength-medium';
            feedbackText = currentLang === 'en' ? "Moderate password" : "Contraseña moderada";
            feedbackColor = 'text-yellow-500';
        } else {
            barClass = 'password-strength-strong';
            feedbackText = currentLang === 'en' ? "Strong password" : "Contraseña fuerte";
            feedbackColor = 'text-green-500';
        }

        strengthBar.className = `h-full rounded-full ${barClass}`;
        feedback.className = `text-xs ${feedbackColor} mt-1`;
        feedback.textContent = feedbackText;
    }

    function showLoading(buttonId, spinnerId, textId) {
        const button = document.getElementById(buttonId);
        const spinner = document.getElementById(spinnerId);
        const text = document.getElementById(textId);

        if (button) button.disabled = true;
        if (spinner) spinner.classList.remove('hidden');
        if (text) {
            const originalText = text.textContent;
            text.setAttribute('data-original', originalText);
            text.textContent = currentLang === 'en' ? "Processing..." : "Procesando...";
        }
    }

    function hideLoading(buttonId, spinnerId, textId) {
        const button = document.getElementById(buttonId);
        const spinner = document.getElementById(spinnerId);
        const text = document.getElementById(textId);

        if (button) button.disabled = false;
        if (spinner) spinner.classList.add('hidden');
        if (text) {
            const originalText = text.getAttribute('data-original');
            if (originalText) {
                text.textContent = originalText;
            }
        }
    }

    function showMessage(message, type = 'error') {
        const alertClass = type === 'error' ?
            'bg-red-100 border-red-400 text-red-700' :
            'bg-green-100 border-green-400 text-green-700';

        const icon = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';

        const messageDiv = document.createElement('div');
        messageDiv.className = `mx-4 mt-4 border px-4 py-3 rounded flex items-center justify-between ${alertClass}`;
        messageDiv.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${icon} mr-2"></i>
                <span>${message}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="ml-4">
                <i class="fas fa-times"></i>
            </button>
        `;

        const container = document.querySelector('.bg-white');
        if (container) {
            container.insertBefore(messageDiv, container.firstChild);
        }

        setTimeout(() => {
            if (messageDiv.parentElement) {
                messageDiv.remove();
            }
        }, 5000);
    }

    function validarFormularioRegistro() {
        const password = document.getElementById('register-password').value;
        const confirmPassword = document.getElementById('confirm-password').value;

        if (password !== confirmPassword) {
            showMessage(currentLang === 'en' ? "Passwords do not match" : "Las contraseñas no coinciden", 'error');
            return false;
        }

        const terms = document.getElementById('register-terms');
        if (!terms.checked) {
            showMessage(currentLang === 'en' ? "You must accept the terms and conditions" :
                "Debes aceptar los términos y condiciones", 'error');
            return false;
        }

        return true;
    }

    // ==========================
    // EVENT LISTENERS
    // ==========================
    document.addEventListener('DOMContentLoaded', () => {
        // Selector de idioma
        const languageToggle = document.getElementById('language-toggle');
        const languageDropdown = document.getElementById('language-dropdown');

        if (languageToggle && languageDropdown) {
            languageToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                languageDropdown.classList.toggle('show');
            });

            document.addEventListener('click', (e) => {
                if (!languageToggle.contains(e.target) && !languageDropdown.contains(e.target)) {
                    languageDropdown.classList.remove('show');
                }
            });

            // Botones para cambiar idioma
            document.querySelectorAll('.change-language').forEach(button => {
                button.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const lang = button.getAttribute('data-lang');
                    changeLanguage(lang);
                });
            });
        }

        // Tabs
        const tabLogin = document.getElementById('tab-login');
        const tabRegister = document.getElementById('tab-register');
        const formLogin = document.getElementById('form-login');
        const formRegister = document.getElementById('form-register');

        if (tabLogin && tabRegister && formLogin && formRegister) {
            tabLogin.addEventListener('click', () => {
                formLogin.classList.remove('hidden');
                formRegister.classList.add('hidden');
                tabLogin.className =
                    'flex-1 py-3 px-4 rounded-md transition-all duration-300 tab-active';
                tabRegister.className =
                    'flex-1 py-3 px-4 rounded-md transition-all duration-300 font-medium text-gray-600 hover:text-gray-800';
                history.replaceState(null, '', '?page=login');
            });

            tabRegister.addEventListener('click', () => {
                formLogin.classList.add('hidden');
                formRegister.classList.remove('hidden');
                tabRegister.className =
                    'flex-1 py-3 px-4 rounded-md transition-all duration-300 tab-active';
                tabLogin.className =
                    'flex-1 py-3 px-4 rounded-md transition-all duration-300 font-medium text-gray-600 hover:text-gray-800';
                history.replaceState(null, '', '?page=register');
            });
        }

        // Recuperación
        const btnOlvido = document.getElementById('btn-olvido');
        const modalRecuperar = document.getElementById('modal-recuperar');
        const cerrarRecuperar = document.getElementById('cerrar-recuperar');
        const cerrarRecuperarBtn = document.getElementById('cerrar-recuperar-btn');

        if (btnOlvido && modalRecuperar) {
            btnOlvido.addEventListener('click', () => {
                modalRecuperar.classList.remove('hidden');
            });
        }

        function closeRecoveryModal() {
            if (modalRecuperar) modalRecuperar.classList.add('hidden');
            const form = document.getElementById('form-recuperar');
            if (form) form.reset();
        }

        if (cerrarRecuperar) cerrarRecuperar.addEventListener('click', closeRecoveryModal);
        if (cerrarRecuperarBtn) cerrarRecuperarBtn.addEventListener('click', closeRecoveryModal);

        // Form submission handlers
        // Form Login
        const formLoginElement = document.getElementById('form-login');
        if (formLoginElement) {
            formLoginElement.addEventListener('submit', function(e) {
                showLoading('btn-login', 'login-spinner', 'login-text');
            });
        }

        // Form Registro
        const formRegisterElement = document.getElementById('form-register');
        if (formRegisterElement) {
            formRegisterElement.addEventListener('submit', function(e) {
                if (!validarFormularioRegistro()) {
                    e.preventDefault();
                    hideLoading('btn-register', 'register-spinner', 'register-text');
                    return;
                }
                showLoading('btn-register', 'register-spinner', 'register-text');
            });
        }

        // Form Crear Organización
        const formCrearOrganizacion = document.getElementById('form-crear-organizacion');
        if (formCrearOrganizacion) {
            formCrearOrganizacion.addEventListener('submit', function(e) {
                showLoading('btn-crear-organizacion', 'crear-organizacion-spinner',
                    'crear-organizacion-text');
            });
        }

        // Form 2FA
        const form2FA = document.getElementById('form-2fa');
        if (form2FA) {
            form2FA.addEventListener('submit', function(e) {
                showLoading('btn-2fa', '2fa-spinner', '2fa-text');
            });
        }

        // Auto-focus según la página
        const urlParams = new URLSearchParams(window.location.search);
        const page = urlParams.get('page');

        if (page === 'crear-organizacion') {
            const input = document.getElementById('nombre-organizacion');
            if (input) input.focus();
        } else if (page === 'register') {
            const input = document.querySelector('#form-register input[required]');
            if (input) input.focus();
        } else if (page === 'login') {
            const input = document.querySelector('#form-login input[required]');
            if (input) input.focus();
        } else if (page === '2fa') {
            const input = document.querySelector('#form-2fa input[name="codigo"]');
            if (input) {
                input.focus();
                input.select();
            }
        }

        console.log('Sistema cargado en idioma:', currentLang);
    });

    // Manejar errores de conexión
    window.addEventListener('online', () => {
        showMessage(currentLang === 'en' ? "Connection restored" : "Conexión restablecida", 'success');
    });

    window.addEventListener('offline', () => {
        showMessage(currentLang === 'en' ? "No internet connection" : "Sin conexión a internet", 'error');
    });
    </script>
</body>

</html>