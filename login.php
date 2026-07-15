<?php
require_once 'config.php';
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - iNotes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-slate-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">iNotes 📝</h1>
            <p class="text-slate-500 text-sm mt-2" id="form-subtitle">Masuk untuk mengelola catatan Anda</p>
        </div>

        <!-- Alert Message -->
        <div id="alert" class="hidden mb-4 p-3 rounded-lg text-sm text-center"></div>

        <!-- FORM LOGIN / REGISTER -->
        <form id="auth-form" class="space-y-4">
            <div id="name-field" class="hidden">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" id="name" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" id="email" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" id="password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>

            <button type="submit" id="submit-btn" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl shadow-md shadow-indigo-200 transition-all">
                Masuk
            </button>
        </form>

        <div class="relative my-6 text-center">
            <hr class="border-slate-200">
            <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-3 text-xs text-slate-400 font-medium">ATAU</span>
        </div>

        <!-- GOOGLE SIGN IN BUTTON -->
        <div class="flex justify-center">
            <div id="g_id_onload"
                 data-client_id="<?= GOOGLE_CLIENT_ID ?>"
                 data-context="signin"
                 data-ux_mode="popup"
                 data-callback="handleCredentialResponse"
                 data-auto_prompt="false">
            </div>
            <div class="g_id_signin w-full" data-type="standard" data-shape="pill" data-theme="outline" data-text="signin_with" data-size="large" data-logo_alignment="left"></div>
        </div>

        <p class="text-center text-sm text-slate-600 mt-6">
            <span id="toggle-text">Belum punya akun?</span>
            <button id="toggle-btn" class="text-indigo-600 font-semibold hover:underline focus:outline-none">Daftar Sekarang</button>
        </p>
    </div>

    <script>
        let isLoginMode = true;
        const authForm = document.getElementById('auth-form');
        const toggleBtn = document.getElementById('toggle-btn');
        const nameField = document.getElementById('name-field');
        const formSubtitle = document.getElementById('form-subtitle');
        const submitBtn = document.getElementById('submit-btn');
        const toggleText = document.getElementById('toggle-text');
        const alertBox = document.getElementById('alert');

        // Toggle antara login dan register
        toggleBtn.addEventListener('click', () => {
            isLoginMode = !isLoginMode;
            nameField.classList.toggle('hidden', isLoginMode);
            document.getElementById('name').required = !isLoginMode;
            
            formSubtitle.innerText = isLoginMode ? 'Masuk untuk mengelola catatan Anda' : 'Buat akun gratis Anda sekarang';
            submitBtn.innerText = isLoginMode ? 'Masuk' : 'Daftar';
            toggleText.innerText = isLoginMode ? 'Belum punya akun?' : 'Sudah punya akun?';
            toggleBtn.innerText = isLoginMode ? 'Daftar Sekarang' : 'Login';
            alertBox.classList.add('hidden');
        });

        // Handler Form Submit (Manual Auth)
        authForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const action = isLoginMode ? 'login' : 'register';
            const data = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                name: document.getElementById('name').value
            };

            try {
                const response = await fetch(`auth.php?action=${action}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (result.success) {
                    if (isLoginMode) {
                        window.location.href = 'index.php';
                    } else {
                        showAlert('green', result.message);
                        toggleBtn.click(); // Pindah ke mode login otomatis
                    }
                } else {
                    showAlert('red', result.message);
                }
            } catch (err) {
                showAlert('red', 'Terjadi kesalahan sistem.');
            }
        });

        // Handler Google Sign-In Response
        async function handleCredentialResponse(response) {
            try {
                const res = await fetch('auth.php?action=google-login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ credential: response.credential })
                });
                const result = await res.json();
                if (result.success) {
                    window.location.href = 'index.php';
                } else {
                    showAlert('red', result.message);
                }
            } catch (err) {
                showAlert('red', 'Gagal masuk lewat Google.');
            }
        }

        function showAlert(type, msg) {
            alertBox.className = `mb-4 p-3 rounded-xl text-sm text-center ${type === 'red' ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600'}`;
            alertBox.innerText = msg;
            alertBox.classList.remove('hidden');
        }
    </script>
</body>
</html>