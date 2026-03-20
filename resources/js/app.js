import './bootstrap';

// ─── Swiper (replaces CDN) ───
import Swiper from 'swiper';
import { FreeMode } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/free-mode';
window.Swiper = Swiper;
window.SwiperModules = { FreeMode };

// ─── Trix Editor (replaces CDN) ───
import 'trix';
import 'trix/dist/trix.css';
import Chart from 'chart.js/auto';


const initDashboardChart = () => {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;

    // Destroy existing chart instance if it exists
    const existingChart = Chart.getChart(ctx);
    if (existingChart) {
        existingChart.destroy();
    }

    const labels = JSON.parse(ctx.dataset.labels);
    const data = JSON.parse(ctx.dataset.data);
    const color = ctx.dataset.color;
    const colorRgb = ctx.dataset.colorRgb;
    const currencySymbol = ctx.dataset.currencySymbol || '$';

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales',
                data: data,
                borderColor: color,
                backgroundColor: `rgba(${colorRgb}, 0.1)`,
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: color,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#1e293b',
                    bodyColor: '#64748b',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function (context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += currencySymbol + new Intl.NumberFormat('en-US').format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9',
                        drawBorder: false,
                    },
                    ticks: {
                        color: '#94a3b8',
                        font: {
                            family: "'Inter', sans-serif",
                            size: 11
                        },
                        callback: function (value) {
                            return currencySymbol + value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#94a3b8',
                        font: {
                            family: "'Inter', sans-serif",
                            size: 11
                        }
                    }
                }
            }
        }
    });
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initDashboardChart();
    if (window.initTypeChart) window.initTypeChart();
});

// Initialize on Livewire navigation
document.addEventListener('livewire:navigated', () => {
    initDashboardChart();
    if (window.initTypeChart) window.initTypeChart();
});

window.initTypeChart = () => {
    const ctx = document.getElementById('typeChart');
    if (!ctx) return;

    // Destroy existing chart instance if it exists
    const existingChart = Chart.getChart(ctx);
    if (existingChart) {
        existingChart.destroy();
    }

    const readyCount = parseInt(ctx.dataset.ready) || 0;
    const customCount = parseInt(ctx.dataset.custom) || 0;
    const primaryColor = ctx.dataset.color || '#3b82f6';
    const accentColor = '#f59e0b'; // Amber for contrast

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Ready Cakes', 'Custom Cakes'],
            datasets: [{
                data: [readyCount, customCount],
                backgroundColor: [primaryColor, accentColor],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#64748b',
                        font: {
                            family: "'Inter', sans-serif",
                            size: 12
                        },
                        usePointStyle: true,
                        padding: 20
                    }
                }
            },
            cutout: '75%'
        }
    });
};
