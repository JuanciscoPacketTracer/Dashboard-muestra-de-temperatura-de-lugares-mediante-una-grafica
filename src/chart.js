$(document).ready(function () {
    let mode = 'realtime';
    let pollInterval = null;
    let chart = null;
    let latestTime = null;
    let currentSeriesData = [];
    let isHovering = false;

    function startClock() {
        function tick() {
            const now = new Date();
            $('#clockDisplay').text(
                now.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false })
            );
        }
        tick();
        setInterval(tick, 1000);
    }
    startClock();

    function setLiveBadge(isLive) {
        const badge = $('#liveBadge');
        const dot = $('#liveDot');
        const text = $('#liveText');
        if (isLive) {
            badge.removeClass('bg-amber-500/10 border-amber-500/25 text-amber-400')
                 .addClass('bg-emerald-500/10 border-emerald-500/25 text-emerald-400');
            dot.removeClass('bg-amber-400').addClass('bg-emerald-400 animate-pulse');
            text.text('EN VIVO');
        } else {
            badge.removeClass('bg-emerald-500/10 border-emerald-500/25 text-emerald-400')
                 .addClass('bg-amber-500/10 border-amber-500/25 text-amber-400');
            dot.removeClass('bg-emerald-400 animate-pulse').addClass('bg-amber-400');
            text.text('HISTÓRICO');
        }
    }

    function updateLastUpdated(latestDataTime) {
        const now = new Date();
        $('#lastUpdated').text(
            now.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false })
        );
        if (latestDataTime) {
            const d = new Date(latestDataTime.replace(' ', 'T'));
            if (!isNaN(d.getTime())) {
                $('#lastDataTimeValue').text(
                    d.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false })
                );
                $('#lastDataTime').removeClass('hidden');
            }
        }
    }
    var options = {
        series: [],
        chart: {
            type: 'area',
            height: 500,
            background: 'transparent',
            toolbar: {
                show: true,
                tools: { download: true, selection: true, zoom: true, pan: true, reset: true },
                autoSelected: 'zoom'
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 600,
                dynamicAnimation: { enabled: true, speed: 1500 }
            },
            fontFamily: 'inherit',
            events: {
                mouseMove: function () { isHovering = true; },
                mouseLeave: function () { isHovering = false; }
            }
        },
        theme: { mode: 'dark' },
        colors: ['#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#f43f5e', '#6366f1', '#14b8a6', '#f97316'],
        stroke: { curve: 'smooth', width: 2.5 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                inverseColors: false,
                opacityFrom: 0.25,
                opacityTo: 0.0,
                stops: [0, 90]
            }
        },
        dataLabels: { enabled: true },
        markers: {
            size: 0,
            strokeWidth: 2,
            strokeColors: '#0f172a',
            hover: { size: 6, sizeOffset: 2 }
        },
        xaxis: {
            type: 'datetime',
            labels: {
                style: { colors: '#64748b', fontSize: '11px' },
                datetimeUTC: false
            },
            axisBorder: { show: false },
            axisTicks: { show: false },
            tooltip: { enabled: false },
            crosshairs: {
                show: true,
                stroke: { color: '#334155', width: 1, dashArray: 4 }
            }
        },
        yaxis: {
            labels: {
                style: { colors: '#64748b', fontSize: '11px' },
                formatter: function (value) { return value.toFixed(1) + "°C"; }
            },
            tickAmount: 6
        },
        grid: {
            borderColor: '#1e293b',
            strokeDashArray: 3,
            xaxis: { lines: { show: false } },
            yaxis: { lines: { show: true } },
            padding: { top: 10, right: 10, bottom: 0, left: 10 }
        },
        legend: {
            position: 'bottom',
            horizontalAlign: 'left',
            labels: { colors: '#94a3b8' },
            fontFamily: 'inherit',
            fontSize: '12px',
            markers: { size: 8, shape: 'circle' },
            itemMargin: { horizontal: 12, vertical: 6 }
        },
        tooltip: {
            theme: 'dark',
            x: { format: 'HH:mm:ss dd MMM yyyy' },
            y: { formatter: function (val) { return val.toFixed(1) + " °C"; } },
            style: { fontSize: '12px', fontFamily: 'inherit' }
        }
    };
    function initChart() {
        if (!chart) {
            chart = new ApexCharts(document.querySelector("#mainChart"), options);
            chart.render();
        }
    }

    function showLoading() {
        $('#loadingState').removeClass('hidden').addClass('flex');
        $('#emptyState').removeClass('flex').addClass('hidden');
        $('#mainChart').addClass('opacity-0');
    }

    function hideLoading(hasData) {
        $('#loadingState').removeClass('flex').addClass('hidden');
        if (hasData) {
            $('#emptyState').removeClass('flex').addClass('hidden');
            $('#mainChart').removeClass('opacity-0');
        } else {
            $('#emptyState').removeClass('hidden').addClass('flex');
            $('#mainChart').addClass('opacity-0');
        }
    }

    function toDateStr(date) {
        return date.toISOString().split('T')[0];
    }

    const granularityLabels = {
        minute: 'Promedio por minuto',
        hour: 'Promedio por hora',
        day: 'Promedio por día'
    };

    function setActivePreset($btn) {
        $('.preset-btn').removeClass('preset-btn-active');
        if ($btn) $btn.addClass('preset-btn-active');
    }

    function loadRealtimeData() {
        if (pollInterval) clearInterval(pollInterval);
        latestTime = null;
        showLoading();
        $('#lastDataTime').addClass('hidden');

        $.ajax({
            url: 'api/realtime.php',
            method: 'GET',
            dataType: 'json',
            success: function (res) {
                try {
                    if (res.series && res.series.length > 0 && res.series.some(s => s.data && s.data.length > 0)) {
                        currentSeriesData = res.series.map(s => ({
                            name: s.name,
                            data: s.data.slice(-20)
                        }));
                        initChart();
                        chart.updateSeries(currentSeriesData);
                        chart.updateOptions({ tooltip: { x: { format: 'HH:mm:ss dd MMM yyyy' } } });

                        if (res.latest) latestTime = res.latest;
                        if (res.totalRegistros !== undefined) $('#totalRegistros').text(res.totalRegistros);
                        hideLoading(true);
                        updateLastUpdated(res.latest);

                        pollInterval = setInterval(pollNewData, 10000);
                    } else {
                        hideLoading(false);
                    }
                } catch (err) {
                    hideLoading(false);
                }
            },
            error: function () {
                hideLoading(false);
            }
        });
    }

    function pollNewData() {
        if (!latestTime || mode !== 'realtime' || !chart) return;
        if (isHovering) return;

        $.ajax({
            url: 'api/realtime.php?since=' + encodeURIComponent(latestTime),
            method: 'GET',
            dataType: 'json',
            success: function (res) {
                if (isHovering) return;

                if (res.totalRegistros !== undefined) {
                    $('#totalRegistros').text(res.totalRegistros);
                }

                if (res.series && res.series.length > 0 && res.latest && res.latest > latestTime) {
                    latestTime = res.latest;
                    let hasNewData = false;
                    currentSeriesData.forEach((existingSerie) => {
                        let matchingNew = res.series.find(s => s.name === existingSerie.name);
                        let newData = matchingNew ? matchingNew.data : [];
                        if (newData.length > 0) {
                            existingSerie.data = existingSerie.data.concat(newData).slice(-20);
                            hasNewData = true;
                        }
                    });
                    if (hasNewData) {
                        chart.updateSeries(currentSeriesData);
                        updateLastUpdated(res.latest);
                    }
                }
            }
        });
    }

    // ─── Historic Mode ─────────────────────────────────────────────────
    function loadHistoricData(fromStr, toStr) {
        if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
        showLoading();
        $('#lastDataTime').addClass('hidden');

        if (!fromStr || !toStr) {
            let from = new Date();
            from.setDate(from.getDate() - 7);
            fromStr = toDateStr(from);
            toStr = toDateStr(new Date());
        }

        $('#dateFrom').val(fromStr);
        $('#dateTo').val(toStr);

        $.ajax({
            url: 'api/historic.php?from=' + fromStr + '&to=' + toStr,
            method: 'GET',
            dataType: 'json',
            success: function (res) {
                try {
                    if (res.series && res.series.length > 0 && res.series.some(s => s.data && s.data.length > 0)) {
                        initChart();
                        chart.updateSeries(res.series);
                        chart.updateOptions({ tooltip: { x: { format: 'dd MMM yyyy HH:mm' } } });
                        hideLoading(true);
                        updateLastUpdated(null);

                        if (res.granularity && granularityLabels[res.granularity]) {
                            $('#granularityBadge').text(granularityLabels[res.granularity]).removeClass('hidden');
                        }
                    } else {
                        hideLoading(false);
                        $('#granularityBadge').addClass('hidden');
                    }
                } catch (err) {
                    hideLoading(false);
                }
            },
            error: function () {
                hideLoading(false);
            }
        });
    }

    $('#btnRealtime').on('click', function () {
        if (mode === 'realtime') return;
        mode = 'realtime';
        $(this).removeClass('text-slate-400 hover:text-slate-200 hover:bg-slate-800/50')
               .addClass('text-white bg-cyan-600 shadow-sm shadow-cyan-900/50');
        $('#btnHistoric').removeClass('text-white bg-cyan-600 shadow-sm shadow-cyan-900/50')
                         .addClass('text-slate-400 hover:text-slate-200 hover:bg-slate-800/50');
        $('#historicControls').addClass('hidden');
        $('#granularityBadge').addClass('hidden');
        setLiveBadge(true);
        loadRealtimeData();
    });

    $('#btnHistoric').on('click', function () {
        if (mode === 'historic') return;
        mode = 'historic';
        $(this).removeClass('text-slate-400 hover:text-slate-200 hover:bg-slate-800/50')
               .addClass('text-white bg-cyan-600 shadow-sm shadow-cyan-900/50');
        $('#btnRealtime').removeClass('text-white bg-cyan-600 shadow-sm shadow-cyan-900/50')
                         .addClass('text-slate-400 hover:text-slate-200 hover:bg-slate-800/50');
        $('#historicControls').removeClass('hidden');
        setLiveBadge(false);
        setActivePreset($('.preset-btn[data-days="7"]'));
        loadHistoricData();
    });

    $('.preset-btn').on('click', function () {
        setActivePreset($(this));
        const days = parseInt($(this).data('days'));
        const to = new Date();
        const from = new Date();
        if (days > 0) from.setDate(from.getDate() - days);
        loadHistoricData(toDateStr(from), toDateStr(to));
    });

    $('#btnFilterHistoric').on('click', function () {
        const fromVal = $('#dateFrom').val();
        const toVal = $('#dateTo').val();
        if (fromVal && toVal) {
            setActivePreset(null);
            loadHistoricData(fromVal, toVal);
        }
    });

    setLiveBadge(true);
    loadRealtimeData();
});
