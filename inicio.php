<?php ob_start();
session_start();
include __DIR__ . '/src/data.php';
?>
<!doctype html>
<html lang="es" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="output.css" rel="stylesheet">
    <title>Dashboard Temperaturas</title>
    <link rel="icon" href="images/icono.png" sizes="32x32" type="image/png">
    <script src="src/jquery-3.7.1.min.js"></script>
    <script src="src/apexcharts.min.js"></script>
    <style>
        body {
            background-color: #0f172a;
            color: #f8fafc;
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(15px);
        }

        .delay-1 {
            animation-delay: 0.1s;
        }

        .delay-2 {
            animation-delay: 0.2s;
        }

        .delay-3 {
            animation-delay: 0.3s;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="min-h-screen bg-slate-900 text-slate-200 font-sans selection:bg-cyan-500 selection:text-slate-900 pb-12 overflow-x-hidden">
    <nav class="sticky top-0 z-50 bg-slate-900/80 backdrop-blur-md border-b border-slate-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="shrink-0 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-linear-to-br from-cyan-400 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-cyan-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a6 6 0 1 0 5 0z" />
                        </svg>
                    </div>
                    <a href="inicio.php" class="text-xl font-bold text-white tracking-wide">
                        Room<span class="text-cyan-400">Temperature</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 fade-in-up delay-1">
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6 relative overflow-hidden group hover:border-slate-600 transition-colors">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all"></div>
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-slate-900/50 rounded-xl border border-slate-700 text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-400">Volumen Procesado</p>
                        <h3 class="text-3xl font-extrabold text-white mt-1"><?= $total_registros ?> <span class="text-base font-medium text-slate-500">registros</span></h3>
                    </div>
                </div>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6 relative overflow-hidden group hover:border-slate-600 transition-colors">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-cyan-500/10 rounded-full blur-2xl group-hover:bg-cyan-500/20 transition-all"></div>
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-slate-900/50 rounded-xl border border-slate-700 text-cyan-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a6 6 0 1 0 5 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-400">Promedio Global Histórico</p>
                        <h3 class="text-3xl font-extrabold text-white mt-1"><?= $promedio_total ?> <span class="text-base font-medium text-slate-500">°C</span></h3>
                    </div>
                </div>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6 relative overflow-hidden group hover:border-slate-600 transition-colors">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-500/10 rounded-full blur-2xl group-hover:bg-rose-500/20 transition-all"></div>
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-slate-900/50 rounded-xl border border-slate-700 text-rose-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-400">Pico de Temperatura (Max)</p>
                        <h3 class="text-3xl font-extrabold text-white mt-1"><?= $max_temp ?> <span class="text-base font-medium text-slate-500">°C</span></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-slate-800/80 backdrop-blur-lg border border-slate-700 rounded-3xl p-6 md:p-8 shadow-2xl fade-in-up delay-2">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-white">Variacion de temperatura a lo largo del tiempo</h2>
                </div>
                <div class="flex items-center bg-slate-900/50 rounded-lg p-1 border border-slate-700">
                    <button id="btnRealtime" class="px-3 py-1.5 text-xs font-medium text-white bg-cyan-600 rounded-md shadow-sm transition-colors mr-1">Tiempo Real</button>
                    <button id="btnHistoric" class="px-3 py-1.5 text-xs font-medium text-slate-400 hover:text-white transition-colors rounded-md">Histórico</button>
                </div>
            </div>
            <div id="historicControls" class="hidden flex flex-wrap items-center gap-3 mb-5">
                <div class="flex items-center gap-2">
                    <label for="dateFrom" class="text-xs text-slate-400 font-medium">Desde</label>
                    <input type="date" id="dateFrom"
                        class="bg-slate-900/60 border border-slate-700 text-slate-200 text-xs rounded-lg px-3 py-1.5 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition-colors">
                </div>
                <div class="flex items-center gap-2">
                    <label for="dateTo" class="text-xs text-slate-400 font-medium">Hasta</label>
                    <input type="date" id="dateTo"
                        class="bg-slate-900/60 border border-slate-700 text-slate-200 text-xs rounded-lg px-3 py-1.5 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition-colors">
                </div>
                <button id="btnFilterHistoric"
                    class="px-4 py-1.5 text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 rounded-lg shadow transition-colors">
                    Buscar
                </button>
                <span id="granularityBadge"
                    class="ml-auto text-[11px] font-medium px-2.5 py-1 rounded-full bg-slate-700/60 text-slate-300 border border-slate-600">
                </span>
            </div>
            <div class="relative w-full h-[500px]">
                <div id="mainChart" class="w-full h-full opacity-0 transition-opacity duration-500"></div>
                <div id="emptyState" class="absolute inset-0 hidden flex-col items-center justify-center text-slate-500">
                    <svg class="w-16 h-16 mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="text-lg font-medium text-slate-400">Aún no hay suficientes datos registrados para trazar un gráfico.</p>
                </div>
                <div id="loadingState" class="absolute inset-0 flex flex-col items-center justify-center text-slate-500">
                    <svg class="animate-spin h-10 w-10 text-cyan-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-lg font-medium text-slate-400">Cargando datos...</p>
                </div>
            </div>


        </div>
    </main>
    <script src="src/chart.js"></script>
</body>

</html>