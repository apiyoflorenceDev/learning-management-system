// Fetch total enrolments
fetch('enrolment.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById('totalEnrolments').innerText = data.total;
    });

// Weekly Progress Chart dynamic
fetch('weekly_data.php')
    .then(response => response.json())
    .then(data => {
        const labels = data.map(item => item.date);
        const totals = data.map(item => item.total);

        const ctx = document.getElementById('weeklyProgressChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Enrolments',
                    data: totals,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('active');
    }
    