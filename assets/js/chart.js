
// Données PHP vers JS

// Graphique commandes/ventes
const ctx = document.getElementById('ordersChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: months,
        datasets: [
            {
                label: 'Commandes',
                data: orderData,
                backgroundColor: 'rgba(52, 152, 219, 0.7)',
                borderColor: 'rgba(41, 128, 185, 1)',
                borderWidth: 1
            },
            {
                label: 'Ventes ($)',
                data: salesData,
                backgroundColor: 'rgba(46, 204, 113, 0.7)',
                borderColor: 'rgba(39, 174, 96, 1)',
                borderWidth: 1,
                type: 'line',
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Commandes' }
            },
            y1: {
                beginAtZero: true,
                position: 'right',
                grid: { drawOnChartArea: false },
                title: { display: true, text: 'Ventes (€)' }
            }
        }
    }
});
