<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Autenticación - Sistema CARACTERIZACION</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-blue-600 to-purple-700 min-h-screen">
    <div class="min-h-screen flex items-center justify-center p-4">
        <!-- Card Principal -->
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <!-- Header con Logo -->
            <div class="text-center p-6 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-project-diagram text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold">CARACTERIZACION</h1>
                <p class="text-blue-100 mt-2">Sistema de Gestión de Proyectos</p>
            </div>

            <!-- Mensajes de éxito/error -->
            <?php if (isset($error) && $error): ?>
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

            <?php if (isset($success) && $success): ?>
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
                        <h3 class="text-lg font-semibold text-blue-800">Verificación en Dos Pasos</h3>
                        <p class="text-blue-600 mt-2 text-sm">
                            Hemos enviado un código de verificación de 6 dígitos a tu email
                        </p>
                        <p class="text-blue-500 text-xs mt-1">
                            Revisa tu bandeja de entrada y la carpeta de spam
                        </p>

                        <form id="form-2fa" method="POST" action="index.php" class="mt-4 space-y-4">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="action" value="verify2fa">

                            <div class="relative">
                                <input type="text" name="codigo" maxlength="6"
                                    class="w-full p-4 text-center text-2xl font-mono border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="000000" required pattern="[0-9]{6}" autocomplete="one-time-code"
                                    inputmode="numeric" />
                                <i
                                    class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                            </div>

                            <div class="text-center">
                                <p class="text-blue-500 text-xs">
                                    El código expira en 15 minutos
                                </p>
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" id="btn-2fa"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition duration-300 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105">
                                    <i class="fas fa-check mr-2"></i>
                                    <span id="2fa-text">Verificar</span>
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
                                Volver al inicio de sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs (ocultos durante 2FA) -->
            <div id="seccion-tabs" class="<?= $current_page === '2fa' ? 'hidden' : 'block' ?>">
                <div class="flex bg-gray-100 p-1 m-4 rounded-lg">
                    <button id="tab-login"
                        class="flex-1 py-3 px-4 rounded-md transition-all duration-300 <?= $current_page === 'login' ? 'bg-blue-600 text-white font-medium shadow-lg' : 'font-medium text-gray-600 hover:text-gray-800' ?>">
                        <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                    </button>
                    <button id="tab-register"
                        class="flex-1 py-3 px-4 rounded-md transition-all duration-300 <?= $current_page === 'register' ? 'bg-blue-600 text-white font-medium shadow-lg' : 'font-medium text-gray-600 hover:text-gray-800' ?>">
                        <i class="fas fa-building mr-2"></i>Registrar Organización
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
                                placeholder="Usuario o Email" required
                                value="<?= isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : '' ?>" />
                            <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>

                        <div class="relative">
                            <input type="password" name="password" id="login-password"
                                class="w-full p-3 pl-10 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                placeholder="••••••••" required />
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
                                <span class="ml-2 text-sm text-gray-600">Recordarme</span>
                            </label>
                            <button type="button" id="btn-olvido"
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium transition duration-200">
                                ¿Olvidó su contraseña?
                            </button>
                        </div>

                        <button type="submit" id="btn-login"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition duration-300 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            <span id="login-text">Ingresar al Sistema</span>
                            <div id="login-spinner" class="hidden ml-2">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </button>
                    </form>

                    <!-- Registro Form -->
                    <form id="form-register" method="POST" action="index.php"
                        class="space-y-5 <?= $current_page === 'register' ? '' : 'hidden' ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="register">

                        <?php
                        // Recuperar datos del formulario si existen
                        $form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
                        unset($_SESSION['form_data']); // Limpiar después de usar
                        ?>

                        <div class="relative">
                            <input type="text" name="nombre" id="register-nombre"
                                class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                placeholder="Nombre de la Organización" required
                                value="<?= isset($form_data['nombre']) ? htmlspecialchars($form_data['nombre']) : (isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '') ?>" />
                            <i
                                class="fas fa-building absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>

                        <div class="relative">
                            <input type="text" name="descripcion" id="register-descripcion"
                                class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                placeholder="Descripción de la organización (opcional)"
                                value="<?= isset($form_data['descripcion']) ? htmlspecialchars($form_data['descripcion']) : (isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : '') ?>" />
                            <i
                                class="fas fa-info-circle absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>

                        <div class="relative">
                            <input type="email" name="email" id="register-email"
                                class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                placeholder="correo@organizacion.com" required
                                value="<?= isset($form_data['email']) ? htmlspecialchars($form_data['email']) : (isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '') ?>" />
                            <i
                                class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>

                        <div class="relative">
                            <input type="text" name="usuario" id="register-usuario"
                                class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                placeholder="Nombre de usuario" required
                                value="<?= isset($form_data['usuario']) ? htmlspecialchars($form_data['usuario']) : (isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : '') ?>" />
                            <i
                                class="fas fa-user-tag absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>

                        <div class="relative">
                            <input type="password" name="contrasena" id="register-password"
                                class="w-full p-3 pl-10 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                placeholder="Crear contraseña" required oninput="checkPasswordStrength(this.value)" />
                            <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <button type="button"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition duration-200"
                                onclick="togglePassword('register-password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                        <div class="relative">
                            <input type="password" name="confirmar_contrasena" id="confirm-password"
                                class="w-full p-3 pl-10 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                placeholder="Confirmar contraseña" required />
                            <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <button type="button"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition duration-200"
                                onclick="togglePassword('confirm-password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                        <!-- Indicador de fortaleza de contraseña -->
                        <div id="password-strength" class="h-2 bg-gray-200 rounded-full hidden">
                            <div id="password-strength-bar" class="h-full rounded-full transition-all duration-300">
                            </div>
                        </div>
                        <div id="password-feedback" class="text-xs text-gray-500 mt-1"></div>

                        <div class="flex items-center">
                            <input type="checkbox" name="terms" id="register-terms" required
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition duration-200">
                            <span class="ml-2 text-sm text-gray-600">
                                Acepto los <a href="#"
                                    class="text-blue-600 hover:underline transition duration-200">términos y
                                    condiciones</a>
                            </span>
                        </div>

                        <button type="submit" id="btn-register"
                            class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition duration-300 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-building mr-2"></i>
                            <span id="register-text">Registrar Organización</span>
                            <div id="register-spinner" class="hidden ml-2">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t text-center">
                <p class="text-sm text-gray-600">
                    &copy; 2025 Sistema CARACTERIZACION. Todos los derechos reservados.
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
                        Recuperar Contraseña
                    </h3>
                    <button type="button" id="cerrar-recuperar" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            <form id="form-recuperar" class="p-6 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="action" value="solicitarRecuperacion">

                <p class="text-sm text-gray-600">
                    Ingrese su correo electrónico y le enviaremos un enlace para restablecer su contraseña.
                </p>
                <div class="relative">
                    <input type="email" name="email" id="email-recuperar"
                        class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="correo@organizacion.com" required />
                    <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" id="cerrar-recuperar-btn"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-medium">
                        Cancelar
                    </button>
                    <button type="submit" id="btn-enviar-recuperacion"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium flex items-center transition duration-300 disabled:opacity-50">
                        <i class="fas fa-paper-plane mr-2"></i>
                        <span id="recuperacion-text">Enviar Enlace</span>
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
    // CONFIGURACIÓN
    // ==========================
    const BASE_URL = window.location.origin;

    // ==========================
    // ELEMENTOS DEL DOM
    // ==========================
    const tabLogin = document.getElementById('tab-login');
    const tabRegister = document.getElementById('tab-register');
    const formLogin = document.getElementById('form-login');
    const formRegister = document.getElementById('form-register');
    const form2FA = document.getElementById('form-2fa');
    const seccion2FA = document.getElementById('seccion-2fa');
    const seccionTabs = document.getElementById('seccion-tabs');
    const seccionForms = document.getElementById('seccion-forms');
    const modalRecuperar = document.getElementById('modal-recuperar');
    const btnOlvido = document.getElementById('btn-olvido');
    const btnVolverLogin = document.getElementById('btn-volver-login');
    const btnRenviarCodigo = document.getElementById('btn-renviar-codigo');
    const cerrarRecuperar = document.getElementById('cerrar-recuperar');
    const cerrarRecuperarBtn = document.getElementById('cerrar-recuperar-btn');

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

        let barColor, barWidth, feedbackText, feedbackColor;
        if (strength <= 1) {
            barColor = 'bg-red-500';
            barWidth = 'w-1/4';
            feedbackText = 'Contraseña débil';
            feedbackColor = 'text-red-500';
        } else if (strength <= 3) {
            barColor = 'bg-yellow-500';
            barWidth = 'w-2/3';
            feedbackText = 'Contraseña moderada';
            feedbackColor = 'text-yellow-500';
        } else {
            barColor = 'bg-green-500';
            barWidth = 'w-full';
            feedbackText = 'Contraseña fuerte';
            feedbackColor = 'text-green-500';
        }

        strengthBar.className = `h-full rounded-full ${barWidth} ${barColor}`;
        feedback.className = `text-xs ${feedbackColor} mt-1`;
        feedback.textContent = feedbackText;
    }

    function showLoading(buttonId, spinnerId, textId) {
        const button = document.getElementById(buttonId);
        const spinner = document.getElementById(spinnerId);
        const text = document.getElementById(textId);

        if (button) button.disabled = true;
        if (spinner) spinner.classList.remove('hidden');
        if (text) text.textContent = 'Procesando...';
    }

    function hideLoading(buttonId, spinnerId, textId, originalText) {
        const button = document.getElementById(buttonId);
        const spinner = document.getElementById(spinnerId);
        const text = document.getElementById(textId);

        if (button) button.disabled = false;
        if (spinner) spinner.classList.add('hidden');
        if (text) text.textContent = originalText;
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

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentElement) {
                messageDiv.remove();
            }
        }, 5000);
    }

    // ==========================
    // EVENT LISTENERS
    // ==========================

    // Tabs
    if (tabLogin) {
        tabLogin.addEventListener('click', () => {
            if (formLogin) formLogin.classList.remove('hidden');
            if (formRegister) formRegister.classList.add('hidden');
            tabLogin.className =
                'flex-1 py-3 px-4 rounded-md transition-all duration-300 bg-blue-600 text-white font-medium shadow-lg';
            tabRegister.className =
                'flex-1 py-3 px-4 rounded-md transition-all duration-300 font-medium text-gray-600 hover:text-gray-800';
            history.replaceState(null, '', '?page=login');
        });
    }

    if (tabRegister) {
        tabRegister.addEventListener('click', () => {
            if (formLogin) formLogin.classList.add('hidden');
            if (formRegister) formRegister.classList.remove('hidden');
            tabRegister.className =
                'flex-1 py-3 px-4 rounded-md transition-all duration-300 bg-blue-600 text-white font-medium shadow-lg';
            tabLogin.className =
                'flex-1 py-3 px-4 rounded-md transition-all duration-300 font-medium text-gray-600 hover:text-gray-800';
            history.replaceState(null, '', '?page=register');
        });
    }

    // Recuperación
    if (btnOlvido) {
        btnOlvido.addEventListener('click', () => {
            if (modalRecuperar) modalRecuperar.classList.remove('hidden');
        });
    }

    function closeRecoveryModal() {
        if (modalRecuperar) modalRecuperar.classList.add('hidden');
        const form = document.getElementById('form-recuperar');
        if (form) form.reset();
    }

    if (cerrarRecuperar) {
        cerrarRecuperar.addEventListener('click', closeRecoveryModal);
    }

    if (cerrarRecuperarBtn) {
        cerrarRecuperarBtn.addEventListener('click', closeRecoveryModal);
    }

    // Manejar envío de formularios con loading states
    document.addEventListener('DOMContentLoaded', () => {
        // Form Login
        if (formLogin) {
            formLogin.addEventListener('submit', function(e) {
                showLoading('btn-login', 'login-spinner', 'login-text');
            });
        }

        // Form Register
        if (formRegister) {
            formRegister.addEventListener('submit', function(e) {
                // Validar contraseñas
                const password = document.getElementById('register-password').value;
                const confirmPassword = document.getElementById('confirm-password').value;

                if (password !== confirmPassword) {
                    e.preventDefault();
                    showMessage('Las contraseñas no coinciden', 'error');
                    hideLoading('btn-register', 'register-spinner', 'register-text',
                        'Registrar Organización');
                    return;
                }

                showLoading('btn-register', 'register-spinner', 'register-text');
            });
        }

        // Form 2FA
        if (form2FA) {
            form2FA.addEventListener('submit', function(e) {
                showLoading('btn-2fa', '2fa-spinner', '2fa-text');
            });
        }

        // Auto-focus en el primer campo
        const firstInput = document.querySelector('input[required]');
        if (firstInput) firstInput.focus();

        // Mostrar sección 2FA si es necesario
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('page') === '2fa') {
            if (seccion2FA) seccion2FA.classList.remove('hidden');
            if (seccionTabs) seccionTabs.classList.add('hidden');

            const codigoInput = document.querySelector('#form-2fa input[name="codigo"]');
            if (codigoInput) {
                codigoInput.focus();
                // Auto-seleccionar el texto para fácil reemplazo
                codigoInput.select();
            }
        }

        console.log('Sistema CARACTERIZACION - Vista de autenticación cargada');
    });

    // Manejar errores de conexión
    window.addEventListener('online', () => {
        showMessage('Conexión restablecida', 'success');
    });

    window.addEventListener('offline', () => {
        showMessage('Sin conexión a internet', 'error');
    });
    </script>
</body>

</html>