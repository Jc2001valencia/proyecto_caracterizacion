<?php
// $page = 'login' | 'register' | '2fa' | 'recuperar';
$page = $page ?? 'login';
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Autenticación - Sistema de Gestión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    body {
        font-family: 'Inter', sans-serif;
    }

    .auth-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }

    .auth-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .tab-active {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .input-group {
        position: relative;
    }

    .input-group input {
        transition: all 0.3s ease;
        padding-left: 2.5rem;
    }

    .input-group i {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        transition: color 0.3s ease;
    }

    .input-group input:focus+i {
        color: #3b82f6;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #10b981, #059669);
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }

    .modal-overlay {
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(5px);
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

    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

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

    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 640px) {
        .auth-card {
            margin: 1rem;
            padding: 1.5rem;
        }

        .input-group input {
            font-size: 16px;
            /* Previene zoom en iOS */
        }
    }

    .password-toggle {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
    }

    .strength-meter {
        height: 4px;
        border-radius: 2px;
        margin-top: 0.25rem;
        transition: all 0.3s ease;
    }

    .strength-0 {
        width: 0%;
        background: #ef4444;
    }

    .strength-1 {
        width: 25%;
        background: #ef4444;
    }

    .strength-2 {
        width: 50%;
        background: #f59e0b;
    }

    .strength-3 {
        width: 75%;
        background: #f59e0b;
    }

    .strength-4 {
        width: 100%;
        background: #10b981;
    }
    </style>
</head>

<body class="auth-container">
    <div class="min-h-screen flex items-center justify-center p-4">
        <!-- Card Principal -->
        <div class="auth-card rounded-2xl w-full max-w-md overflow-hidden fade-in">
            <!-- Header con Logo -->
            <div class="text-center p-6 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-lock text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold">Sistema de Gestión</h1>
                <p class="text-blue-100 mt-2">Plataforma de caracterización de proyectos</p>
            </div>

            <!-- Tabs -->
            <div class="flex bg-gray-100 p-1 m-4 rounded-lg">
                <button id="tab-login"
                    class="flex-1 py-3 px-4 rounded-md transition-all duration-300 tab-active font-medium">
                    <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                </button>
                <button id="tab-register"
                    class="flex-1 py-3 px-4 rounded-md transition-all duration-300 font-medium text-gray-600 hover:text-gray-800">
                    <i class="fas fa-user-plus mr-2"></i>Registrarse
                </button>
            </div>

            <!-- Contenido de los Forms -->
            <div class="p-6 pt-0">
                <!-- Login Form -->
                <form id="form-login" class="space-y-5">
                    <div class="input-group">
                        <input type="email" name="email"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="correo@ejemplo.com" required />
                        <i class="fas fa-envelope"></i>
                    </div>

                    <div class="input-group">
                        <input type="password" name="contrasena" id="login-password"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="••••••••" required />
                        <i class="fas fa-lock"></i>
                        <button type="button" class="password-toggle" onclick="togglePassword('login-password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <div class="flex justify-between items-center">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Recordarme</span>
                        </label>
                        <button type="button" id="btn-olvido"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            ¿Olvidó su contraseña?
                        </button>
                    </div>

                    <button type="submit"
                        class="w-full btn-primary text-white py-3 rounded-lg font-semibold flex items-center justify-center">
                        <span id="login-text">Ingresar al Sistema</span>
                        <div id="login-spinner" class="loading-spinner hidden ml-2"></div>
                    </button>
                </form>

                <!-- Registro Form -->
                <form id="form-register" class="space-y-5 hidden">
                    <div class="input-group">
                        <input type="text" name="nombre"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Nombre de la Organización" required />
                        <i class="fas fa-building"></i>
                    </div>

                    <div class="input-group">
                        <input type="email" name="email"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="correo@organizacion.com" required />
                        <i class="fas fa-envelope"></i>
                    </div>

                    <div class="input-group">
                        <input type="text" name="usuario"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Nombre de usuario" required />
                        <i class="fas fa-user"></i>
                    </div>

                    <div class="input-group">
                        <input type="password" name="contrasena" id="register-password"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Crear contraseña" required oninput="checkPasswordStrength(this.value)" />
                        <i class="fas fa-lock"></i>
                        <button type="button" class="password-toggle" onclick="togglePassword('register-password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <!-- Indicador de fortaleza de contraseña -->
                    <div id="password-strength" class="strength-meter strength-0 hidden"></div>
                    <div id="password-feedback" class="text-xs text-gray-500 mt-1"></div>

                    <div class="flex items-center">
                        <input type="checkbox" name="terms" required
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">
                            Acepto los <a href="#" class="text-blue-600 hover:underline">términos y condiciones</a>
                        </span>
                    </div>

                    <button type="submit"
                        class="w-full btn-secondary text-white py-3 rounded-lg font-semibold flex items-center justify-center">
                        <span id="register-text">Crear Cuenta</span>
                        <div id="register-spinner" class="loading-spinner hidden ml-2"></div>
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t text-center">
                <p class="text-sm text-gray-600">
                    &copy; 2024 Sistema de Gestión. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>

    <!-- Modal: Recuperar Contraseña -->
    <div id="modal-recuperar" class="fixed inset-0 modal-overlay flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm transform transition-all fade-in">
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
                <p class="text-sm text-gray-600">
                    Ingrese su correo electrónico y le enviaremos un enlace para restablecer su contraseña.
                </p>
                <div class="input-group">
                    <input type="email" name="email_recuperar"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="correo@ejemplo.com" required />
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" id="cerrar-recuperar-btn"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-medium">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 btn-primary text-white rounded-lg font-medium flex items-center">
                        <span id="recover-text">Enviar Enlace</span>
                        <div id="recover-spinner" class="loading-spinner hidden ml-2"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Código 2FA -->
    <div id="modal-2fa" class="fixed inset-0 modal-overlay flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm transform transition-all fade-in">
            <div class="p-6 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-shield-alt mr-2 text-green-600"></i>
                        Verificación en Dos Pasos
                    </h3>
                    <button type="button" id="cerrar-2fa" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            <form id="form-2fa" class="p-6 space-y-4">
                <p class="text-sm text-gray-600">
                    Hemos enviado un código de verificación a su correo electrónico. Por favor ingréselo a continuación.
                </p>
                <div class="input-group">
                    <input type="text" name="codigo_2fa"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center text-lg font-mono tracking-widest"
                        placeholder="000000" maxlength="6" required />
                    <i class="fas fa-qrcode"></i>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <button type="button" id="reenviar-codigo" class="text-blue-600 hover:text-blue-800 font-medium">
                        <i class="fas fa-redo mr-1"></i>Reenviar código
                    </button>
                    <span id="contador-reenvio" class="text-gray-500">60s</span>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" id="cerrar-2fa-btn"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-medium">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 btn-secondary text-white rounded-lg font-medium flex items-center">
                        <span id="verify-text">Verificar Código</span>
                        <div id="verify-spinner" class="loading-spinner hidden ml-2"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Referencias generales
    const tabLogin = document.getElementById('tab-login');
    const tabRegister = document.getElementById('tab-register');
    const formLogin = document.getElementById('form-login');
    const formRegister = document.getElementById('form-register');

    // Modales
    const modalRecuperar = document.getElementById('modal-recuperar');
    const modal2FA = document.getElementById('modal-2fa');
    const btnOlvido = document.getElementById('btn-olvido');
    const cerrarRecuperar = document.getElementById('cerrar-recuperar');
    const cerrarRecuperarBtn = document.getElementById('cerrar-recuperar-btn');
    const cerrar2FA = document.getElementById('cerrar-2fa');
    const cerrar2FABtn = document.getElementById('cerrar-2fa-btn');
    const reenviarCodigo = document.getElementById('reenviar-codigo');
    const contadorReenvio = document.getElementById('contador-reenvio');

    // ==========================
    //  FUNCIONES UTILITARIAS
    // ==========================
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.parentNode.querySelector('.password-toggle i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }

    function checkPasswordStrength(password) {
        const strengthMeter = document.getElementById('password-strength');
        const feedback = document.getElementById('password-feedback');

        if (!password) {
            strengthMeter.classList.add('hidden');
            feedback.textContent = '';
            return;
        }

        let strength = 0;
        let feedbackText = '';

        // Verificar longitud
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;

        // Verificar caracteres especiales
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;

        // Verificar números y letras
        if (/[0-9]/.test(password) && /[a-zA-Z]/.test(password)) strength++;

        // Actualizar indicador visual
        strengthMeter.className = `strength-meter strength-${strength}`;
        strengthMeter.classList.remove('hidden');

        // Actualizar feedback
        if (strength <= 1) {
            feedbackText = 'Contraseña débil';
            feedback.className = 'text-xs text-red-500 mt-1';
        } else if (strength <= 3) {
            feedbackText = 'Contraseña moderada';
            feedback.className = 'text-xs text-yellow-500 mt-1';
        } else {
            feedbackText = 'Contraseña fuerte';
            feedback.className = 'text-xs text-green-500 mt-1';
        }

        feedback.textContent = feedbackText;
    }

    function showLoading(buttonId, spinnerId, textId) {
        document.getElementById(buttonId).disabled = true;
        document.getElementById(spinnerId).classList.remove('hidden');
        document.getElementById(textId).textContent = 'Procesando...';
    }

    function hideLoading(buttonId, spinnerId, textId, originalText) {
        document.getElementById(buttonId).disabled = false;
        document.getElementById(spinnerId).classList.add('hidden');
        document.getElementById(textId).textContent = originalText;
    }

    function showError(input, message) {
        input.classList.add('shake', 'border-red-500');
        setTimeout(() => {
            input.classList.remove('shake');
        }, 500);

        // Mostrar tooltip de error (podría mejorarse con un sistema de notificaciones)
        const existingError = input.parentNode.querySelector('.error-message');
        if (existingError) existingError.remove();

        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-red-500 text-xs mt-1';
        errorDiv.textContent = message;
        input.parentNode.appendChild(errorDiv);
    }

    function clearErrors(form) {
        form.querySelectorAll('.error-message').forEach(error => error.remove());
        form.querySelectorAll('input').forEach(input => {
            input.classList.remove('border-red-500');
        });
    }

    // ==========================
    //  PESTAÑAS LOGIN / REGISTRO
    // ==========================
    function switchToLogin() {
        formLogin.classList.remove('hidden');
        formRegister.classList.add('hidden');
        tabLogin.classList.add('tab-active', 'text-white');
        tabLogin.classList.remove('text-gray-600');
        tabRegister.classList.remove('tab-active', 'text-white');
        tabRegister.classList.add('text-gray-600');
        clearErrors(formLogin);
    }

    function switchToRegister() {
        formLogin.classList.add('hidden');
        formRegister.classList.remove('hidden');
        tabRegister.classList.add('tab-active', 'text-white');
        tabRegister.classList.remove('text-gray-600');
        tabLogin.classList.remove('tab-active', 'text-white');
        tabLogin.classList.add('text-gray-600');
        clearErrors(formRegister);
    }

    tabLogin.addEventListener('click', switchToLogin);
    tabRegister.addEventListener('click', switchToRegister);

    // ==========================
    //  MODAL DE RECUPERACIÓN
    // ==========================
    btnOlvido.addEventListener('click', (e) => {
        e.preventDefault();
        modalRecuperar.classList.remove('hidden');
    });

    function closeRecoveryModal() {
        modalRecuperar.classList.add('hidden');
        document.getElementById('form-recuperar').reset();
    }

    cerrarRecuperar.addEventListener('click', closeRecoveryModal);
    cerrarRecuperarBtn.addEventListener('click', closeRecoveryModal);

    // ==========================
    //  LOGIN
    // ==========================
    formLogin.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearErrors(formLogin);

        const formData = new FormData(formLogin);
        showLoading('form-login', 'login-spinner', 'login-text');

        try {
            const res = await fetch('controllers/AuthController.php?action=login', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                modal2FA.classList.remove('hidden');
                startResendTimer();
            } else {
                showError(formLogin.querySelector('input[name="email"]'), data.message);
            }
        } catch (err) {
            console.error('Error en login:', err);
            alert('Error al conectar con el servidor.');
        } finally {
            hideLoading('form-login', 'login-spinner', 'login-text', 'Ingresar al Sistema');
        }
    });

    // ==========================
    //  REGISTRO
    // ==========================
    formRegister.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearErrors(formRegister);

        const formData = new FormData(formRegister);
        showLoading('form-register', 'register-spinner', 'register-text');

        try {
            const response = await fetch('controllers/AuthController.php?action=register', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                alert('✅ Registro exitoso. Ahora puede iniciar sesión.');
                formRegister.reset();
                document.getElementById('password-strength').classList.add('hidden');
                switchToLogin();
            } else {
                showError(formRegister.querySelector('input[name="email"]'), data.message);
            }
        } catch (err) {
            console.error('Error en registro:', err);
            alert('Error al registrar usuario.');
        } finally {
            hideLoading('form-register', 'register-spinner', 'register-text', 'Crear Cuenta');
        }
    });

    // ==========================
    //  RECUPERACIÓN DE CONTRASEÑA
    // ==========================
    document.getElementById('form-recuperar').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        showLoading('form-recuperar', 'recover-spinner', 'recover-text');

        try {
            const response = await fetch('controllers/AuthController.php?action=recuperar', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            alert(data.message);
            closeRecoveryModal();
        } catch (err) {
            console.error('Error en recuperación:', err);
            alert('No se pudo recuperar la contraseña.');
        } finally {
            hideLoading('form-recuperar', 'recover-spinner', 'recover-text', 'Enviar Enlace');
        }
    });

    // ==========================
    //  VERIFICACIÓN 2FA
    // ==========================
    let resendTimer;
    let timeLeft = 60;

    function startResendTimer() {
        timeLeft = 60;
        reenviarCodigo.disabled = true;
        updateResendTimer();

        resendTimer = setInterval(() => {
            timeLeft--;
            updateResendTimer();

            if (timeLeft <= 0) {
                clearInterval(resendTimer);
                reenviarCodigo.disabled = false;
                contadorReenvio.textContent = 'Listo';
            }
        }, 1000);
    }

    function updateResendTimer() {
        contadorReenvio.textContent = `${timeLeft}s`;
    }

    document.getElementById('form-2fa').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form2FA = e.target;
        const formData = new FormData(form2FA);

        showLoading('form-2fa', 'verify-spinner', 'verify-text');

        try {
            const res = await fetch('controllers/AuthController.php?action=verificar2FA', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                alert('✅ Verificación exitosa');
                modal2FA.classList.add('hidden');
                clearInterval(resendTimer);
                window.location.href = data.redirect;
            } else {
                showError(form2FA.querySelector('input[name="codigo_2fa"]'), data.message);
            }
        } catch (err) {
            console.error('Error en verificación 2FA:', err);
            alert('Error al verificar el código 2FA.');
        } finally {
            hideLoading('form-2fa', 'verify-spinner', 'verify-text', 'Verificar Código');
        }
    });

    reenviarCodigo.addEventListener('click', async () => {
        if (reenviarCodigo.disabled) return;

        showLoading('form-2fa', 'verify-spinner', 'verify-text');

        try {
            // Aquí iría la lógica para reenviar el código
            const response = await fetch('controllers/AuthController.php?action=reenviar2FA', {
                method: 'POST'
            });

            const data = await response.json();
            if (data.success) {
                alert('📧 Código reenviado exitosamente');
                startResendTimer();
            } else {
                alert('Error al reenviar el código');
            }
        } catch (err) {
            console.error('Error al reenviar código:', err);
            alert('Error al reenviar el código');
        } finally {
            hideLoading('form-2fa', 'verify-spinner', 'verify-text', 'Verificar Código');
        }
    });

    // ==========================
    //  CERRAR MODAL 2FA
    // ==========================
    function close2FAModal() {
        modal2FA.classList.add('hidden');
        clearInterval(resendTimer);
        document.getElementById('form-2fa').reset();
    }

    cerrar2FA.addEventListener('click', close2FAModal);
    cerrar2FABtn.addEventListener('click', close2FAModal);

    // ==========================
    //  MEJORAS DE UX
    // ==========================

    // Auto-focus en el primer input del form activo
    document.addEventListener('DOMContentLoaded', () => {
        formLogin.querySelector('input[type="email"]').focus();
    });

    // Enter para enviar forms
    document.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            const activeForm = document.querySelector('form:not(.hidden)');
            if (activeForm) {
                activeForm.dispatchEvent(new Event('submit'));
            }
        }
    });

    // Cerrar modales con ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (!modalRecuperar.classList.contains('hidden')) {
                closeRecoveryModal();
            }
            if (!modal2FA.classList.contains('hidden')) {
                close2FAModal();
            }
        }
    });

    // Validación en tiempo real para el código 2FA (solo números)
    document.querySelector('input[name="codigo_2fa"]').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    </script>
</body>

</html>