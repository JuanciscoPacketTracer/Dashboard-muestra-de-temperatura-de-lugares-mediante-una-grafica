$(document).ready(function () {
    let mode = 'realtime';
    let pollInterval = null;
    let chart = null;
    let latestTime = null;
    let currentSeriesData = [];
    let isHovering = false;

    var options = {
        series: [],
        chart: {
            type: 'area',
            height: 500,
            background: 'transparent',
            toolbar: {
                show: true,
                tools: { download: true, selection: true, zoom: true, pan: false, reset: true },
                autoSelected: 'zoom'
            },
            animations: {
                enabled: true,
                easing: 'linear',
                dynamicAnimation: { enabled: true, speed: 4500 }
            },
            fontFamily: 'inherit',
            events: {
                mouseMove: function () { isHovering = true; },
                mouseLeave: function () { isHovering = false; }
            }
        },
        theme: { mode: 'dark', palette: 'palette2' },
        colors: ['#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#f43f5e', '#6366f1', '#14b8a6', '#f97316'],
        stroke: { curve: 'smooth', width: 4 },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.45, opacityTo: 0.05, stops: [20, 100] }
        },
        dataLabels: { enabled: true },
        markers: { size: 0, hover: { size: 6 } },
        xaxis: {
            type: 'datetime',
            labels: { style: { colors: '#94a3b8' }, datetimeUTC: false },
            axisBorder: { show: false }, axisTicks: { show: false }, tooltip: { enabled: false }
        },
        yaxis: {
            labels: { style: { colors: '#94a3b8' }, formatter: function (value) { return value.toFixed(1) + " °C"; } }
        },
        grid: {
            borderColor: '#334155', strokeDashArray: 4,
            xaxis: { lines: { show: true } }, yaxis: { lines: { show: true } },
            padding: { top: 0, right: 0, bottom: 0, left: 10 }
        },
        legend: { position: 'bottom', horizontalAlign: 'left', labels: { colors: '#cbd5e1' }, fontFamily: 'Times New Roman, serif', markers: { radius: 24 } },
        tooltip: { theme: 'dark', x: { format: 'HH:mm dd MMM yyyy' }, y: { formatter: function (val) { return val + " °C" } } }
    };

    function initChart() {
        if (!chart) {
            chart = new ApexCharts(document.querySelector("#mainChart"), options);
            chart.render();
        }
    }

    function showLoading() {
        $('#loadingState').removeClass('hidden');
        $('#emptyState').addClass('hidden');
        $('#mainChart').addClass('opacity-0');
    }

    function hideLoading(hasData) {
        $('#loadingState').addClass('hidden');
        if (hasData) {
            $('#emptyState').addClass('hidden');
            $('#mainChart').removeClass('opacity-0');
        } else {
            $('#emptyState').removeClass('hidden');
            $('#mainChart').addClass('opacity-0');
        }
    }

    const granularityLabels = {
        minute: 'Promedio por minuto',
        hour: 'Promedio por hora',
        day: 'Promedio por día'
    };

    function toDateStr(date) {
        return date.toISOString().split('T')[0];
    }

    function loadRealtimeData() {
        if (pollInterval) clearInterval(pollInterval);
        latestTime = null;
        showLoading();
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
                        hideLoading(true);

                        pollInterval = setInterval(pollNewData, 1000);
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
                    }
                }
            }
        });
    }

    function loadHistoricData(fromStr, toStr) {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
        showLoading();

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
                        chart.updateOptions({ tooltip: { x: { format: 'HH:mm dd MMM yyyy' } } });
                        hideLoading(true);

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
        $(this).removeClass('text-slate-400 hover:text-white').addClass('text-white bg-cyan-600');
        $('#btnHistoric').removeClass('text-white bg-cyan-600').addClass('text-slate-400 hover:text-white');
        $('#historicControls').addClass('hidden');
        loadRealtimeData();
    });

    $('#btnHistoric').on('click', function () {
        if (mode === 'historic') return;
        mode = 'historic';
        $(this).removeClass('text-slate-400 hover:text-white').addClass('text-white bg-cyan-600');
        $('#btnRealtime').removeClass('text-white bg-cyan-600').addClass('text-slate-400 hover:text-white');
        $('#historicControls').removeClass('hidden');
        loadHistoricData();
    });

    $('#btnFilterHistoric').on('click', function () {
        let fromVal = $('#dateFrom').val();
        let toVal = $('#dateTo').val();
        if (fromVal && toVal) {
            loadHistoricData(fromVal, toVal);
        }
    });

    loadRealtimeData();
});
