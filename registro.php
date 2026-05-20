<?php
require_once __DIR__ . '/auth.php';
roomtemperature_require_login();
?>
<!doctype html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de usuario | RoomTemperature</title>
    <link href="output.css" rel="stylesheet">
    <link rel="icon" href="images/icono.png" sizes="32x32" type="image/png">
    <style>
        body.auth-page {
            margin: 0;
            min-height: 100vh;
            font-family: ui-sans-serif, system-ui, sans-serif;
            color: #e2e8f0;
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, 0.18), transparent 28%),
                radial-gradient(circle at left center, rgba(59, 130, 246, 0.16), transparent 30%),
                linear-gradient(135deg, #020617 0%, #0f172a 45%, #020617 100%);
            overflow-x: hidden;
        }

        .auth-shell {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .auth-grid {
            width: min(1120px, 100%);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .auth-hero,
        .auth-card {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(51, 65, 85, 0.9);
            border-radius: 32px;
            background: rgba(15, 23, 42, 0.78);
            backdrop-filter: blur(22px);
            box-shadow: 0 30px 80px rgba(2, 6, 23, 0.6);
        }

        .auth-hero {
            padding: 2.5rem;
            min-height: 540px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .auth-card {
            padding: 2.5rem;
        }

        .auth-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.6rem 1rem;
            border-radius: 999px;
            border: 1px solid rgba(34, 211, 238, 0.25);
            background: rgba(34, 211, 238, 0.1);
            color: #67e8f9;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            font-size: 0.72rem;
            font-weight: 800;
        }

        .auth-dot {
            width: 0.55rem;
            height: 0.55rem;
            border-radius: 999px;
            background: #22d3ee;
            box-shadow: 0 0 0 6px rgba(34, 211, 238, 0.12);
            animation: pulse 2s infinite;
        }

        .auth-title {
            margin: 1.35rem 0 0;
            font-size: clamp(2.5rem, 5vw, 4.6rem);
            line-height: 0.95;
            font-weight: 900;
            letter-spacing: -0.05em;
            color: #f8fafc;
        }

        .auth-title span { color: #22d3ee; }

        .auth-copy {
            max-width: 42rem;
            margin-top: 1.25rem;
            font-size: 1.05rem;
            line-height: 1.8;
            color: #94a3b8;
        }

        .auth-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .auth-stat {
            border: 1px solid rgba(51, 65, 85, 0.95);
            border-radius: 24px;
            background: rgba(2, 6, 23, 0.35);
            padding: 1rem;
        }

        .auth-stat small {
            display: block;
            margin-bottom: 0.4rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.22em;
            font-size: 0.68rem;
            font-weight: 800;
        }

        .auth-stat p {
            margin: 0;
            color: #e2e8f0;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .auth-form {
            width: 100%;
        }

        .auth-form-head small {
            display: block;
            color: #67e8f9;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            font-size: 0.7rem;
            font-weight: 800;
        }

        .auth-form-head h2 {
            margin: 0.55rem 0 0;
            font-size: 2rem;
            color: #fff;
        }

        .auth-form-head p {
            margin: 0.45rem 0 0;
            color: #64748b;
        }

        .field {
            display: grid;
            gap: 0.5rem;
            margin-top: 1.1rem;
        }

        .field label {
            font-size: 0.95rem;
            font-weight: 600;
            color: #cbd5e1;
        }

        .field input {
            width: 100%;
            box-sizing: border-box;
            border-radius: 18px;
            border: 1px solid rgba(51, 65, 85, 0.9);
            background: rgba(2, 6, 23, 0.72);
            color: #f8fafc;
            padding: 0.95rem 1rem;
            font-size: 1rem;
            outline: none;
            transition: border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .field input::placeholder { color: #64748b; }

        .field input:focus {
            border-color: rgba(34, 211, 238, 0.9);
            box-shadow: 0 0 0 4px rgba(34, 211, 238, 0.14);
        }

        .primary-btn {
            width: 100%;
            border: 0;
            border-radius: 18px;
            padding: 0.95rem 1rem;
            margin-top: 1rem;
            color: white;
            font-weight: 700;
            background: linear-gradient(90deg, #06b6d4 0%, #2563eb 100%);
            box-shadow: 0 18px 30px rgba(8, 47, 73, 0.35);
            cursor: pointer;
            transition: transform 160ms ease, filter 160ms ease;
        }

        .primary-btn:hover { transform: translateY(-1px); filter: brightness(1.08); }

        .error-box {
            margin-top: 1rem;
            border-radius: 18px;
            border: 1px solid rgba(244, 63, 94, 0.35);
            background: rgba(244, 63, 94, 0.12);
            color: #fecdd3;
            padding: 0.9rem 1rem;
            font-size: 0.95rem;
        }

        .helper-text {
            margin-top: 1rem;
            color: #64748b;
            font-size: 0.8rem;
            line-height: 1.7;
        }

        .visual-glow {
            position: absolute;
            border-radius: 999px;
            filter: blur(64px);
            pointer-events: none;
        }

        .glow-1 { width: 28rem; height: 28rem; top: -10rem; right: -8rem; background: rgba(34, 211, 238, 0.12); }
        .glow-2 { width: 20rem; height: 20rem; left: -6rem; top: 48%; background: rgba(59, 130, 246, 0.1); }
        .glow-3 { width: 22rem; height: 22rem; bottom: -8rem; right: 24%; background: rgba(16, 185, 129, 0.08); }

        @media (max-width: 1024px) {
            .auth-grid { grid-template-columns: 1fr; }
            .auth-hero { min-height: auto; }
        }

        @media (max-width: 640px) {
            .auth-shell { padding: 1rem; }
            .auth-hero,
            .auth-card { padding: 1.25rem; border-radius: 24px; }
            .auth-stats { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="auth-page">
    <div class="visual-glow glow-1" aria-hidden="true"></div>
    <div class="visual-glow glow-2" aria-hidden="true"></div>
    <div class="visual-glow glow-3" aria-hidden="true"></div>

    <main class="auth-shell">
        <div class="auth-grid">
            <section class="auth-hero">
                <div>
                    <div class="auth-chip"><span class="auth-dot"></span>Alta de usuario</div>
                    <h1 class="auth-title">Registro de usuarios</h1>
                    <p class="auth-copy">
                        Crea accesos para el equipo y deja trazabilidad dentro del panel de temperatura.
                    </p>
                </div>

                <div class="auth-stats">
                    <div class="auth-stat">
                        <small>Email</small>
                        <p>Se valida antes de guardar para evitar duplicados.</p>
                    </div>
                    <div class="auth-stat">
                        <small>Nombre</small>
                        <p>Se guarda junto al acceso para identificar al usuario.</p>
                    </div>
                    <div class="auth-stat">
                        <small>Respuesta</small>
                        <p>El sistema devuelve JSON con mensaje y contraseña generada.</p>
                    </div>
                </div>
            </section>

            <section class="auth-card">
                <div class="auth-form">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div class="auth-form-head">
                            <small>Panel administrativo</small>
                            <h2>Registrar usuario</h2>
                        </div>
                        <a href="inicio.php" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-700/60 bg-slate-800/60 px-3 py-2 text-sm text-slate-300 hover:text-white hover:border-cyan-500/40 transition-colors">
                            Volver
                        </a>
                    </div>

                    <form id="registroForm" autocomplete="off">
                        <div class="field">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required placeholder="correo@ejemplo.com">
                        </div>
                        <div class="field">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" required placeholder="Tu nombre completo">
                        </div>
                        <button id="submitBtn" type="submit" class="primary-btn">
                            <span id="submitText">Enviar registro</span>
                            <svg id="spinner" class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </form>
                    <div id="responseCard" class="mt-5 hidden rounded-2xl border p-4 text-sm">
                        <p id="responseTitle" class="font-semibold"></p>
                        <p id="responseMessage" class="mt-1"></p>
                        <p id="responseDetails" class="mt-2 whitespace-pre-line break-all text-slate-300"></p>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script>
        const form = document.getElementById('registroForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const spinner = document.getElementById('spinner');
        const responseCard = document.getElementById('responseCard');
        const responseTitle = document.getElementById('responseTitle');
        const responseMessage = document.getElementById('responseMessage');
        const responseDetails = document.getElementById('responseDetails');

        function setLoading(loading) {
            submitBtn.disabled = loading;
            spinner.classList.toggle('hidden', !loading);
            submitText.textContent = loading ? 'Procesando...' : 'Enviar registro';
        }

        function showResponse(type, title, message, details) {
            const styles = {
                success: 'border-emerald-500/30 bg-emerald-500/10 text-emerald-100',
                error: 'border-rose-500/30 bg-rose-500/10 text-rose-100',
                info: 'border-slate-700/60 bg-slate-800/60 text-slate-100'
            };
            responseCard.className = `mt-5 rounded-2xl border p-4 text-sm ${styles[type] || styles.info}`;
            responseTitle.textContent = title;
            responseMessage.textContent = message;
            responseDetails.textContent = details || '';
            responseCard.classList.remove('hidden');
        }

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            setLoading(true);
            showResponse('info', 'Enviando...', 'Validando la información.', '');

            try {
                const response = await fetch('guardarusuario.php', {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'Accept': 'application/json' }
                });

                const data = await response.json().catch(() => ({
                    success: false,
                    title: 'Respuesta inválida',
                    message: 'El servidor no devolvió un JSON válido.',
                    details: 'Revisa guardarusuario.php.'
                }));

                showResponse(
                    data.success ? 'success' : 'error',
                    data.title || (data.success ? 'Registro creado' : 'Error'),
                    data.message || 'No se recibió un mensaje del servidor.',
                    data.details || ''
                );

                if (data.success) {
                    form.reset();
                }
            } catch (error) {
                showResponse('error', 'Error de red', 'No fue posible comunicarse con el servidor.', error.message);
            } finally {
                setLoading(false);
            }
        });
    </script>
</body>
</html>
