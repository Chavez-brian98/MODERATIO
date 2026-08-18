import ApexCharts from 'apexcharts';

const data = window.dashboardChartData ?? {
    salesTrend: { categories: [], series: [] },
    paymentMethods: {},
    topProducts: [],
};

const fontFamily = getComputedStyle(document.body).fontFamily || 'inherit';

function isDark() {
    return document.documentElement.classList.contains('dark');
}

function color(shade) {
    return getComputedStyle(document.documentElement)
        .getPropertyValue(`--color-brand-${shade}`)
        .trim();
}

function gridColor() {
    return isDark() ? '#262626' : color('100');
}

function foreColor() {
    return isDark() ? '#a3a3a3' : '#737373';
}

function legendColor() {
    return isDark() ? '#d4d4d4' : '#404040';
}

function strokeColor() {
    return isDark() ? '#171717' : '#ffffff';
}

function render(id, options) {
    const element = document.getElementById(id);

    if (element) {
        new ApexCharts(element, options).render();
    }
}

const baseOptions = {
    chart: {
        fontFamily,
        foreColor: foreColor(),
        toolbar: { show: false },
        zoom: { enabled: false },
    },
    dataLabels: { enabled: false },
    grid: { borderColor: gridColor() },
};

const chartHeights = [
    { breakpoint: 1024, options: { chart: { height: 280 } } },
    { breakpoint: 640, options: { chart: { height: 260 } } },
    { breakpoint: 480, options: { chart: { height: 240 } } },
];

render('sales-trend-chart', {
    ...baseOptions,
    chart: { ...baseOptions.chart, type: 'area', height: 320 },
    series: [{ name: 'Ventas', data: data.salesTrend.series }],
    colors: [color('600')],
    stroke: { curve: 'smooth', width: 2 },
    fill: {
        type: 'gradient',
        gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90] },
    },
    xaxis: { categories: data.salesTrend.categories },
    yaxis: {
        labels: { formatter: (value) => `$${Number(value).toFixed(0)}` },
    },
    tooltip: { y: { formatter: (value) => `$${Number(value).toFixed(2)}` } },
    responsive: [
        ...chartHeights,
        {
            breakpoint: 640,
            options: {
                xaxis: {
                    labels: { rotate: -45, hideOverlappingLabels: true, style: { fontSize: '10px' } },
                },
            },
        },
        {
            breakpoint: 480,
            options: {
                chart: { height: 240 },
                xaxis: {
                    labels: { rotate: -45, hideOverlappingLabels: true, style: { fontSize: '9px' } },
                },
            },
        },
    ],
});

render('payment-methods-chart', {
    ...baseOptions,
    chart: { ...baseOptions.chart, type: 'donut', height: 320 },
    series: Object.values(data.paymentMethods),
    labels: Object.keys(data.paymentMethods),
    colors: [color('500'), color('600'), color('700')],
    stroke: { colors: [strokeColor()] },
    legend: {
        position: 'bottom',
        labels: { colors: legendColor() },
    },
    tooltip: { y: { formatter: (value) => `$${Number(value).toFixed(2)}` } },
    responsive: [
        {
            breakpoint: 1024,
            options: {
                chart: { height: 280 },
                legend: { position: 'bottom', fontSize: '12px' },
            },
        },
        {
            breakpoint: 640,
            options: {
                chart: { height: 260 },
                legend: { position: 'bottom', fontSize: '11px' },
            },
        },
        {
            breakpoint: 480,
            options: {
                chart: { height: 240 },
                legend: { position: 'bottom', fontSize: '10px' },
            },
        },
    ],
});

render('top-products-chart', {
    ...baseOptions,
    chart: { ...baseOptions.chart, type: 'bar', height: 320 },
    series: [
        {
            name: 'Unidades vendidas',
            data: data.topProducts.map((item) => item.quantity),
        },
    ],
    colors: [color('500')],
    plotOptions: {
        bar: { horizontal: true, borderRadius: 4, barHeight: '55%' },
    },
    xaxis: {
        categories: data.topProducts.map((item) => item.name),
        labels: { trim: true, maxHeight: 90 },
    },
    yaxis: {
        labels: { style: { fontSize: '13px' } },
    },
    dataLabels: { enabled: true, formatter: (value) => String(value) },
    responsive: [
        {
            breakpoint: 1024,
            options: {
                chart: { height: 280 },
                yaxis: { labels: { style: { fontSize: '12px' } } },
            },
        },
        {
            breakpoint: 640,
            options: {
                chart: { height: 260 },
                yaxis: { labels: { style: { fontSize: '11px' } } },
            },
        },
        {
            breakpoint: 480,
            options: {
                chart: { height: 240 },
                yaxis: { labels: { style: { fontSize: '10px' } } },
            },
        },
    ],
});
