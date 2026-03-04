<?php ob_start();
session_start();
include __DIR__ . '/src/data.php';

function getTempColorClass($temp) {
    if ($temp === null || $temp === '') return 'text-slate-400';
    $t = (float)$temp;
    if ($t < 18) return 'text-blue-400';
    if ($t < 22) return 'text-cyan-400';
    if ($t < 26) return 'text-emerald-400';
    if ($t < 30) return 'text-amber-400';
    return 'text-rose-400';
}

function getTempBadgeClass($temp) {
    if ($temp === null || $temp === '') return 'bg-slate-700/50 text-slate-400 border-slate-600/50';
    $t = (float)$temp;
    if ($t < 18) return 'bg-blue-500/15 text-blue-300 border-blue-500/30';
    if ($t < 22) return 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30';
    if ($t < 26) return 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30';
    if ($t < 30) return 'bg-amber-500/15 text-amber-300 border-amber-500/30';
    return 'bg-rose-500/15 text-rose-300 border-rose-500/30';
}
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
</head>

<body class="min-h-screen bg-slate-900 text-slate-200 font-sans selection:bg-cyan-500/30 selection:text-white overflow-x-hidden">

    <div class="fixed inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
        <div class="absolute -top-60 -right-40 w-[500px] h-[500px] rounded-full bg-cyan-500/4 blur-3xl"></div>
        <div class="absolute top-1/2 -left-40 w-96 h-96 rounded-full bg-blue-600/5 blur-3xl"></div>
        <div class="absolute bottom-20 right-1/3 w-80 h-80 rounded-full bg-violet-600/4 blur-3xl"></div>
    </div>

    <nav class="sticky top-0 z-50 bg-slate-900/85 backdrop-blur-xl border-b border-slate-800/80 shadow-lg shadow-slate-900/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <div class="shrink-0 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-linear-to-br from-cyan-400 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-cyan-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a6 6 0 1 0 5 0z" />
                        </svg>
                    </div>
                    <div>
                        <a href="inicio.php" class="text-xl font-bold text-white tracking-wide leading-none">
                            Room<span class="text-cyan-400">Temperature</span>
                        </a>
                        <p class="text-[10px] text-slate-500 mt-0.5 leading-none">Monitor de sensores</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    <div id="liveBadge" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 text-[11px] font-bold uppercase tracking-wide transition-all duration-500">
                        <span id="liveDot" class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                        <span id="liveText" class="hidden sm:inline">DATOS EN VIVO</span>
                    </div>
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-800/60 border border-slate-700/50">
                        <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span id="clockDisplay" class="text-sm font-mono tabular-nums text-slate-300">--:--:--</span>
                    </div>
                </div>

            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative">
        <div class="flex overflow-x-auto gap-4 mb-8 pb-4 fade-in-up delay-1 custom-scrollbar snap-x snap-mandatory">
            <div class="min-w-[200px] shrink-0 snap-start">
                <div class="h-full bg-slate-800/50 backdrop-blur-md border border-slate-700/50 rounded-2xl p-5 relative overflow-hidden group hover:border-blue-500/40 hover:glow-blue transition-all duration-300 shadow-lg">
                    <div class="absolute inset-0 bg-linear-to-br from-blue-600/6 to-transparent pointer-events-none rounded-2xl"></div>
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/8 rounded-full blur-2xl group-hover:bg-blue-500/15 transition-all duration-500"></div>

                    <div class="flex items-center gap-2.5 mb-3">
                        <div class="p-2 bg-blue-500/10 rounded-lg text-blue-400 group-hover:scale-110 transition-transform duration-300 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Volumen Total</p>
                    </div>

                    <h3 class="text-3xl font-black text-white tabular-nums leading-none mb-1">
                        <span id="totalRegistros"><?= $total_registros ?></span>
                    </h3>
                    <p class="text-xs text-slate-500">registros almacenados</p>

                    <div class="mt-3 pt-3 border-t border-slate-700/40 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                        <span class="text-[10px] text-blue-400/80">Base de datos activa</span>
                    </div>
                </div>
            </div>
            <?php foreach ($location_stats as $stat): ?>
                <div class="min-w-[280px] md:min-w-[310px] shrink-0 snap-start">
                    <div class="h-full bg-slate-800/50 backdrop-blur-md border border-slate-700/50 rounded-2xl p-5 relative overflow-hidden group hover:border-cyan-500/40 hover:glow-cyan transition-all duration-300 shadow-lg">
                        <div class="absolute inset-0 bg-linear-to-br from-cyan-500/4 to-transparent pointer-events-none rounded-2xl"></div>
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-cyan-500/5 rounded-full blur-2xl group-hover:bg-cyan-500/12 transition-all duration-500"></div>
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="p-2 bg-cyan-500/10 rounded-lg text-cyan-400 shrink-0 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[9px] text-slate-500 uppercase tracking-wider font-bold mb-0.5">Sensor</p>
                                    <h3 class="text-sm font-bold text-white truncate leading-tight"><?= htmlspecialchars($stat['NombreLugar']) ?></h3>
                                </div>
                            </div>
                            <div class="shrink-0 px-2.5 py-1 rounded-lg border text-sm font-bold <?= getTempBadgeClass($stat['Promedio']) ?>">
                                <?= $stat['Promedio'] !== null ? $stat['Promedio'] : '--' ?>°C
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-1.5 pt-3 border-t border-slate-700/40">
                            <div class="text-center py-2 px-1 rounded-lg bg-slate-900/30">
                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1">Mín</p>
                                <p class="text-sm font-bold <?= getTempColorClass($stat['MinTemp']) ?>">
                                    <?= $stat['MinTemp'] !== null ? $stat['MinTemp'] : '--' ?><span class="text-[9px] text-slate-600 font-normal">°C</span>
                                </p>
                            </div>
                            <div class="text-center py-2 px-1 rounded-lg bg-slate-900/30">
                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1">Prom</p>
                                <p class="text-sm font-bold text-white">
                                    <?= $stat['Promedio'] !== null ? $stat['Promedio'] : '--' ?><span class="text-[9px] text-slate-600 font-normal">°C</span>
                                </p>
                            </div>
                            <div class="text-center py-2 px-1 rounded-lg bg-slate-900/30">
                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1">Máx</p>
                                <p class="text-sm font-bold text-rose-400">
                                    <?= $stat['MaxTemp'] !== null ? $stat['MaxTemp'] : '--' ?><span class="text-[9px] text-slate-600 font-normal">°C</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="bg-slate-800/60 backdrop-blur-xl border border-slate-700/60 rounded-3xl p-6 md:p-8 shadow-2xl shadow-slate-900/60 fade-in-up delay-2 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-px bg-linear-to-r from-transparent via-cyan-500/40 to-transparent"></div>
            <div class="absolute inset-0 bg-linear-to-br from-cyan-500/2 via-transparent to-blue-500/2 pointer-events-none rounded-3xl"></div>
            <div class="flex flex-wrap items-start justify-between gap-4 mb-6 relative">
                <div>
                    <h2 class="text-lg font-bold text-white">Variación de Temperatura</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Monitoreo continuo por sensores</p>
                    <p id="lastDataTime" class="hidden mt-1.5 text-[11px] text-slate-600">
                        Último dato: <span id="lastDataTimeValue" class="text-cyan-400/80 font-mono font-medium ml-0.5"></span>
                    </p>
                </div>
                <div class="flex items-center bg-slate-900/60 rounded-xl p-1 border border-slate-700/60 gap-1 shrink-0">
                    <button id="btnRealtime" class="flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg transition-all duration-200 text-white bg-cyan-600 shadow-sm shadow-cyan-900/50">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Tiempo Real
                    </button>
                    <button id="btnHistoric" class="flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg transition-all duration-200 text-slate-400 hover:text-slate-200 hover:bg-slate-800/50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Histórico
                    </button>
                </div>
            </div>
            <div id="historicControls" class="hidden mb-6">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <span class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider mr-1">Rango:</span>
                    <button class="preset-btn px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-600/50 bg-slate-700/40 text-slate-300 hover:bg-cyan-600/20 hover:text-cyan-300 hover:border-cyan-500/30 transition-all duration-200" data-days="0">Hoy</button>
                    <button class="preset-btn px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-600/50 bg-slate-700/40 text-slate-300 hover:bg-cyan-600/20 hover:text-cyan-300 hover:border-cyan-500/30 transition-all duration-200" data-days="7">7 días</button>
                    <button class="preset-btn px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-600/50 bg-slate-700/40 text-slate-300 hover:bg-cyan-600/20 hover:text-cyan-300 hover:border-cyan-500/30 transition-all duration-200" data-days="30">30 días</button>
                    <button class="preset-btn px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-600/50 bg-slate-700/40 text-slate-300 hover:bg-cyan-600/20 hover:text-cyan-300 hover:border-cyan-500/30 transition-all duration-200" data-days="90">90 días</button>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2">
                        <label for="dateFrom" class="text-xs text-slate-400 font-medium whitespace-nowrap">Desde</label>
                        <input type="date" id="dateFrom"
                            class="bg-slate-900/60 border border-slate-700/80 text-slate-200 text-xs rounded-lg px-3 py-2 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50 outline-none transition-all duration-200 cursor-pointer hover:border-slate-600">
                    </div>
                    <div class="flex items-center gap-2">
                        <label for="dateTo" class="text-xs text-slate-400 font-medium whitespace-nowrap">Hasta</label>
                        <input type="date" id="dateTo"
                            class="bg-slate-900/60 border border-slate-700/80 text-slate-200 text-xs rounded-lg px-3 py-2 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50 outline-none transition-all duration-200 cursor-pointer hover:border-slate-600">
                    </div>
                    <button id="btnFilterHistoric"
                        class="flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 rounded-lg shadow transition-all duration-200 hover:shadow-cyan-500/20">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Buscar
                    </button>
                    <span id="granularityBadge"
                        class="hidden text-[11px] font-semibold px-3 py-1.5 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20">
                    </span>
                </div>
            </div>
            <div class="relative w-full h-[500px]">
                <div id="mainChart" class="w-full h-full opacity-0 transition-opacity duration-500"></div>
                <div id="emptyState" class="absolute inset-0 hidden flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 rounded-2xl bg-slate-700/30 flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <p class="text-base font-semibold text-slate-400 mb-1">Sin datos suficientes</p>
                    <p class="text-sm text-slate-600">No hay registros para mostrar en este rango</p>
                </div>
                <div id="loadingState" class="absolute inset-0 flex flex-col gap-3 p-1 pt-3">
                    <div class="flex gap-4 flex-1 min-h-0">
                        <div class="flex flex-col justify-between py-4 w-14 shrink-0">
                            <?php foreach ([85, 68, 80, 58, 72] as $w): ?>
                                <div class="h-2.5 rounded-full skeleton-shimmer" style="width:<?= $w ?>%"></div>
                            <?php endforeach; ?>
                        </div>
                        <div class="flex-1 rounded-xl skeleton-shimmer opacity-40"></div>
                    </div>
                    <div class="flex gap-5 pl-18 pb-1 shrink-0">
                        <?php foreach ([56, 48, 64, 40, 56, 44, 60] as $w): ?>
                            <div class="h-2 rounded-full skeleton-shimmer" style="width:<?= $w ?>px"></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="flex gap-5 justify-start pl-4 pb-1 shrink-0">
                        <?php foreach ([60, 80, 70] as $lw): ?>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded-full skeleton-shimmer"></div>
                                <div class="h-2.5 rounded-full skeleton-shimmer" style="width:<?= $lw ?>px"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="src/chart.js"></script>
</body>

</html>
