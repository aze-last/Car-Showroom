import Chart from 'chart.js/auto';

/**
 * Shared line-chart factory for the admin dashboard (Portfolio Velocity,
 * Views Over Time). Registered as an Alpine component so Blade can pass the
 * series via @js(); the canvas lives inside wire:ignore so Livewire's
 * 30s poll morphs never destroy it.
 *
 * config: { labels: string[], data: number[], accent: string,
 *           fillFrom: string, yLabel: string, unitLabel: string }
 */
document.addEventListener('alpine:init', () => {
    window.Alpine.data('adminLineChart', (config) => ({
        chart: null,

        init() {
            const max = Math.max(...config.data, 0);

            this.chart = new Chart(this.$refs.canvas, {
                type: 'line',
                data: {
                    labels: config.labels,
                    datasets: [
                        {
                            label: config.yLabel,
                            data: config.data,
                            borderColor: config.accent,
                            backgroundColor: config.fillFrom,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: config.data.length > 12 ? 0 : 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: config.accent,
                            pointBorderWidth: 2.5,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#18181b', // zinc-900
                            titleColor: '#a1a1aa', // zinc-400
                            bodyColor: '#ffffff',
                            titleFont: { family: "'Hanken Grotesk', sans-serif", size: 10, weight: 'bold' },
                            bodyFont: { family: "'Hanken Grotesk', sans-serif", size: 13, weight: 'bold' },
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: false,
                            callbacks: {
                                label: (context) => `${context.parsed.y.toLocaleString()} ${config.unitLabel}`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: {
                                color: '#a1a1aa', // zinc-400
                                font: { family: "'Hanken Grotesk', sans-serif", size: 10, weight: 'bold' },
                                maxRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 8,
                            },
                        },
                        y: {
                            beginAtZero: true,
                            // Keep modest counts from pegging the top of the chart
                            // or collapsing into a flat-looking line.
                            suggestedMax: Math.max(4, Math.ceil(max * 1.2)),
                            grid: { color: 'rgba(0, 0, 0, 0.04)' },
                            border: { display: false },
                            title: {
                                display: true,
                                text: config.yLabel.toUpperCase(),
                                color: '#a1a1aa',
                                font: { family: "'Hanken Grotesk', sans-serif", size: 9, weight: 'bold' },
                            },
                            ticks: {
                                color: '#a1a1aa',
                                precision: 0, // integer counts only
                                font: { family: "'Hanken Grotesk', sans-serif", size: 10, weight: 'bold' },
                            },
                        },
                    },
                },
            });
        },

        destroy() {
            this.chart?.destroy();
            this.chart = null;
        },
    }));
});
