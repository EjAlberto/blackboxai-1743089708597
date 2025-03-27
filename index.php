<?php
require_once 'includes/db_connection.php';
require_once 'includes/header.php';

// Get total student count
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();

// Get students per strand
$strandsQuery = $pdo->query("
    SELECT s.name, COUNT(st.id) as count 
    FROM strands s
    LEFT JOIN students st ON s.id = st.strand_id
    GROUP BY s.name
");
$strandsData = $strandsQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Students Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div>
                <h3 class="text-gray-500">Total Students</h3>
                <p class="text-2xl font-semibold"><?= $totalStudents ?></p>
            </div>
        </div>
    </div>

    <!-- Students per Strand Cards -->
    <?php foreach ($strandsData as $strand): ?>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <i class="fas fa-graduation-cap text-2xl"></i>
            </div>
            <div>
                <h3 class="text-gray-500"><?= $strand['name'] ?></h3>
                <p class="text-2xl font-semibold"><?= $strand['count'] ?></p>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Chart Section -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4">Students Distribution</h2>
    <div class="h-80">
        <canvas id="studentsChart"></canvas>
    </div>
</div>

<script>
// Students Chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('studentsChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($strandsData, 'name')) ?>,
            datasets: [{
                label: 'Students per Strand',
                data: <?= json_encode(array_column($strandsData, 'count')) ?>,
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(245, 158, 11, 0.7)',
                    'rgba(244, 63, 94, 0.7)',
                    'rgba(139, 92, 246, 0.7)'
                ],
                borderColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 11, 1)',
                    'rgba(244, 63, 94, 1)',
                    'rgba(139, 92, 246, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>