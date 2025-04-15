@extends('layouts.app')

@section('header')
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Dashboard Admin</h1>
@endsection

@section('content')
<div class="container px-4 py-8 mx-auto">
    <!-- Bar Chart: Penjualan Harian -->
    <div class="p-6 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-gray-100">Penjualan Harian (30 Hari Terakhir)</h2>
        <div class="chart-container" style="position: relative; height: 40vh; width: 100%">
            <canvas id="dailySalesChart"></canvas>
        </div>
    </div>

    <!-- Doughnut Chart: Persentase Penjualan Produk -->
    <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-gray-100">Persentase Penjualan Produk</h2>
        <div class="chart-container" style="position: relative; height: 40vh; width: 100%">
            <canvas id="productSalesChart"></canvas>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Deteksi mode tema (light/dark)
    function isDarkMode() {
        return document.documentElement.classList.contains('dark') ||
               window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    // Fungsi untuk mendapatkan warna berdasarkan tema
    function getThemeColors() {
        const darkMode = isDarkMode();
        return {
            gridColor: darkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)',
            textColor: darkMode ? 'rgba(255, 255, 255, 0.7)' : 'rgba(0, 0, 0, 0.7)',
            legendColor: darkMode ? 'rgba(255, 255, 255, 0.7)' : 'rgba(0, 0, 0, 0.7)'
        };
    }

    // Fungsi untuk mengatur ukuran chart
    function resizeCharts() {
        const chartContainers = document.querySelectorAll('.chart-container');
        chartContainers.forEach(container => {
            const canvas = container.querySelector('canvas');
            if (canvas) {
                canvas.style.width = '100%';
                canvas.style.height = '100%';
            }
        });
    }

    // Mendapatkan warna tema
    const themeColors = getThemeColors();

    // Bar Chart: Penjualan Harian
    const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
    const dailySalesChart = new Chart(dailySalesCtx, {
        type: 'bar',
        data: {
            labels: @json($dailySalesLabels),
            datasets: [{
                label: 'Total Penjualan',
                data: @json($dailySalesData),
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: themeColors.gridColor
                    },
                    ticks: {
                        color: themeColors.textColor
                    }
                },
                x: {
                    grid: {
                        color: themeColors.gridColor
                    },
                    ticks: {
                        color: themeColors.textColor
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: themeColors.legendColor
                    }
                }
            }
        }
    });

    // Doughnut Chart: Persentase Penjualan Produk
    const productSalesCtx = document.getElementById('productSalesChart').getContext('2d');
    const productSalesChart = new Chart(productSalesCtx, {
        type: 'doughnut',
        data: {
            labels: @json($productSalesLabels),
            datasets: [{
                label: 'Total Terjual',
                data: @json($productSalesData),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: themeColors.legendColor
                    }
                }
            }
        }
    });

    // Mendeteksi perubahan tema
    const darkModeObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                // Mendapatkan warna tema baru
                const newThemeColors = getThemeColors();

                // Update warna pada chart
                [dailySalesChart, productSalesChart].forEach(chart => {
                    if (chart.options.scales) {
                        // Update untuk chart dengan scales (bar chart)
                        if (chart.options.scales.y) {
                            chart.options.scales.y.grid.color = newThemeColors.gridColor;
                            chart.options.scales.y.ticks.color = newThemeColors.textColor;
                        }
                        if (chart.options.scales.x) {
                            chart.options.scales.x.grid.color = newThemeColors.gridColor;
                            chart.options.scales.x.ticks.color = newThemeColors.textColor;
                        }
                    }

                    // Update legend untuk semua chart
                    chart.options.plugins.legend.labels.color = newThemeColors.legendColor;
                    chart.update();
                });
            }
        });
    });

    // Observe perubahan pada class di html element (untuk deteksi perubahan tema)
    darkModeObserver.observe(document.documentElement, { attributes: true });

    // Panggil fungsi resizeCharts saat window di-resize
    window.addEventListener('resize', resizeCharts);

    // Panggil fungsi resizeCharts saat halaman pertama kali dimuat
    resizeCharts();
</script>
@endsection
