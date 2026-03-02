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

        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(30, 41, 59, 0.5);
            border-radius: 14px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(56, 189, 248, 0.4);
            border-radius: 14px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(56, 189, 248, 0.8);
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
        <div class="flex overflow-x-auto gap-4 mb-8 pb-4 fade-in-up delay-1 custom-scrollbar snap-x snap-mandatory">
            <div class="min-w-[240px] md:min-w-[280px] shrink-0 snap-start bg-slate-800/40 backdrop-blur-md border border-slate-700/50 rounded-2xl p-4 relative overflow-hidden group hover:border-blue-500/50 transition-all duration-300 shadow-lg">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-all"></div>
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-blue-500/10 rounded-xl text-blue-400 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Volumen Procesado</p>
                        <h3 class="text-xl font-bold text-white tracking-tight mt-0.5">
                            <span id="totalRegistros"><?= $total_registros ?></span>
                            <span class="text-xs font-normal text-slate-500 ml-1">registros</span>
                        </h3>
                    </div>
                </div>
            </div>

            <?php foreach ($location_stats as $stat): ?>
                <div class="min-w-[300px] md:min-w-[340px] shrink-0 snap-start bg-slate-800/40 backdrop-blur-md border border-slate-700/50 rounded-2xl p-4 relative overflow-hidden group hover:border-cyan-500/50 transition-all duration-300 shadow-lg flex flex-col">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-cyan-500/5 rounded-full blur-2xl group-hover:bg-cyan-500/10 transition-all"></div>

                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="p-2 bg-cyan-500/10 rounded-lg text-cyan-400 shrink-0 group-hover:rotate-12 transition-transform duration-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-white truncate leading-tight"><?= htmlspecialchars($stat['NombreLugar']) ?></h3>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-auto pt-2 border-t border-slate-700/30">
                        <div class="flex items-baseline gap-2">
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Prom:</span>
                            <span class="text-base font-bold text-white"><?= $stat['Promedio'] ?? '--' ?><span class="text-[10px] ml-0.5 text-slate-400">°C</span></span>
                        </div>
                        <div class="h-4 w-px bg-slate-700/50"></div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Max:</span>
                            <span class="text-base font-bold text-rose-400"><?= $stat['MaxTemp'] ?? '--' ?><span class="text-[10px] ml-0.5 text-slate-500">°C</span></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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