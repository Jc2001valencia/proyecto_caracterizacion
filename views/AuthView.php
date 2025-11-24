<?php
// $page = 'login' | 'register' | '2fa' | 'recuperar';
$page = $page ?? 'login';
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Autenticación - Organización</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-800 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-lg shadow p-6 relative">

        <!-- Tabs -->
        <div class="flex mb-4 border-b">
            <button id="tab-login"
                class="flex-1 text-center py-2 border-b-2 border-blue-600 font-semibold">Login</button>
            <button id="tab-register"
                class="flex-1 text-center py-2 border-b-2 border-transparent hover:border-gray-300">Registro</button>
        </div>

        <!-- Login Form -->
        <form id="form-login" class="space-y-4">
            <div>
                <label class="text-sm">Email</label>
                <input type="email" name="email" class="w-full mt-1 p-2 border rounded" placeholder="email@ejemplo.com"
                    required />
            </div>
            <div>
                <label class="text-sm">Contraseña</label>
                <input type="password" name="contrasena" class="w-full mt-1 p-2 border rounded" placeholder="••••••••"
                    required />
            </div>
            <div class="flex justify-between items-center">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Ingresar</button>
                <a href="#" id="btn-olvido" class="text-sm text-blue-600 hover:underline">¿Olvidó su contraseña?</a>
            </div>
        </form>

        <!-- Registro Form -->
        <form id="form-register" class="space-y-4 hidden">
            <div>
                <label class="text-sm">Nombre de la Organización</label>
                <input type="text" name="nombre" class="w-full mt-1 p-2 border rounded" required />
            </div>
            <div>
                <label class="text-sm">Email</label>
                <input type="email" name="email" class="w-full mt-1 p-2 border rounded" required />
            </div>
            <div>
                <label class="text-sm">Usuario</label>
                <input type="text" name="usuario" class="w-full mt-1 p-2 border rounded" required />
            </div>
            <div>
                <label class="text-sm">Contraseña</label>
                <input type="password" name="contrasena" class="w-full mt-1 p-2 border rounded" required />
            </div>
            <div class="flex justify-end">
                <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Registrar</button>
            </div>
        </form>
    </div>

    <!-- Modal: Recuperar Contraseña -->
    <div id="modal-recuperar" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded shadow w-full max-w-sm relative">
            <h3 class="text-lg font-semibold mb-4">Recuperar Contraseña</h3>
            <form id="form-recuperar" class="space-y-4">
                <div>
                    <label class="text-sm">Ingrese su correo</label>
                    <input type="email" name="email_recuperar" class="w-full mt-1 p-2 border rounded" required />
                </div>
                <div class="flex justify-end gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Enviar</button>
                    <button type="button" id="cerrar-recuperar"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Código 2FA -->
    <div id="modal-2fa" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded shadow w-full max-w-sm relative">
            <h3 class="text-lg font-semibold mb-4">Verificación en 2 pasos</h3>
            <form id="form-2fa" class="space-y-4">
                <div>
                    <label class="text-sm">Ingrese el código enviado a su correo</label>
                    <input type="text" name="codigo_2fa" class="w-full mt-1 p-2 border rounded" required />
                </div>
                <div class="flex justify-end gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Verificar</button>
                    <button type="button" id="cerrar-2fa"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancelar</button>
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
    const cerrar2FA = document.getElementById('cerrar-2fa');

    // ==========================
    //  PESTAÑAS LOGIN / REGISTRO
    // ==========================
    tabLogin.addEventListener('click', () => {
        formLogin.classList.remove('hidden');
        formRegister.classList.add('hidden');
        tabLogin.classList.add('border-blue-600', 'font-semibold');
        tabRegister.classList.remove('border-blue-600', 'font-semibold');
    });

    tabRegister.addEventListener('click', () => {
        formLogin.classList.add('hidden');
        formRegister.classList.remove('hidden');
        tabRegister.classList.add('border-blue-600', 'font-semibold');
        tabLogin.classList.remove('border-blue-600', 'font-semibold');
    });

    // ==========================
    //  MODAL DE RECUPERACIÓN
    // ==========================
    btnOlvido.addEventListener('click', (e) => {
        e.preventDefault();
        modalRecuperar.classList.remove('hidden');
    });

    cerrarRecuperar.addEventListener('click', () => modalRecuperar.classList.add('hidden'));

    // ==========================
    //  LOGIN
    // ==========================
    formLogin.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formLogin);

        // Mostrar mensaje de carga
        const btnLogin = formLogin.querySelector('button[type="submit"]');
        const originalText = btnLogin.textContent;
        btnLogin.textContent = "Verificando...";
        btnLogin.disabled = true;

        try {
            const res = await fetch('controllers/AuthController.php?action=login', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            console.log('Datos login:', data);

            if (data.success) {
                // ✅ Mostrar modal 2FA solo si el login fue exitoso
                modal2FA.classList.remove('hidden');
            } else {
                alert(data.message);
            }
        } catch (err) {
            console.error('Error en login:', err);
            alert('Error al conectar con el servidor.');
        } finally {
            // Restaurar botón
            btnLogin.textContent = originalText;
            btnLogin.disabled = false;
        }
    });

    // ==========================
    //  REGISTRO
    // ==========================
    formRegister.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(formRegister);
        const btnRegister = formRegister.querySelector('button[type="submit"]');
        const originalText = btnRegister.textContent;
        btnRegister.textContent = "Registrando...";
        btnRegister.disabled = true;

        try {
            const response = await fetch('controllers/AuthController.php?action=register', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                alert('Registro exitoso');
                formRegister.reset();
                tabLogin.click(); // Cambiar a pestaña de login automáticamente
            } else {
                alert('Error: ' + data.message);
            }
        } catch (err) {
            console.error('Error en registro:', err);
            alert('Error al registrar usuario.');
        } finally {
            btnRegister.textContent = originalText;
            btnRegister.disabled = false;
        }
    });

    // ==========================
    //  RECUPERACIÓN DE CONTRASEÑA
    // ==========================
    document.getElementById('form-recuperar').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        try {
            const response = await fetch('controllers/AuthController.php?action=recuperar', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            alert(data.message);
            modalRecuperar.classList.add('hidden');
        } catch (err) {
            console.error('Error en recuperación:', err);
            alert('No se pudo recuperar la contraseña.');
        }
    });

    // ==========================
    //  VERIFICACIÓN 2FA
    // ==========================
    document.getElementById('form-2fa').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form2FA = e.target;
        const formData = new FormData(form2FA);

        const btn2FA = form2FA.querySelector('button[type="submit"]');
        const originalText = btn2FA.textContent;
        btn2FA.textContent = "Verificando código...";
        btn2FA.disabled = true;

        try {
            const res = await fetch('controllers/AuthController.php?action=verificar2FA', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            console.log('Resultado verificación 2FA:', data);

            if (data.success) {
                alert(data.message);
                modal2FA.classList.add('hidden');
                // Redirigir al home
                window.location.href = data.redirect;
            } else {
                alert(data.message);
            }
        } catch (err) {
            console.error('Error en verificación 2FA:', err);
            alert('Error al verificar el código 2FA.');
        } finally {
            btn2FA.textContent = originalText;
            btn2FA.disabled = false;
        }
    });

    // ==========================
    //  CERRAR MODAL 2FA
    // ==========================
    cerrar2FA.addEventListener('click', () => modal2FA.classList.add('hidden'));
    </script>


</body>

</html>