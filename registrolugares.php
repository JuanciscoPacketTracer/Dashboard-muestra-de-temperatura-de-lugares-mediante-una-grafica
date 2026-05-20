<?php
require_once __DIR__ . '/auth.php';
roomtemperature_require_login();
?>
<!doctype html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de lugares | RoomTemperature</title>
    <link href="output.css" rel="stylesheet">
    <link rel="icon" href="images/icono.png" sizes="32x32" type="image/png">
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 font-sans selection:bg-cyan-500/30 selection:text-white overflow-x-hidden">
    <div class="fixed inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
        <div class="absolute -top-40 -right-36 w-[420px] h-[420px] rounded-full bg-cyan-500/10 blur-3xl"></div>
        <div class="absolute top-1/2 -left-28 w-80 h-80 rounded-full bg-blue-600/10 blur-3xl"></div>
    </div>

    <main class="min-h-screen flex items-center justify-center px-4 py-10 relative">
        <section class="w-full max-w-xl rounded-[2rem] border border-slate-800/80 bg-slate-900/85 backdrop-blur-xl shadow-2xl shadow-slate-950/60 p-8 md:p-10 relative overflow-hidden">
            <div class="absolute inset-0 bg-linear-to-br from-cyan-500/5 via-transparent to-blue-500/5 pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.25em] text-cyan-300 font-semibold">Alta de lugar</p>
                        <h1 class="mt-2 text-3xl font-black text-white">Registro de lugares</h1>
                    </div>
                    <a href="inicio.php" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-700/60 bg-slate-800/60 px-3 py-2 text-sm text-slate-300 hover:text-white hover:border-cyan-500/40 transition-colors">
                        Volver
                    </a>
                </div>

                <form id="registroForm" class="space-y-5" autocomplete="off">
                    <div class="space-y-2">
                        <label for="lugar" class="text-sm font-medium text-slate-300">Nombre del lugar</label>
                        <input type="text" id="lugar" name="lugar" required placeholder="Nombre del lugar"
                            class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20">
                    </div>
                    <button id="submitBtn" type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-cyan-600 px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-cyan-900/40 transition hover:bg-cyan-500 disabled:cursor-not-allowed disabled:opacity-70">
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
                const response = await fetch('guardarlugar.php', {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'Accept': 'application/json' }
                });

                const data = await response.json().catch(() => ({
                    success: false,
                    title: 'Respuesta inválida',
                    message: 'El servidor no devolvió un JSON válido.',
                    details: 'Revisa guardarlugar.php.'
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
